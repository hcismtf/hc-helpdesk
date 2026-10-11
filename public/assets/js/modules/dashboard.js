/**
 * Dashboard Client Interactivity
 * Handles ticket filter tabs, rich dropdown status/category filtering, keyword search, and quick FAQ search.
 */
document.addEventListener('DOMContentLoaded', function () {
  const filterTabs = document.querySelectorAll('.ticket-filter-tabs .filter-tab');
  const ticketCards = document.querySelectorAll('.ticket-item-card');
  const searchInput = document.getElementById('dashboardTicketSearch');
  const statusInput = document.getElementById('statusFilter');
  const categoryInput = document.getElementById('categoryFilter');

  function applyAllFilters() {
    const searchTerm = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const statusVal = statusInput ? statusInput.value : '';
    const categoryVal = (categoryInput ? categoryInput.value : '').toLowerCase().trim();
    const activeTab = document.querySelector('.ticket-filter-tabs .filter-tab.active');
    const activeTabType = activeTab ? activeTab.getAttribute('data-filter') : 'all';

    ticketCards.forEach((card) => {
      const text = card.textContent.toLowerCase();
      const statusPill = card.querySelector('.status-pill');
      const categoryTag = card.querySelector('.tag-category');
      const categoryText = categoryTag ? categoryTag.textContent.toLowerCase() : '';

      // 1. Keyword search match
      const matchSearch = !searchTerm || text.includes(searchTerm);

      // 2. Status match (either from rich dropdown or from active tab)
      let matchStatus = true;
      if (statusVal) {
        matchStatus = statusPill && statusPill.classList.contains('status-' + statusVal);
      } else if (activeTabType !== 'all') {
        if (activeTabType === 'action') {
          matchStatus = statusPill && statusPill.classList.contains('status-needs_action');
        } else if (activeTabType === 'in_progress') {
          matchStatus = statusPill && statusPill.classList.contains('status-in_progress');
        } else if (activeTabType === 'resolved') {
          matchStatus = statusPill && statusPill.classList.contains('status-resolved');
        }
      }

      // 3. Category match
      let matchCategory = true;
      if (categoryVal) {
        matchCategory = categoryText.includes(categoryVal);
      }

      card.style.display = (matchSearch && matchStatus && matchCategory) ? 'flex' : 'none';
    });
  }

  // 1. Tab switching
  filterTabs.forEach((tab) => {
    tab.addEventListener('click', function () {
      filterTabs.forEach((t) => t.classList.remove('active'));
      this.classList.add('active');

      // Reset specific status dropdown when clicking tabs
      if (statusInput) {
        statusInput.value = '';
        var statusLabel = document.getElementById('label_statusFilter');
        if (statusLabel) statusLabel.textContent = 'Status: Semua';
      }

      applyAllFilters();
    });
  });

  // 2. Keyword input
  if (searchInput) {
    searchInput.addEventListener('input', applyAllFilters);
  }

  // 3. Status rich dropdown change
  if (statusInput) {
    statusInput.addEventListener('change', function () {
      // Deactivate tab highlight if specific status chosen
      if (this.value) {
        filterTabs.forEach((t) => t.classList.remove('active'));
      }
      applyAllFilters();
    });
  }

  // 4. Category rich dropdown change
  if (categoryInput) {
    categoryInput.addEventListener('change', applyAllFilters);
  }

  // 5. Quick FAQ Search
  const quickFaqSearch = document.getElementById('quickFaqSearch');
  if (quickFaqSearch) {
    quickFaqSearch.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
          window.location.href = `/pusat-bantuan?q=${encodeURIComponent(query)}`;
        }
      }
    });
  }
});
