<?php
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';

$pdo = db();

// 1. Update Education table to BSIS
$pdo->prepare("
    UPDATE education 
    SET degree_program = 'Bachelor of Science in Information Systems (BSIS)',
        description = 'Currently in 3rd year standing. Focused on enterprise information systems, database design, software engineering, web application development, and IT solutions.'
    WHERE institution LIKE '%Carlos Hilado%'
")->execute();

// 2. Update Profile summary & about_me
$pdo->prepare("
    UPDATE profile SET
        short_summary = 'Information Systems (BSIS) student at CHMSU, Prompt Engineer, and Web Developer based in Talisay, Western Visayas, Philippines. Passionate about AI-assisted web development, database architecture, and digital operations.',
        about_me = 'I am a 3rd-year Bachelor of Science in Information Systems (BSIS) student at Carlos Hilado Memorial State University and a practicing Prompt Engineer & Web Developer based in Talisay, Western Visayas, Philippines.

I specialize in building practical, high-performance web applications using Core PHP 8, MySQL PDO, modern CSS, and vanilla JavaScript, enhanced with generative AI workflows. In addition to full-stack web development and database management, I hold Civil Service Commission (CSC) Professional Eligibility and have completed training in Ethical Hacking & Cybersecurity.

Currently undertaking a remote Artificial Intelligence Internship with Progee, focusing on building and refining intelligent automated workflows.'
    WHERE id = 1
")->execute();

// 3. Update Site Settings
$pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'meta_description'")
    ->execute(['Portfolio of Martin Diasanta - 3rd Year BSIS Student at CHMSU, Prompt Engineer, AI Intern at Progee, and Web Developer based in Talisay, Philippines.']);

echo "Successfully updated degree to BSIS (Bachelor of Science in Information Systems).\n";
