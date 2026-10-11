/**
 * FAQ & Help Center Module Script
 * Handles search filter, category filter, accordion toggling, and swipe gestures.
 */

// Toggle Accordion Item
function toggleFaqItem(btn) {
  const item = btn.closest('.faq-item');
  if (!item) return;
  const isActive = item.classList.contains('active');

  // Close other items for clean single accordion mode
  document.querySelectorAll('.faq-item').forEach(el => {
    if (el !== item) el.classList.remove('active');
  });

  if (isActive) {
    item.classList.remove('active');
  } else {
    item.classList.add('active');
  }
}

// Realtime Search & Filter
function filterFaq() {
  const input = document.getElementById('faqSearchInput');
  if (!input) return;
  const query = input.value.toLowerCase().trim();
  const items = document.querySelectorAll('.faq-item');
  let visibleCount = 0;

  items.forEach(item => {
    const text = (item.innerText + ' ' + (item.getAttribute('data-keywords') || '')).toLowerCase();
    if (!query || text.includes(query)) {
      item.style.display = 'block';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });

  const noFound = document.getElementById('noFaqFound');
  if (noFound) {
    noFound.style.display = visibleCount === 0 ? 'block' : 'none';
  }
}

// Smoothly scroll directly to "Pertanyaan yang sering ditanyakan" header
function scrollToFaqHeader() {
  const header = document.querySelector('.faq-accordion-header');
  if (header) {
    const navbar = document.querySelector('.navbar.is-app-header, .landing-header');
    const navHeight = navbar ? navbar.offsetHeight : 70;
    const targetPos = header.getBoundingClientRect().top + window.pageYOffset;
    const offsetPosition = targetPos - navHeight - 16;

    window.scrollTo({
      top: Math.max(0, offsetPosition),
      behavior: 'smooth'
    });
  }
}

// Filter by keyword chip
function searchByKeyword(keyword) {
  const input = document.getElementById('faqSearchInput');
  if (input) {
    input.value = keyword;
    filterFaq();
    scrollToFaqHeader();
  }
}

// Filter by category card
function filterByCategory(cat) {
  const items = document.querySelectorAll('.faq-item');
  let visibleCount = 0;

  items.forEach(item => {
    const itemCat = item.getAttribute('data-category') || '';
    if (cat === 'all' || itemCat.includes(cat)) {
      item.style.display = 'block';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });

  // Clear search input
  const input = document.getElementById('faqSearchInput');
  if (input) input.value = '';

  const noFound = document.getElementById('noFaqFound');
  if (noFound) {
    noFound.style.display = visibleCount === 0 ? 'block' : 'none';
  }

  // Automatically open the first matching FAQ item for instant view
  const firstVisible = Array.from(items).find(item => item.style.display !== 'none');
  if (firstVisible) {
    items.forEach(el => el.classList.remove('active'));
    firstVisible.classList.add('active');
  }

  scrollToFaqHeader();
}
