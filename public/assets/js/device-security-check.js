// Device Security Detection Script
(function() {
    // Deteksi Android Root
    function detectAndroidRoot() {
        // Check common root packages via user-agent dan environment
        const suPaths = [
            '/system/bin/su',
            '/system/xbin/su',
            '/data/local/tmp/su',
            '/data/local/su',
            '/system/bin/busybox',
        ];

        // Deteksi via performance timing (emulator ciri khas)
        const perfNow = performance.now();
        const isEmulator = perfNow < 100;

        // Deteksi via developer mode (chrome remote debugging)
        const isDeveloperMode = (typeof window.__REACT_DEVTOOLS_GLOBAL_HOOK__ !== 'undefined') ||
                              (typeof window.__REDUX_DEVTOOLS_EXTENSION__ !== 'undefined');

        return {
            is_rooted: false, // Dari client-side sulit detect, rely on server
            is_emulator: isEmulator,
            developer_mode: isDeveloperMode,
            user_agent: navigator.userAgent
        };
    }

    // Deteksi iOS Jailbreak
    function detectIOSJailbreak() {
        const jailbreakIndicators = [
            '/Applications/Cydia.app',
            '/Applications/Sileo.app',
            '/Applications/Installer.app',
            '/Applications/Zebra.app',
            '/var/cache/apt',
            '/var/lib/apt',
        ];

        // Deteksi via WebRTC leak (tapi limited capability)
        const isDeveloperMode = (typeof window.__REACT_DEVTOOLS_GLOBAL_HOOK__ !== 'undefined');

        return {
            is_jailbroken: false, // Dari client-side sulit detect, rely on server
            developer_mode: isDeveloperMode,
            user_agent: navigator.userAgent
        };
    }

    // Main detection function
    function detectDeviceSecurity() {
        const isAndroid = /Android/.test(navigator.userAgent);
        const isIOS = /iPhone|iPad|iPod/.test(navigator.userAgent);

        let result = {
            is_rooted: false,
            is_jailbroken: false,
            is_emulator: false,
            developer_mode: false,
            user_agent: navigator.userAgent
        };

        if (isAndroid) {
            result = { ...result, ...detectAndroidRoot() };
        } else if (isIOS) {
            result = { ...result, ...detectIOSJailbreak() };
        }

        return result;
    }

    // Send detection result to server
    function sendDeviceCheck() {
        const deviceCheck = detectDeviceSecurity();

        // Cek apakah ada cookie atau session storage yang menunjukkan sudah di-check
        if (sessionStorage.getItem('device_check_sent')) {
            return;
        }

        // Buat form data
        const formData = new FormData();
        formData.append('device_check', JSON.stringify(deviceCheck));

        // Hanya kirim jika bukan halaman root /
        const currentPath = window.location.pathname;
        if (currentPath === '/' || currentPath === '') {
            // Untuk halaman root, cukup store di session, jangan kirim POST
            sessionStorage.setItem('device_check_sent', 'true');
            sessionStorage.setItem('device_check_data', JSON.stringify(deviceCheck));
            return;
        }

        // Kirim ke current page dengan POST
        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Device-Check': 'true'
            }
        }).then(response => {
            if (response.ok) {
                sessionStorage.setItem('device_check_sent', 'true');
            }
        }).catch(error => {
            console.error('Device check error:', error);
        });
    }

    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', sendDeviceCheck);
    } else {
        sendDeviceCheck();
    }
})();
