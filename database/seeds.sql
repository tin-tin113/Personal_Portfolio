-- ============================================================
-- Personal Portfolio & Private Admin CMS - Initial Seed Data
-- ============================================================

USE `personal_portfolio`;

-- Clear existing data safely
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `admin_users`;
TRUNCATE TABLE `site_settings`;
TRUNCATE TABLE `profile`;
TRUNCATE TABLE `skill_categories`;
TRUNCATE TABLE `skills`;
TRUNCATE TABLE `projects`;
TRUNCATE TABLE `project_images`;
TRUNCATE TABLE `certifications`;
TRUNCATE TABLE `licenses`;
TRUNCATE TABLE `education`;
TRUNCATE TABLE `experience`;
TRUNCATE TABLE `external_links`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- Admin User
-- Default Credentials: Username: admin | Password: AdminPassword123!
-- ------------------------------------------------------------
INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$ygqfX7J0nCFcr2US7kOcKOTDybYQfgu/wIXyNCSBAUc4mXnodW.wS');

-- ------------------------------------------------------------
-- Site Settings
-- ------------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_title', '[Your Name] | AI-Assisted Full-Stack Developer & QA Specialist'),
('site_description', 'Professional portfolio of [Your Name] - AI-Assisted Full-Stack Developer, Quality Assurance Analyst, and Administrative Specialist.'),
('accent_color', '#0f766e'),
('contact_email', 'contact@example.com'),
('footer_text', 'Building practical, reliable web solutions with modern PHP, database design, and AI workflows.'),
('copyright_text', '© 2026 [Your Name]. All rights reserved.'),
('seo_keywords', 'Full-Stack Developer, PHP, MySQL, Quality Assurance, Software Testing, AI Workflows'),
('availability_status', 'Available for Full-Time & Project Roles');

-- ------------------------------------------------------------
-- Profile Information
-- ------------------------------------------------------------
INSERT INTO `profile` (
  `id`, `full_name`, `professional_title`, `tagline`, `short_summary`, `about_me`,
  `email`, `phone`, `location`, `availability_status`, `github_url`, `linkedin_url`
) VALUES (
  1,
  '[Your Name]',
  'AI-Assisted Full-Stack Developer | Quality Assurance | Administrative Support',
  'Building practical, reliable, and user-focused web solutions with PHP, MySQL, and AI-driven productivity tools.',
  'Versatile developer and QA analyst skilled in plain PHP, relational database design, software testing methodologies, and AI-accelerated development workflows.',
  'I am a full-stack developer and software quality assurance specialist who leverages modern AI tools to accelerate development cycles, refine test suites, and deliver robust web applications. With a strong foundation in plain PHP 8, MySQL database architecture, and responsive frontend interfaces, I focus on clean code, performance, and user accessibility.\n\nMy approach combines rigorous software testing with streamlined administrative efficiency, ensuring every solution meets real-world business objectives.',
  'your.email@example.com',
  '+1 (555) 000-0000',
  '[City, Country]',
  'Open to Full-Time & Freelance Roles',
  'https://github.com/[your-username]',
  'https://linkedin.com/in/[your-username]'
);

-- ------------------------------------------------------------
-- Skill Categories
-- ------------------------------------------------------------
INSERT INTO `skill_categories` (`id`, `name`, `display_order`) VALUES
(1, 'Web Development', 10),
(2, 'Programming & Databases', 20),
(3, 'AI Workflows & Tools', 30),
(4, 'QA & Software Testing', 40),
(5, 'Administrative & Digital Support', 50);

-- ------------------------------------------------------------
-- Skills
-- ------------------------------------------------------------
INSERT INTO `skills` (`category_id`, `name`, `proficiency_level`, `level_name`, `icon_class`, `display_order`, `is_active`) VALUES
(1, 'PHP 8.x (Core & PDO)', 90, 'Advanced', 'bi bi-code-slash', 10, 1),
(1, 'HTML5 & CSS3', 95, 'Expert', 'bi bi-filetype-html', 20, 1),
(1, 'Bootstrap 5', 90, 'Advanced', 'bi bi-bootstrap', 30, 1),
(1, 'Vanilla JavaScript (ES6+)', 85, 'Advanced', 'bi bi-filetype-js', 40, 1),

(2, 'MySQL / MariaDB', 85, 'Advanced', 'bi bi-database', 10, 1),
(2, 'Database Design & SQL', 85, 'Advanced', 'bi bi-diagram-3', 20, 1),
(2, 'Git & GitHub Version Control', 88, 'Advanced', 'bi bi-git', 30, 1),

