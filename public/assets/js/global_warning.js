function showGlobalWarning(message = 'Are you sure want to submit the data?', onOk = null, onCancel = null) {
    document.getElementById('global-warning-message').innerText = message;
    document.getElementById('global-warning-confirm-bg').style.display = 'block';
    document.getElementById('global-warning-confirm').style.display = 'block';

    // Remove previous listeners
    var okBtn = document.getElementById('global-warning-ok-btn');
    var cancelBtn = document.getElementById('global-warning-cancel-btn');
    okBtn.onclick = function() {
        document.getElementById('global-warning-confirm').style.display = 'none';
        document.getElementById('global-warning-confirm-bg').style.display = 'none';
        if (typeof onOk === 'function') onOk();
    };
    cancelBtn.onclick = function() {
        document.getElementById('global-warning-confirm').style.display = 'none';
        document.getElementById('global-warning-confirm-bg').style.display = 'none';
        if (typeof onCancel === 'function') onCancel();
    };
}