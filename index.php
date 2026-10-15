<?php
/**
 * Public Homepage - Personal Portfolio (Interactive & Responsive)
 */
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = db();

// Fetch Data for Sections
$profile = get_profile();

// Skills categorized
$skillCategories = $pdo->query("
    SELECT c.*, COUNT(s.id) as skill_count
    FROM skill_categories c
    JOIN skills s ON s.category_id = c.id
    WHERE s.is_active = 1
    GROUP BY c.id
    ORDER BY c.display_order ASC
")->fetchAll();

$skillsByCategory = [];
$allSkills = $pdo->query("SELECT * FROM skills WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
foreach ($allSkills as $sk) {
    $catId = $sk['category_id'] ?: 0;
    $skillsByCategory[$catId][] = $sk;
}

// Featured & Published Projects (Limit 6 for homepage)
$featuredProjects = $pdo->query("
    SELECT * FROM projects
    WHERE is_published = 1
    ORDER BY is_featured DESC, display_order ASC, id DESC
    LIMIT 6
")->fetchAll();

// Published Certifications
$certifications = $pdo->query("
    SELECT * FROM certifications
    WHERE is_published = 1
    ORDER BY is_featured DESC, display_order ASC, id DESC
    LIMIT 4
")->fetchAll();

// Published Licenses
$licenses = $pdo->query("
    SELECT * FROM licenses
    WHERE is_published = 1
    ORDER BY display_order ASC, id DESC
")->fetchAll();

// Published Work Experience
$experiences = $pdo->query("
    SELECT * FROM experience
    WHERE is_published = 1
    ORDER BY display_order ASC, start_date DESC
")->fetchAll();

// Education Records
$educationList = $pdo->query("
    SELECT * FROM education
    ORDER BY display_order ASC, id DESC
")->fetchAll();

// External Social Links
$socialLinks = $pdo->query("
    SELECT * FROM external_links
    WHERE is_active = 1
    ORDER BY display_order ASC
")->fetchAll();
?>

<!-- 1. HERO SECTION -->
<section class="hero-section text-center text-md-start">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-12 col-md-5 col-lg-5 text-center order-md-last reveal-fade-up">
        <div class="hero-avatar-wrap">
          <?php if (!empty($profile['profile_image'])): ?>
            <img src="<?= BASE_URL . '/' . e($profile['profile_image']) ?>" class="hero-avatar" alt="<?= e($profile['full_name']) ?>">
          <?php else: ?>
            <div class="hero-avatar bg-light d-flex align-items-center justify-content-center text-teal mx-auto fs-1 fw-bold" style="color:var(--primary-color);">
              <?= e(get_monogram($profile['full_name'] ?? 'P')) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-12 col-md-7 col-lg-7 reveal-fade-up">
        <?php if (!empty($profile['availability_status'])): ?>
          <div class="mb-3">
            <span class="status-badge">
              <span class="status-dot"></span>
              <?= e($profile['availability_status']) ?>
            </span>
          </div>
        <?php endif; ?>

        <h1 class="hero-title mb-2"><?= e($profile['full_name']) ?></h1>
        <h3 class="fw-bold mb-3" style="color:var(--primary-color); font-size:1.35rem;">
          <?= e($profile['professional_title']) ?>
        </h3>

        <p class="hero-subtitle mb-4">
          <?= e($profile['tagline'] ?: $profile['short_summary']) ?>
        </p>

        <div class="hero-actions-wrap justify-content-center justify-content-md-start">
          <a href="#projects" class="btn btn-primary-custom hero-btn">
            <i class="bi bi-folder-fill me-1"></i> View My Projects
          </a>
          <?php if (!empty($profile['resume_file'])): ?>
            <a href="<?= BASE_URL ?>/resume.php" target="_blank" class="btn btn-outline-custom hero-btn">
              <i class="bi bi-file-earmark-arrow-down me-1"></i> Download Resume
            </a>
          <?php endif; ?>
          <a href="#contact" class="btn btn-outline-custom hero-btn">
            <i class="bi bi-chat-dots me-1"></i> Contact Me
          </a>
        </div>

        <!-- Quick Competency Chips -->
        <div class="hero-chips-wrap justify-content-center justify-content-md-start">
          <span class="hero-chip"><i class="bi bi-code-slash text-teal"></i> Plain PHP &amp; MySQL</span>
          <span class="hero-chip"><i class="bi bi-shield-check text-teal"></i> Software QA &amp; Testing</span>
          <span class="hero-chip"><i class="bi bi-robot text-teal"></i> AI-Assisted Workflows</span>
          <span class="hero-chip"><i class="bi bi-bootstrap text-teal"></i> Responsive UI</span>
        </div>

        <?php if (!empty($socialLinks)): ?>
          <div class="mt-4 pt-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
            <span class="text-muted small fw-semibold me-1">Connect:</span>
            <?php foreach ($socialLinks as $sl): ?>
              <a href="<?= e($sl['url']) ?>" target="_blank" rel="noopener noreferrer" class="hero-social-link" title="<?= e($sl['name']) ?>">
                <i class="<?= e($sl['icon'] ?: 'bi bi-link-45deg') ?>"></i>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>


<!-- 2. ABOUT ME SECTION -->
<section id="about" class="section-padding">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Background &amp; Philosophy</div>
      <h2 class="section-title">About Me</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <div class="row g-4 align-items-stretch">
      <div class="col-12 col-lg-7 reveal-fade-up">
        <div class="card-custom p-4 p-md-5 h-100">
          <h4 class="fw-bold mb-3 text-dark">Professional Overview</h4>
          <div class="lh-lg text-muted">
            <?= nl2br(e($profile['about_me'])) ?>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-5 reveal-fade-up">
        <div class="row g-3 h-100">
          <div class="col-12">
            <div class="card-custom p-3 p-md-4">
              <div class="d-flex align-items-start gap-3">
                <div class="skill-icon-wrap flex-shrink-0"><i class="bi bi-code-square"></i></div>
                <div>
                  <h6 class="fw-bold mb-1 text-dark">Full-Stack Web Development</h6>
                  <p class="small text-muted mb-0">Clean plain PHP 8, normalized MySQL PDO queries, Bootstrap 5, and responsive vanilla JS architecture.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card-custom p-3 p-md-4">
              <div class="d-flex align-items-start gap-3">
                <div class="skill-icon-wrap flex-shrink-0" style="background:#fef3c7; color:#d97706;"><i class="bi bi-robot"></i></div>
                <div>
                  <h6 class="fw-bold mb-1 text-dark">AI-Assisted Workflow</h6>
                  <p class="small text-muted mb-0">Utilizing generative AI tools for accelerated code drafting, debugging, and edge-case test generation.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card-custom p-3 p-md-4">
              <div class="d-flex align-items-start gap-3">
                <div class="skill-icon-wrap flex-shrink-0" style="background:#eff6ff; color:#2563eb;"><i class="bi bi-shield-check"></i></div>
                <div>
                  <h6 class="fw-bold mb-1 text-dark">Quality Assurance &amp; Testing</h6>
                  <p class="small text-muted mb-0">Designing structured test cases, regression testing, boundary checks, API validation, and bug reporting.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- 3. SKILLS SECTION -->
<section id="skills" class="section-padding bg-card border-top border-bottom" style="background-color:var(--bg-card);">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Technical Competencies</div>
      <h2 class="section-title">Skills &amp; Technologies</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <!-- Skill Filter Tabs -->
    <div class="filter-nav-wrap reveal-fade-up">
      <button class="filter-btn skill-category-btn active" data-category-id="all">
        <i class="bi bi-grid me-1"></i> All Skills
      </button>
      <?php foreach ($skillCategories as $cat): ?>
        <button class="filter-btn skill-category-btn" data-category-id="<?= $cat['id'] ?>">
          <?= e($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <div class="row g-4">
      <?php foreach ($skillCategories as $cat): ?>
        <?php $catSkills = $skillsByCategory[$cat['id']] ?? []; ?>
        <?php if (!empty($catSkills)): ?>
          <div class="col-12 col-md-6 col-lg-4 skill-category-card reveal-fade-up" data-category-id="<?= $cat['id'] ?>">
            <div class="card-custom p-4 h-100">
              <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark d-flex align-items-center justify-content-between">
                <span><?= e($cat['name']) ?></span>
                <span class="badge bg-light text-muted border font-monospace extra-small"><?= count($catSkills) ?></span>
              </h5>
              <div class="d-flex flex-column gap-3">
                <?php foreach ($catSkills as $sk): ?>
                  <div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="fw-semibold small text-dark">
                        <i class="<?= e($sk['icon_class'] ?: 'bi bi-check2-circle') ?> me-1" style="color:var(--primary-color);"></i>
                        <?= e($sk['name']) ?>
                      </span>
                      <span class="extra-small text-muted fw-bold"><?= (int)$sk['proficiency_level'] ?>%</span>
                    </div>
                    <div class="skill-progress">
                      <div class="skill-progress-bar" data-level="<?= (int)$sk['proficiency_level'] ?>"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- 4. PROJECTS SHOWCASE SECTION -->
<section id="projects" class="section-padding">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Portfolio Highlights</div>
      <h2 class="section-title">Featured Projects</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <div class="row g-4 mb-5">
      <?php if (empty($featuredProjects)): ?>
        <div class="col-12 text-center text-muted py-5">No projects published yet.</div>
      <?php else: ?>
        <?php foreach ($featuredProjects as $p): ?>
          <div class="col-12 col-md-6 col-lg-4 reveal-fade-up">
            <div class="card-custom d-flex flex-column">
              <div class="project-card-img-wrap">
                <?php if (!empty($p['project_image'])): ?>
                  <img src="<?= BASE_URL . '/' . e($p['project_image']) ?>" class="project-card-img" alt="<?= e($p['title']) ?>">
                <?php else: ?>
                  <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted fs-2">
                    <i class="bi bi-folder2-open"></i>
                  </div>
                <?php endif; ?>
                <?php if ($p['is_featured']): ?>
                  <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark shadow-sm fw-bold">
                    <i class="bi bi-star-fill me-1"></i> Featured
                  </span>
                <?php endif; ?>
              </div>

              <div class="p-4 d-flex flex-column flex-grow-1">
                <div class="text-muted extra-small mb-1 fw-bold text-uppercase"><?= e($p['project_year']) ?></div>
                <h5 class="fw-bold mb-2 text-dark"><?= e($p['title']) ?></h5>
                <p class="small text-muted mb-3 flex-grow-1"><?= e($p['short_description']) ?></p>

                <?php if (!empty($p['technologies_used'])): ?>
                  <div class="d-flex flex-wrap gap-1 mb-3">
                    <?php 
                    $techs = explode(',', $p['technologies_used']);
                    foreach (array_slice($techs, 0, 4) as $t): 
                    ?>
                      <span class="tech-badge"><?= e(trim($t)) ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
                  <a href="<?= BASE_URL ?>/project.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-custom btn-card-action fw-semibold">
                    View Details &rarr;
                  </a>
                  <div class="d-flex gap-2">
                    <?php if (!empty($p['github_url'])): ?>
                      <a href="<?= e($p['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-muted fs-5" title="GitHub Repo"><i class="bi bi-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($p['live_demo_url'])): ?>
                      <a href="<?= e($p['live_demo_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-muted fs-5" title="Live Demo"><i class="bi bi-box-arrow-up-right"></i></a>
                    <?php endif; ?>
                  </div>
                </div>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="text-center reveal-fade-up">
      <a href="<?= BASE_URL ?>/projects.php" class="btn btn-primary-custom">
        Explore All Projects &amp; Archives <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>


<!-- 5. CERTIFICATIONS & LICENSES -->
<section id="certifications" class="section-padding bg-card border-top border-bottom" style="background-color:var(--bg-card);">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Credentials &amp; Training</div>
      <h2 class="section-title">Certifications &amp; Licenses</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <div class="row g-4">
      <?php foreach ($certifications as $c): ?>
        <div class="col-12 col-md-6 reveal-fade-up">
          <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-start gap-3">
              <div class="skill-icon-wrap flex-shrink-0"><i class="bi bi-patch-check-fill"></i></div>
              <div class="flex-grow-1">
                <h5 class="fw-bold mb-1 text-dark"><?= e($c['title']) ?></h5>
                <div class="text-muted small fw-semibold mb-2"><?= e($c['issuing_organization']) ?></div>
                <p class="small text-muted mb-3"><?= e($c['description']) ?></p>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top extra-small text-muted">
                  <span>Issued: <strong><?= format_date($c['issue_date'], 'M Y') ?></strong></span>
                  <?php if (!empty($c['credential_id'])): ?>
                    <span class="font-monospace">ID: <?= e($c['credential_id']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($c['certificate_file']) && !empty($c['credential_url'])): ?>
                    <div class="d-flex gap-2">
                      <a href="<?= BASE_URL . '/' . e($c['certificate_file']) ?>" target="_blank" class="fw-bold text-teal">View File &rarr;</a>
                      <span class="text-muted">&bull;</span>
                      <a href="<?= e($c['credential_url']) ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-teal">Verify Online &rarr;</a>
                    </div>
                  <?php elseif (!empty($c['certificate_file'])): ?>
                    <a href="<?= BASE_URL . '/' . e($c['certificate_file']) ?>" target="_blank" class="fw-bold text-teal">View Certificate File &rarr;</a>
                  <?php elseif (!empty($c['credential_url'])): ?>
                    <a href="<?= e($c['credential_url']) ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-teal">Verify Online &rarr;</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php foreach ($licenses as $l): ?>
        <div class="col-12 col-md-6 reveal-fade-up">
          <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-start gap-3">
              <div class="skill-icon-wrap flex-shrink-0" style="background:#eff6ff; color:#2563eb;"><i class="bi bi-award-fill"></i></div>
              <div class="flex-grow-1">
                <h5 class="fw-bold mb-1 text-dark"><?= e($l['title']) ?></h5>
                <div class="text-muted small fw-semibold mb-2"><?= e($l['issuing_organization']) ?></div>
                <p class="small text-muted mb-3"><?= e($l['description']) ?></p>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top extra-small text-muted">
                  <span>License #: <strong><?= e($l['license_number'] ?: 'Verified') ?></strong></span>
                  <?php if (!empty($l['document_file'])): ?>
                    <a href="<?= BASE_URL . '/' . e($l['document_file']) ?>" target="_blank" class="fw-bold text-teal">View Document &rarr;</a>
                  <?php elseif (!empty($l['verification_url'])): ?>
                    <a href="<?= e($l['verification_url']) ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-teal">Verify License &rarr;</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5 reveal-fade-up">
      <a href="<?= BASE_URL ?>/certifications.php" class="btn btn-outline-custom">
        <i class="bi bi-patch-check me-1"></i> View All Certifications &amp; Credentials <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>


<!-- 6. EXPERIENCE TIMELINE -->
<section id="experience" class="section-padding">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Career Journey</div>
      <h2 class="section-title">Work Experience</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="timeline">
          <?php foreach ($experiences as $exp): ?>
            <div class="timeline-item reveal-fade-up">
              <div class="timeline-dot"></div>
              <div class="card-custom p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                  <div>
                    <h5 class="fw-bold mb-0 text-dark"><?= e($exp['position']) ?></h5>
                    <div class="fw-semibold mt-1" style="color:var(--primary-color);">
                      <?= e($exp['organization']) ?> &bull; <span class="text-muted small"><?= e($exp['employment_type']) ?></span>
                    </div>
                  </div>
                  <div class="timeline-date">
                    <?= format_date($exp['start_date'], 'M Y') ?> &ndash; 
                    <?= $exp['is_current'] ? 'Present' : format_date($exp['end_date'], 'M Y') ?>
                  </div>
                </div>

                <p class="small text-muted mb-3"><?= e($exp['description']) ?></p>

                <?php if (!empty($exp['responsibilities'])): ?>
                  <div class="small text-dark mb-2">
                    <strong>Key Responsibilities:</strong>
                    <div class="lh-relaxed text-muted mt-1"><?= nl2br(e($exp['responsibilities'])) ?></div>
                  </div>
                <?php endif; ?>

                <?php if (!empty($exp['technologies_used'])): ?>
                  <div class="pt-2 border-top extra-small text-muted">
                    <strong>Technologies:</strong> <?= e($exp['technologies_used']) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- 7. EDUCATION -->
<section id="education" class="section-padding bg-card border-top border-bottom" style="background-color:var(--bg-card);">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Academic History</div>
      <h2 class="section-title">Education</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <div class="row g-4 justify-content-center">
      <?php foreach ($educationList as $edu): ?>
        <div class="col-12 col-md-6 reveal-fade-up">
          <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-start gap-3">
              <div class="skill-icon-wrap flex-shrink-0"><i class="bi bi-mortarboard-fill"></i></div>
              <div>
                <h5 class="fw-bold mb-1 text-dark"><?= e($edu['degree_program']) ?></h5>
                <div class="fw-semibold text-muted small mb-2"><?= e($edu['institution']) ?> (<?= e($edu['start_year']) ?> &ndash; <?= e($edu['end_year']) ?>)</div>
                <p class="small text-muted mb-2"><?= e($edu['description']) ?></p>
                <?php if (!empty($edu['relevant_coursework'])): ?>
                  <div class="extra-small text-muted"><strong>Coursework:</strong> <?= e($edu['relevant_coursework']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- 8. CONTACT SECTION -->
<section id="contact" class="section-padding">
  <div class="container">
    <div class="section-title-wrap text-center reveal-fade-up">
      <div class="section-subtitle">Get In Touch</div>
      <h2 class="section-title">Contact Me</h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <div class="row g-4 justify-content-center">
      <!-- Contact Info Cards -->
      <div class="col-12 col-lg-5 reveal-fade-up">
        <div class="card-custom p-4 p-md-5 h-100">
          <h4 class="fw-bold mb-3">Let's Connect</h4>
          <p class="text-muted small mb-4">
            Whether you have an upcoming project, a job opening, QA requirements, or need assistance with digital operations, feel free to reach out.
          </p>

          <div class="d-flex flex-column gap-3">
            <!-- Email Item with Copy Button -->
            <div class="p-3 rounded border bg-light d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3 overflow-hidden">
                <div class="p-2 rounded bg-white text-teal fs-5" style="color:var(--primary-color);"><i class="bi bi-envelope-at"></i></div>
                <div class="overflow-hidden">
                  <div class="extra-small text-muted fw-bold text-uppercase">Email Address</div>
                  <a href="mailto:<?= e($profile['email']) ?>" class="fw-semibold text-dark text-truncate d-block"><?= e($profile['email']) ?></a>
                </div>
              </div>
              <button class="btn btn-sm btn-outline-secondary btn-copy-clipboard" data-copy-value="<?= e($profile['email']) ?>" title="Copy email address">
                <i class="bi bi-clipboard"></i>
              </button>
            </div>

            <?php if (!empty($profile['location'])): ?>
              <div class="p-3 rounded border bg-light d-flex align-items-center gap-3">
                <div class="p-2 rounded bg-white text-teal fs-5" style="color:var(--primary-color);"><i class="bi bi-geo-alt"></i></div>
                <div>
                  <div class="extra-small text-muted fw-bold text-uppercase">Location</div>
                  <span class="fw-semibold text-dark"><?= e($profile['location']) ?></span>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($profile['phone'])): ?>
              <div class="p-3 rounded border bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <div class="p-2 rounded bg-white text-teal fs-5" style="color:var(--primary-color);"><i class="bi bi-telephone"></i></div>
                  <div>
                    <div class="extra-small text-muted fw-bold text-uppercase">Phone</div>
                    <span class="fw-semibold text-dark"><?= e($profile['phone']) ?></span>
                  </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary btn-copy-clipboard" data-copy-value="<?= e($profile['phone']) ?>" title="Copy phone number">
                  <i class="bi bi-clipboard"></i>
                </button>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-12 col-lg-7 reveal-fade-up">
        <div class="card-custom p-4 p-md-5">
          <?php render_flash_messages(); ?>

          <form action="<?= BASE_URL ?>/contact.php" method="POST">
            <?= csrf_field() ?>
            <!-- Spam Honeypot Field -->
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="contact_name" class="form-label fw-semibold small">Your Name *</label>
                <input type="text" class="form-control" id="contact_name" name="name" required placeholder="John Doe">
              </div>

              <div class="col-12 col-md-6">
                <label for="contact_email" class="form-label fw-semibold small">Your Email *</label>
                <input type="email" class="form-control" id="contact_email" name="email" required placeholder="john@example.com">
              </div>

              <div class="col-12">
                <label for="contact_subject" class="form-label fw-semibold small">Subject *</label>
                <input type="text" class="form-control" id="contact_subject" name="subject" required placeholder="Full-Stack Web Development Opportunity">
              </div>

              <div class="col-12">
                <label for="contact_message" class="form-label fw-semibold small">Message *</label>
                <textarea class="form-control" id="contact_message" name="message" rows="5" required placeholder="Hello, I would like to discuss..."></textarea>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary-custom w-100 py-3">
                  Send Message <i class="bi bi-send ms-1"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
