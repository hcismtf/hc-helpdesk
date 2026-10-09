document.addEventListener('DOMContentLoaded', () => {
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
  $navbarBurgers.forEach(el => {
    el.addEventListener('click', () => {
      const target = document.getElementById(el.dataset.target);
      el.classList.toggle('is-active');
      target.classList.toggle('is-active');
    });
  });

  const trackBtn = document.getElementById('navTrackLink');
  if (trackBtn) {
    trackBtn.addEventListener('click', (e) => {
      const isLanding = window.location.pathname === '/' || window.location.pathname.endsWith('/index.php');
      if (isLanding) {
        e.preventDefault();
        const targetSection = document.getElementById('lacak-tiket');
        const inputTicket = document.getElementById('trackingTicketId');
        if (targetSection) {
          targetSection.scrollIntoView({ behavior: 'smooth' });
          setTimeout(() => { if (inputTicket) inputTicket.focus(); }, 350);
        }
      }
    });
  }
});