<?php
/**
 * Synchronize Martin Diasanta's Complete Real Profile, Experience, Education, Licenses & Certifications
 */
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';

$pdo = db();

echo "Starting database sync for Martin Diasanta...\n";

// 1. Profile
$profileStmt = $pdo->prepare("
    UPDATE profile SET
        full_name = 'Martin Diasanta',
        professional_title = 'Aspiring Web Developer | Prompt Engineer | AI & QA Specialist',
        tagline = 'Bridging code and AI to build smarter, reliable digital solutions.',
        short_summary = 'Management Information Systems student at CHMSU, Prompt Engineer, and Web Developer based in Talisay, Western Visayas, Philippines. Passionate about AI-assisted web development, database architecture, and digital operations.',
        about_me = 'I am a 3rd-year Management Information Systems (BS MIS) student at Carlos Hilado Memorial State University and a practicing Prompt Engineer & Web Developer based in Talisay, Western Visayas, Philippines.

I specialize in building practical, high-performance web applications using Core PHP 8, MySQL PDO, modern CSS, and vanilla JavaScript, enhanced with generative AI workflows. In addition to full-stack web development and database management, I hold Civil Service Commission (CSC) Professional Eligibility and have completed training in Ethical Hacking & Cybersecurity.

Currently undertaking a remote Artificial Intelligence Internship with Progee, focusing on building and refining intelligent automated workflows.',
        email = 'martindiasanta.dev@gmail.com',
        phone = '',
        location = 'Talisay, Western Visayas, Philippines',
        availability_status = 'Open to Work (Web Developer, Coder, Virtual Assistant)',
        linkedin_url = 'https://www.linkedin.com/in/martin-diasanta-4b5200279'
    WHERE id = 1
");
$profileStmt->execute();
echo "1. Profile details updated.\n";

// 2. Clear old demo education and insert real records
$pdo->exec("DELETE FROM education");
$eduStmt = $pdo->prepare("
    INSERT INTO education (
        institution, degree_program, start_year, end_year,
        description, achievements, relevant_coursework, display_order
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$eduStmt->execute([
    'Carlos Hilado Memorial State University',
    'Bachelor of Science in Management Information Systems (BS MIS)',
    '2023',
    '2027',
    'Currently in 3rd year standing. Focused on enterprise information systems, database design, software engineering, web application development, and IT management.',
    'Grade: 3rd Year Standing | Active Member: Environment and Computers Society',
    'Web Systems & Technologies, Database Management Systems (MySQL), Systems Analysis & Design, Prompt Engineering & AI Applications',
    1
]);

$eduStmt->execute([
    'Rafael B. Lacson Memorial High School',
    'High School Diploma (Secondary Education)',
    '2017',
    '2023',
    'Completed junior and senior high school secondary education with strong academic focus on information technology, mathematics, and leadership.',
    'Active participation in student organizations and academic programs.',
    'Information & Communications Technology (ICT), Basic Programming, Computer Operations',
    2
]);
echo "2. Education records updated (CHMSU & Rafael B. Lacson Memorial High School).\n";

// 3. Clear old demo experience and insert real records
$pdo->exec("DELETE FROM experience");
$expStmt = $pdo->prepare("
    INSERT INTO experience (
        position, organization, employment_type,
        start_date, end_date, is_current, description, responsibilities, achievements, technologies_used, display_order, is_published
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$expStmt->execute([
    'Artificial Intelligence Remote Intern',
    'Progee',
    'Internship / Remote',
    '2026-08-01',
    '2026-09-05',
    1,
    'Selected for a competitive remote Artificial Intelligence Internship program focusing on practical AI industry applications, prompt engineering, and intelligent workflow automation.',
    "• Develop, evaluate, and optimize prompt engineering workflows for AI-assisted applications.\n• Build and test task-specific AI solutions and digital workflows.\n• Collaborate remotely with international developers and engineers on technical assignments.\n• Maintain code quality, testing consistency, and structured project documentation.",
    'Completed competitive selection for global remote cohort',
    'Generative AI, Prompt Engineering, Python, Workflow Automation, Quality Assurance',
    1,
    1
]);

$expStmt->execute([
    'Web Developer & Administrative Support Specialist',
    'Freelance / Independent Projects',
    'Freelance / Project-Based',
    '2024-01-15',
    NULL,
    1,
    'Designing and developing custom web systems, automating administrative tasks, and building responsive client solutions.',
    "• Architect and develop dynamic web applications using Plain PHP 8, MySQL PDO, and Bootstrap 5.\n• Perform end-to-end quality assurance, test case execution, and cross-device testing.\n• Provide virtual assistant and administrative support including data management and documentation.",
    'Delivered responsive web portals and automated administration workflows',
    'PHP, MySQL, HTML5, CSS3, JavaScript, Bootstrap 5, Virtual Assistance',
    2,
    1
]);
echo "3. Experience timeline updated (Progee AI Internship & Web Development).\n";

// 4. Licenses (CSC Professional Eligibility)
$pdo->exec("DELETE FROM licenses");
$licStmt = $pdo->prepare("
    INSERT INTO licenses (
        title, issuing_organization, issue_date, license_number,
        verification_url, description, is_published, display_order
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$licStmt->execute([
    'Career Service Professional Eligibility (CSC Professional)',
    'Civil Service Commission (CSC), Republic of the Philippines',
    '2026-07-15',
    'CSC-PROF-ELIGIBLE',
    'https://csc.gov.ph',
    'Conferred Career Service Professional Eligibility with permanent status for first and second level government and corporate professional positions in the Philippines.',
    1,
    1
]);
echo "4. Licenses updated (CSC Professional Eligibility).\n";

// 5. Certifications (Fundamentals of Ethical Hacking)
$pdo->exec("DELETE FROM certifications");
$certStmt = $pdo->prepare("
    INSERT INTO certifications (
        title, issuing_organization, issue_date, credential_id,
        credential_url, description, is_featured, is_published, display_order
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$certStmt->execute([
    'Fundamentals of Ethical Hacking',
    'Cybersecurity Training & Accreditation',
    '2025-05-19',
    'FEH-2025-VERIFIED',
    'https://thecasehq.com',
    'Practical foundation in ethical hacking, vulnerability discovery, network security analysis, and secure web application assessment.',
    1,
    1,
    1
]);
echo "5. Certifications updated.\n";

// 6. Update Site Settings
$pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'site_title'")
    ->execute(['Martin Diasanta | Portfolio']);
$pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'meta_description'")
    ->execute(['Portfolio of Martin Diasanta - 3rd Year MIS Student at CHMSU, Prompt Engineer, AI Intern at Progee, and Web Developer based in Talisay, Philippines.']);

echo "\nAll real background details successfully synchronized!\n";
