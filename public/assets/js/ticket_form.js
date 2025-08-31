const btnTicket = document.getElementById('btn-ticket');
const btnFaq = document.getElementById('btn-faq');
const ticketSection = document.getElementById('ticket-section');
const faqSection = document.getElementById('faq-section');

btnTicket.addEventListener('click', () => {
    btnTicket.classList.add('active');
    btnFaq.classList.remove('active');
    ticketSection.classList.remove('d-none');
    faqSection.classList.add('d-none');
});

btnFaq.addEventListener('click', () => {
    btnFaq.classList.add('active');
    btnTicket.classList.remove('active');
    faqSection.classList.remove('d-none');
    ticketSection.classList.add('d-none');
});