(3, 'AI-Assisted Coding (Copilot, Gemini, Claude)', 92, 'Expert', 'bi bi-robot', 10, 1),
(3, 'Prompt Engineering for Dev', 90, 'Advanced', 'bi bi-cpu', 20, 1),

(4, 'Software Testing & Bug Reporting', 85, 'Advanced', 'bi bi-bug', 10, 1),
(4, 'Manual & Automated Testing Principles', 80, 'Intermediate', 'bi bi-check-circle', 20, 1),
(4, 'API Testing (Postman/Curl)', 82, 'Advanced', 'bi bi-send', 30, 1),

(5, 'Technical Documentation', 90, 'Advanced', 'bi bi-file-earmark-text', 10, 1),
(5, 'Digital Administrative Support', 88, 'Advanced', 'bi bi-briefcase', 20, 1);

-- ------------------------------------------------------------
-- Projects
-- ------------------------------------------------------------
INSERT INTO `projects` (
  `id`, `title`, `slug`, `short_description`, `detailed_description`,
  `my_role`, `key_features`, `challenges`, `solutions`,
  `development_methodology`, `project_year`, `github_url`, `live_demo_url`, `documentation_url`,
  `technologies_used`, `is_featured`, `is_published`, `display_order`
) VALUES
(
  1,
  '[Sample Project] E-Commerce Inventory & Order System',
  'sample-ecommerce-inventory-system',
  'Custom PHP/MySQL inventory tracking system built with secure admin dashboard and live order processing.',
  'A full-featured web application designed for small-to-medium businesses to manage real-time inventory levels, customer orders, and supplier catalogs. Built entirely with plain PHP PDO, Bootstrap 5, and custom SQL queries for optimal speed and reliability.',
  'Lead Full-Stack Developer & Database Architect',
  '• Real-time stock level alerts\n• Multi-role admin permissions\n• CSRF & XSS protected order forms\n• Exportable CSV inventory reports',
  'Optimizing SQL queries for large product catalogs without using heavy ORM abstractions.',
  'Designed indexed database tables and implemented prepared PDO statements for high performance.',
  'Agile / AI-Assisted Iterative Development',
  '2026',
  'https://github.com/[your-username]/[sample-repo-1]',
  'https://[demo-link-1].example.com',
  'https://github.com/[your-username]/[sample-repo-1]#readme',
  'PHP 8, MySQL, Bootstrap 5, Vanilla JS, PDO',
  1, 1, 10
),
(
  2,
  '[Sample Project] Automated Web QA Test Suite Dashboard',
  'sample-qa-test-suite-dashboard',
  'Centralized dashboard for tracking manual test cases, bug execution logs, and automated test summaries.',
  'A dedicated web tool for QA teams to log test executions, generate bug severity reports, and document reproducible steps. Integrated with custom reporting charts built using standard CSS and Vanilla JavaScript.',
  'QA Engineer & Full-Stack Developer',
  '• Test case lifecycle tracking (Pass/Fail/Blocked)\n• Markdown-enabled bug report generator\n• Visual pass-rate analytics\n• Role-based access control',
  'Structuring clear test metrics without relying on third-party analytical frameworks.',
  'Built custom PHP database aggregation routines and light JavaScript visualization components.',
  'Test-Driven Development (TDD)',
  '2025',
  'https://github.com/[your-username]/[sample-repo-2]',
  'https://[demo-link-2].example.com',
  'https://github.com/[your-username]/[sample-repo-2]#readme',
  'PHP 8, MySQL, HTML5, CSS3, JavaScript',
  1, 1, 20
),
(
  3,
  '[Sample Project] AI-Assisted Content Management System',
  'sample-ai-assisted-cms',
  'Lightweight CMS with integrated AI productivity tools for rapid blogging and digital document organization.',
  'A custom CMS engineered for administrative teams to draft, preview, and publish documentation efficiently. Incorporates structured template generation and secure media handling.',
  'Solo Developer & UI Designer',
  '• Secure multi-format upload validator\n• Dynamic SEO metadata editor\n• Responsive layout manager\n• Audit log tracking',
  'Ensuring zero malicious file uploads while allowing document attachments like PDFs.',
  'Implemented MIME-type verification with finfo_file and strict random file renaming routines.',
  'AI-Assisted Workflow',
  '2025',
  'https://github.com/[your-username]/[sample-repo-3]',
  'https://[demo-link-3].example.com',
  'https://github.com/[your-username]/[sample-repo-3]#readme',
  'PHP 8, PDO, Bootstrap 5, MySQL',
  0, 1, 30
);

