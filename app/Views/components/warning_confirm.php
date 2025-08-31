<style>
    #global-warning-confirm-bg {
        display: none;
        position: fixed;
        z-index: 9998;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.18);
        backdrop-filter: blur(2px);
    }
    #global-warning-confirm {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 50%; top: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.08);
        padding: 32px 40px;
        min-width: 320px;
        text-align: center;
    }
    .warning-btn {
        background: #234be7;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 8px 32px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin: 0 8px;
    }
    .warning-btn.cancel {
        background: #bbb;
    }
</style>
<div id="global-warning-confirm-bg"></div>
<div id="global-warning-confirm">
    <div style="font-size:22px; font-weight:600; margin-bottom:12px;">Warning</div>
    <div id="global-warning-message" style="margin-bottom:18px; font-size:16px; color:#444;">
        Are you sure want to submit the data?
    </div>
    <div style="display:flex; justify-content:center; gap:16px;">
        <button class="warning-btn cancel" id="global-warning-cancel-btn">Cancel</button>
        <button class="warning-btn" id="global-warning-ok-btn">OK</button>
    </div>
</div>