function showGlobalSuccess(message = 'Success! Your changes have been saved.') {
    document.getElementById('global-success-message').innerText = message;
    document.getElementById('global-success-confirm-bg').style.display = 'block';
    document.getElementById('global-success-confirm').style.display = 'block';
    setTimeout(function() {
        document.getElementById('global-success-confirm').style.display = 'none';
        document.getElementById('global-success-confirm-bg').style.display = 'none';
    }, 2000);
}