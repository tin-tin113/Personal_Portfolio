<?php
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';

$pdo = db();

// Clear and update with exact LinkedIn education details
$pdo->exec("DELETE FROM education");

$stmt = $pdo->prepare("
    INSERT INTO education (
        institution, degree_program, start_year, end_year,
        description, achievements, relevant_coursework, display_order
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

// 1. CHMSU
$stmt->execute([
    'Carlos Hilado Memorial State University',
    'Bachelor of Science in Information Systems (BSIS)',
    '2023',
    '2027',
    'Currently in 3rd year standing. Studying information systems, database management, software development, and enterprise technologies.',
    'Grade: 3rd Year Standing | Activities and Societies: Systems, Software, Technology',
    'Web Systems & Technologies, Database Management Systems (MySQL), Systems Analysis & Design, Prompt Engineering & AI Applications',
    1
]);

// 2. Rafael B. Lacson Memorial High School
$stmt->execute([
    'Rafael B. Lacson Memorial High School',
    'ICT, Information Technology',
    '2017',
    '2023',
    'Completed secondary education specialized in Information and Communications Technology.',
    'Activities and Societies: Computer System Servicing (CSS)',
    'Computer System Servicing, Basic Networking, Computer Operations, ICT Fundamentals',
    2
]);

echo "Successfully updated education records with exact LinkedIn information.\n";