-- ------------------------------------------------------------
-- Certifications
-- ------------------------------------------------------------
INSERT INTO `certifications` (
  `title`, `issuing_organization`, `issue_date`, `expiration_date`,
  `credential_id`, `credential_url`, `description`, `is_featured`, `is_published`, `display_order`
) VALUES
(
  '[Sample Cert] Full-Stack Web Development Certification',
  '[Issuing Organization / Platform]',
  '2025-06-15',
  NULL,
  'CERT-[12345678]',
  'https://[verification-link].example.com/cert/123',
  'Comprehensive certification covering PHP server-side programming, relational databases, responsive web design, and web security best practices.',
  1, 1, 10
),
(
  '[Sample Cert] Software Quality Assurance & Testing Fundamentals',
  '[QA Testing Institute]',
  '2025-09-20',
  NULL,
  'QA-[87654321]',
  'https://[verification-link].example.com/cert/456',
  'Professional credential covering manual test case execution, bug lifecycle management, boundary analysis, and API validation.',
  1, 1, 20
);

-- ------------------------------------------------------------
-- Licenses & Credentials
-- ------------------------------------------------------------
INSERT INTO `licenses` (
  `title`, `issuing_organization`, `issue_date`, `license_number`,
  `verification_url`, `description`, `is_published`, `display_order`
) VALUES
(
  '[Sample Credential] Certified Information Technology Professional',
  '[Professional Board / Agency]',
  '2024-11-10',
  'LIC-[998877]',
  'https://[license-verification].example.com/verify',
  'Official professional qualification verifying IT proficiency, data management standards, and administrative workflow compliance.',
  1, 10
);

-- ------------------------------------------------------------
-- Education
-- ------------------------------------------------------------
INSERT INTO `education` (
  `institution`, `degree_program`, `start_year`, `end_year`,
  `description`, `achievements`, `relevant_coursework`, `display_order`
) VALUES
(
  '[Your University / College Name]',
  'Bachelor of Science in Information Technology / Computer Science (or Equivalent)',
  '2021',
  '2025',
  'Focused on software engineering principles, database administration, web development, and information systems management.',
  '• Graduated with Academic Honors\n• Lead Developer for Senior Capstone Web Application',
  'Web Development (PHP/MySQL), Database Architecture, Software QA & Testing, Systems Analysis & Design',
  10
);

-- ------------------------------------------------------------
-- Experience
-- ------------------------------------------------------------
INSERT INTO `experience` (
  `position`, `organization`, `employment_type`, `start_date`, `end_date`, `is_current`,
  `description`, `responsibilities`, `achievements`, `technologies_used`, `display_order`, `is_published`
) VALUES
(
  'Full-Stack Developer & QA Specialist',
  '[Company / Organization Name]',
  'Full-time',
  '2025-01-01',
  NULL,
  1,
  'Developing and maintaining custom PHP web applications while enforcing quality assurance standards across web products.',
  '• Designed normalized MySQL database schemas for internal web applications.\n• Implemented AI-assisted coding practices to reduce development turnaround times by 40%.\n• Executed regression testing and authored detailed bug reports in issue trackers.',
  'Refactored legacy database queries using PDO prepared statements to mitigate security vulnerabilities and improve response times.',
  'PHP 8, MySQL, Bootstrap 5, JavaScript, Git',
  10, 1
),
(
  'Administrative Support & Digital Assistant',
  '[Previous Organization Name]',
  'Contract',
  '2023-06-01',
  '2024-12-31',
  0,
  'Provided technical documentation, database entry, and administrative digital operations support.',
  '• Managed data entry and database hygiene for client management portals.\n• Drafted technical user guides and standard operating procedure (SOP) documents.',
  'Automated data cleanup processes using custom scripts, saving 10 hours weekly.',
  'Excel, SQL, Documentation, Administrative Tools',
  20, 1
);

-- ------------------------------------------------------------
-- Social & External Links
-- ------------------------------------------------------------
INSERT INTO `external_links` (`name`, `url`, `icon`, `category`, `display_order`, `is_active`) VALUES
('GitHub', 'https://github.com/[your-username]', 'bi bi-github', 'Social', 10, 1),
('LinkedIn', 'https://linkedin.com/in/[your-username]', 'bi bi-linkedin', 'Social', 20, 1),
('Email Contact', 'mailto:your.email@example.com', 'bi bi-envelope-at', 'Contact', 30, 1);
