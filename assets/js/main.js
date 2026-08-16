/**
 * Public Portfolio Interactive JavaScript Engine
 * Modern, Lightweight Vanilla JS Micro-Interactions
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Dark Mode Theme Toggle & Memory
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const storedTheme = localStorage.getItem('portfolio-theme') || 'light';
  
  document.documentElement.setAttribute('data-theme', storedTheme);
  updateThemeIcon(storedTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('portfolio-theme', newTheme);
      updateThemeIcon(newTheme);
    });
  }

  function updateThemeIcon(theme) {
    if (!themeToggleBtn) return;
    const icon = themeToggleBtn.querySelector('i');
    if (icon) {
      if (theme === 'dark') {
        icon.className = 'bi bi-sun-fill';
        themeToggleBtn.setAttribute('title', 'Switch to Light Mode');
      } else {
        icon.className = 'bi bi-moon-stars-fill';
        themeToggleBtn.setAttribute('title', 'Switch to Dark Mode');
      }
    }
  }

  // 2. Navbar Scroll Elevation & Auto-Collapse on Mobile Click
  const navbar = document.querySelector('.navbar-custom');
  const navbarCollapse = document.getElementById('navbarMain');
  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
      navbar?.classList.add('navbar-scrolled');
    } else {
      navbar?.classList.remove('navbar-scrolled');
    }
  });

  // Auto close mobile navbar upon clicking any navigation link
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (navbarCollapse && navbarCollapse.classList.contains('show')) {
        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
        if (bsCollapse) {
          bsCollapse.hide();
        }
      }
    });
  });

  // 3. Scroll Reveal Animation using IntersectionObserver
  const revealElements = document.querySelectorAll('.reveal-fade-up');
  if ('IntersectionObserver' in window && revealElements.length > 0) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    // Fallback if browser doesn't support observer
    revealElements.forEach(el => el.classList.add('revealed'));
  }

  // 4. Animated Skill Progress Bars on Scroll
  const progressBars = document.querySelectorAll('.skill-progress-bar');
  if ('IntersectionObserver' in window && progressBars.length > 0) {
    const progressObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const bar = entry.target;
          const targetWidth = bar.getAttribute('data-level') || '80';
          bar.style.width = targetWidth + '%';
          observer.unobserve(bar);
        }
      });
    }, { threshold: 0.2 });

    progressBars.forEach(bar => progressObserver.observe(bar));
  } else {
    progressBars.forEach(bar => {
      const targetWidth = bar.getAttribute('data-level') || '80';
      bar.style.width = targetWidth + '%';
    });
  }

  // 5. Interactive Skill Category Tabs
  const skillCategoryButtons = document.querySelectorAll('.skill-category-btn');
  const skillCategorySections = document.querySelectorAll('.skill-category-card');

  if (skillCategoryButtons.length > 0 && skillCategorySections.length > 0) {
    skillCategoryButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        skillCategoryButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filterVal = btn.getAttribute('data-category-id');
        skillCategorySections.forEach(sec => {
          if (filterVal === 'all' || sec.getAttribute('data-category-id') === filterVal) {
            sec.style.display = 'block';
            sec.style.animation = 'fadeIn 0.35s ease';
          } else {
            sec.style.display = 'none';
          }
        });
      });
    });
  }

  // 6. Interactive Projects Filter & Keyword Search
  const projectSearchInput = document.getElementById('project-search-input') || document.getElementById('table-search-input');
  const projectCards = document.querySelectorAll('.project-item');
  const projectFilterBtns = document.querySelectorAll('.project-filter-btn');

  function applyProjectFilters() {
    const query = projectSearchInput ? projectSearchInput.value.toLowerCase().trim() : '';
    const activeBtn = document.querySelector('.project-filter-btn.active');
    const selectedTech = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';

    projectCards.forEach(card => {
      const cardText = card.textContent.toLowerCase();
      const cardTech = (card.getAttribute('data-tech') || '').toLowerCase();

      const matchesQuery = query === '' || cardText.includes(query);
      const matchesTech = selectedTech === 'all' || cardTech.includes(selectedTech.toLowerCase());

      if (matchesQuery && matchesTech) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }

  if (projectSearchInput) {
    projectSearchInput.addEventListener('input', applyProjectFilters);
  }

  if (projectFilterBtns.length > 0) {
    projectFilterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        projectFilterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyProjectFilters();
      });
    });
  }

  // 7. Interactive Copy-to-Clipboard with Toast Feedback
  const copyButtons = document.querySelectorAll('.btn-copy-clipboard');
  const copyToast = document.getElementById('copy-toast');

  copyButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const textToCopy = btn.getAttribute('data-copy-value') || btn.innerText;

      if (navigator.clipboard) {
        navigator.clipboard.writeText(textToCopy).then(() => {
          showCopyToast(`Copied "${textToCopy}" to clipboard!`);
        });
      } else {
        // Fallback
        const tempInput = document.createElement('input');
        tempInput.value = textToCopy;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        showCopyToast(`Copied to clipboard!`);
      }
    });
  });

  function showCopyToast(message) {
    if (!copyToast) return;
    copyToast.innerHTML = `<i class="bi bi-check-circle-fill text-success"></i> ${message}`;
    copyToast.classList.add('show');
    setTimeout(() => {
      copyToast.classList.remove('show');
    }, 2800);
  }

  // 8. Floating Back to Top Button
  const backToTopBtn = document.getElementById('back-to-top-btn');
  if (backToTopBtn) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 350) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    });

    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // 9. Smooth Scroll for Anchor Links with Header Offset
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#' || targetId === '') return;

      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        const headerOffset = 70;
        const elementPosition = targetEl.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

  // 10. Dynamic Navbar ScrollSpy Highlight
  const sections = document.querySelectorAll('section[id]');
  window.addEventListener('scroll', () => {
    const scrollY = window.pageYOffset;
    sections.forEach(current => {
      const sectionHeight = current.offsetHeight;
      const sectionTop = current.offsetTop - 120;
      const sectionId = current.getAttribute('id');
      const matchingLink = document.querySelector(`.navbar-nav a[href*="#${sectionId}"]`);

      if (matchingLink && scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
        navLinks.forEach(link => link.classList.remove('active'));
        matchingLink.classList.add('active');
      }
    });
  });
});
