<?php
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';

$pdo = db();

// 1. Update Profile Information
$pdo->prepare("
    UPDATE profile SET 
        full_name = 'Martin Diasanta',
        professional_title = 'AI-Assisted Full-Stack Developer | QA & Cybersecurity Specialist',
        tagline = 'Building robust, secure, and user-centric web systems with PHP, MySQL, and modern QA standards.',
        short_summary = 'Full-Stack Developer, Quality Assurance & Cybersecurity Specialist based in the Philippines.',
        location = 'Philippines',
        availability_status = 'Open to Opportunities',
        linkedin_url = 'https://www.linkedin.com/in/martin-diasanta-4b5200279'
    WHERE id = 1
")->execute();

// 2. Insert or update the verified Cybersecurity Certification
$stmt = $pdo->prepare("SELECT id FROM certifications WHERE title LIKE '%Ethical Hacking%' LIMIT 1");
$stmt->execute();
$existingCert = $stmt->fetch();

if (!$existingCert) {
    $pdo->prepare("
        INSERT INTO certifications (
            title, issuing_organization, issue_date, expiration_date,
            credential_id, credential_url, description, is_featured, is_published, display_order
        ) VALUES (
            'Fundamentals of Ethical Hacking',
            'Cybersecurity Training & Accreditation',
            '2025-05-19',
            NULL,
            'FEH-2025-VERIFIED',
            'https://thecasehq.com',
            'Practical foundation in ethical hacking, vulnerability discovery, network security analysis, and secure web application assessment.',
            1, 1, 1
        )
    ")->execute();
}

// 3. Add Tagumpay National High School if not exists
$stmt = $pdo->prepare("SELECT id FROM education WHERE institution LIKE '%Tagumpay%' LIMIT 1");
$stmt->execute();
$existingEdu = $stmt->fetch();

if (!$existingEdu) {
    $pdo->prepare("
        INSERT INTO education (
            institution, degree_program, start_year, end_year,
            description, achievements, relevant_coursework, display_order
        ) VALUES (
            'Tagumpay National High School',
            'High School Diploma (Secondary Education)',
            '2017',
            '2021',
            'Completed secondary education with active participation in STEM academics, leadership, and athletic programs.',
            'Table Tennis Champion (Intramurals & District Meets)',
            'Information Technology Basics, Mathematics, Physical Science',
            2
        )
    ")->execute();
}

echo "Successfully synchronized Martin Diasanta's discovered background and verified certification.\n";
