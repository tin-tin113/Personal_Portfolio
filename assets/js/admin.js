/**
 * Admin CMS Interactivity & Security Handlers
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Sidebar Toggle for Mobile
  const sidebarToggle = document.getElementById('sidebar-toggle-btn');
  const sidebar = document.querySelector('.admin-sidebar');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  // 2. Delete Confirmation Interception
  document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const itemTitle = this.getAttribute('data-item-title') || 'this item';
      const confirmMsg = `Are you sure you want to delete "${itemTitle}"?\nThis action cannot be undone!`;
      
      if (!confirm(confirmMsg)) {
        e.preventDefault();
        return false;
      }
    });
  });

  // 3. Live File/Image Preview
  document.querySelectorAll('.image-preview-input').forEach(input => {
    input.addEventListener('change', function () {
      const targetId = this.getAttribute('data-preview-target');
      const targetImg = document.getElementById(targetId);
      
      if (targetImg && this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          targetImg.src = e.target.result;
          targetImg.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // 4. Quick Table Filter Search
  const tableSearch = document.getElementById('table-search-input');
  if (tableSearch) {
    tableSearch.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.table-searchable tbody tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});
