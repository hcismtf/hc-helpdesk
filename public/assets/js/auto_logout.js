let idleTimeout;
function resetIdleTimer() {
    clearTimeout(idleTimeout);
    idleTimeout = setTimeout(() => {
        window.location.href = '/admin/logout';
    }, 5 * 60 * 1000); // 5 menit
}
['mousemove', 'keydown', 'scroll', 'click'].forEach(evt => {
    window.addEventListener(evt, resetIdleTimer);
});
resetIdleTimer();