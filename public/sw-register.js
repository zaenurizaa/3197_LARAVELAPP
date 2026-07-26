// AmikomEventHub – PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });

            // Listen for updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                if (!newWorker) return;

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New content available — show a subtle toast
                        showUpdateToast();
                    }
                });
            });

            console.log('[PWA] Service Worker registered:', registration.scope);
        } catch (err) {
            console.warn('[PWA] Service Worker registration failed:', err);
        }
    });
}

// Install prompt (Add to Home Screen)
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Show install button if it exists on page
    const installBtn = document.getElementById('pwa-install-btn');
    if (installBtn) {
        installBtn.style.display = 'flex';
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log('[PWA] Install outcome:', outcome);
            deferredPrompt = null;
            installBtn.style.display = 'none';
        });
    }
});

window.addEventListener('appinstalled', () => {
    console.log('[PWA] App installed successfully!');
    deferredPrompt = null;
});

function showUpdateToast() {
    const toast = document.createElement('div');
    toast.innerHTML = `
        <div style="
            position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
            background:#1e1b4b; border:1px solid #6366f1; border-radius:12px;
            padding:12px 20px; color:#e2e8f0; font-family:Inter,sans-serif;
            font-size:13px; display:flex; align-items:center; gap:12px;
            box-shadow:0 8px 32px rgba(0,0,0,0.5); z-index:9999;
        ">
            <span>🔄 Update tersedia!</span>
            <button onclick="window.location.reload()" style="
                background:#6366f1; color:white; border:none; border-radius:8px;
                padding:4px 12px; cursor:pointer; font-size:12px; font-weight:600;
            ">Muat Ulang</button>
            <button onclick="this.closest('.pwa-toast').remove()" style="
                background:transparent; color:#94a3b8; border:none; cursor:pointer; font-size:16px;
            ">✕</button>
        </div>
    `;
    toast.className = 'pwa-toast';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 15000);
}
