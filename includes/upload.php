<?php
/**
 * Secure File Upload Engine
 * Personal Portfolio & Private Admin CMS
 */

if (!defined('APP_INIT')) {
    require_once dirname(__DIR__) . '/config/app.php';
}

require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

/**
 * Process File Upload securely
 *
 * @param array $file $_FILES['field'] element
 * @param string $category Subfolder inside /uploads (e.g. 'avatars', 'projects', 'certs', 'licenses', 'resume', 'media')
 * @param string $allowedTypeCategory 'image' or 'doc'
 * @param string|null $oldFilePath Existing file path to remove upon successful replacement
 * @return array ['success' => bool, 'filename' => string, 'filepath' => string, 'url' => string, 'error' => string]
 */
function handle_file_upload(array $file, string $category = 'media', string $allowedTypeCategory = 'image', ?string $oldFilePath = null): array {
    // 1. Check basic upload errors
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameter structure.'];
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'No file uploaded.'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Uploaded file exceeds maximum allowed server file size.'];
        default:
            return ['success' => false, 'error' => 'Unknown file upload error. Code: ' . $file['error']];
    }

    // 2. Validate File Size
    $maxBytes = ($allowedTypeCategory === 'doc') ? MAX_DOC_SIZE : MAX_IMAGE_SIZE;
    if ($file['size'] > $maxBytes) {
        $maxMB = $maxBytes / (1024 * 1024);
        return ['success' => false, 'error' => "File size exceeds maximum limit of {$maxMB}MB."];
    }

    // 3. Server-side MIME Type Verification via finfo
    $tmpPath = $file['tmp_name'];
    if (!is_uploaded_file($tmpPath)) {
        return ['success' => false, 'error' => 'File upload security check failed. Temporary file invalid.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    $allowedMimes = ($allowedTypeCategory === 'doc') ? ALLOWED_DOC_MIMES : ALLOWED_IMAGE_MIMES;

    if (!array_key_exists($mimeType, $allowedMimes)) {
        return [
            'success' => false,
            'error' => "Invalid file format detected ({$mimeType}). Only JPG, PNG, WEBP, and PDF files are permitted."
        ];
    }

    // Determine extension from MIME whitelist (do not trust user extension)
    $ext = $allowedMimes[$mimeType];

    // Additional Image integrity check if expected image
    if ($allowedTypeCategory === 'image' && str_starts_with($mimeType, 'image/')) {
        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo === false) {
            return ['success' => false, 'error' => 'Uploaded file claims to be an image but image verification failed.'];
        }
    }

    // 4. Sanitize Destination Folder & Generate Safe Filename
    $categoryDir = sanitize_input($category);
    $targetDir = UPLOADS_PATH . '/' . $categoryDir;

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create target upload directory.'];
        }
    }

    // Cryptographically random target filename
    $randomName = bin2hex(random_bytes(16)) . '.' . $ext;
    $targetPath = $targetDir . '/' . $randomName;
    $relativePath = 'uploads/' . $categoryDir . '/' . $randomName;

    // 5. Move File to Target Location
    if (!move_uploaded_file($tmpPath, $targetPath)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file to destination folder. Check permissions.'];
    }

    // 6. Record Metadata in `media` database table
    try {
        $stmt = db()->prepare("
            INSERT INTO media (original_name, stored_filename, file_path, file_type, file_size, mime_type, associated_module)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            sanitize_input($file['name'] ?? $randomName),
            $randomName,
            $relativePath,
            $allowedTypeCategory,
            $file['size'],
            $mimeType,
            $categoryDir
        ]);
    } catch (Exception $e) {
        error_log("Failed to insert media metadata: " . $e->getMessage());
    }

    // 7. Delete old file if replacing
    if (!empty($oldFilePath)) {
        delete_uploaded_file($oldFilePath);
    }

    return [
        'success' => true,
        'filename' => $randomName,
        'filepath' => $relativePath,
        'url' => BASE_URL . '/' . $relativePath,
        'error' => null
    ];
}

/**
 * Safely delete an uploaded file from disk and media DB table
 */
function delete_uploaded_file(?string $relativePath): bool {
    if (empty($relativePath)) {
        return false;
    }

    // Prevent directory traversal attacks
    $cleanPath = str_replace(['..', '\\'], ['', '/'], $relativePath);
    $fullPath = ROOT_PATH . '/' . ltrim($cleanPath, '/');

    // Ensure target path is strictly within UPLOADS_PATH
    $realUploadsDir = realpath(UPLOADS_PATH);
    $realTargetPath = realpath($fullPath);

    if ($realTargetPath !== false && $realUploadsDir !== false && str_starts_with($realTargetPath, $realUploadsDir)) {
        if (file_exists($realTargetPath)) {
            @unlink($realTargetPath);
        }

        // Delete from media registry if exists
        try {
            $stmt = db()->prepare("DELETE FROM media WHERE file_path = ?");
            $stmt->execute([$cleanPath]);
        } catch (Exception $e) {
            error_log("Failed to remove media record: " . $e->getMessage());
        }

        return true;
    }

    return false;
}
