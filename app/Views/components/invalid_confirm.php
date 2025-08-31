<div id="global-invalid-confirm" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9999; background:rgba(0,0,0,0.12); align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; border-radius:22px; box-shadow:0 2px 16px rgba(0,0,0,0.08); padding:38px 32px; min-width:320px; max-width:400px; display:flex; flex-direction:column; align-items:center;">
        <div style="font-size:22px; font-weight:600; margin-bottom:12px;">Warning</div>
        <div style="margin-bottom:18px;">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                <circle cx="40" cy="40" r="36" stroke="#7A161C" stroke-width="4"/>
                <line x1="26" y1="26" x2="54" y2="54" stroke="#7A161C" stroke-width="4" stroke-linecap="round"/>
                <line x1="54" y1="26" x2="26" y2="54" stroke="#7A161C" stroke-width="4" stroke-linecap="round"/>
            </svg>
        </div>
        <div id="global-invalid-message" style="font-size:16px; color:#222; text-align:center; margin-bottom:18px;">
            Invalid input detected. Check your data and try again.
        </div>
        <button onclick="closeGlobalInvalid()" style="background:#7A161C; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Close</button>
    </div>
</div>
<script>
function showGlobalInvalid(msg) {
    document.getElementById('global-invalid-message').innerText = msg || 'Invalid input detected. Check your data and try again.';
    document.getElementById('global-invalid-confirm').style.display = 'flex';
}
function closeGlobalInvalid() {
    document.getElementById('global-invalid-confirm').style.display = 'none';
}
</script>