<style>
    #global-success-confirm-bg {
        display: none;
        position: fixed;
        z-index: 9998;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.18);
        backdrop-filter: blur(2px);
    }
    #global-success-confirm {
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
</style>
<div id="global-success-confirm-bg"></div>
<div id="global-success-confirm">
    <div style="font-size:22px; font-weight:600; margin-bottom:12px;">Success</div>
    <div style="margin-bottom:18px;">
        <svg width="64" height="64" viewBox="0 0 64 64" style="margin-bottom:12px;">
            <circle cx="32" cy="32" r="30" fill="none" stroke="#2DE1C2" stroke-width="4"/>
            <polyline points="18,34 30,46 46,22" fill="none" stroke="#2DE1C2" stroke-width="4"/>
        </svg>
        <div id="global-success-message" style="font-size:16px; color:#444;">Success! Your changes have been saved.</div>
    </div>
</div>