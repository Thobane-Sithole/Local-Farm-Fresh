import './bootstrap';

import Alpine from 'alpinejs';
import { animate, inView, press } from 'motion';

// ─── Alpine ──────────────────────────────────────────────────────────────────
window.Alpine = Alpine;

Alpine.store('darkMode', {
    on: document.documentElement.classList.contains('dark'),
    toggle() {
        this.on = !this.on;
        document.documentElement.classList.toggle('dark', this.on);
        try { localStorage.setItem('theme', this.on ? 'dark' : 'light'); } catch {}
    },
});

Alpine.start();

// ─── Page loader: Motion spring upgrade ──────────────────────────────────────
// The loader HTML + triggering live in the Blade layout. Once app.js loads,
// motionLoaderShow/Hide are registered so subsequent navigations use Motion.
const ld = document.getElementById('page-loader');
const ls = document.getElementById('loader-svg');
const lw = document.getElementById('loader-wordmark');

if (ld && ls && lw) {
    // Let Motion own the animation — remove the CSS transitions
    ls.style.transition = 'none';
    lw.style.transition = 'none';

    window.motionLoaderShow = () => {
        ld.style.opacity = '1';
        ld.style.visibility = 'visible';
        ld.style.pointerEvents = 'auto';
        ld.classList.add('loader-active');
        animate(ls, { scale: [0.55, 1] }, { type: 'spring', bounce: 0.5, duration: 0.6 });
        animate(lw, { opacity: [0, 1], y: [12, 0] }, { duration: 0.3, delay: 0.28, ease: 'easeOut' });
    };

    window.motionLoaderHide = () => {
        animate(ld, { opacity: 0 }, { duration: 0.18, ease: 'easeOut' }).then(() => {
            ld.style.visibility = 'hidden';
            ld.style.pointerEvents = 'none';
            ld.classList.remove('loader-active');
        });
    };
}

// ─── Card entrance animations ─────────────────────────────────────────────────
// Each .rounded-card fades + slides up as it enters the viewport.
inView('.rounded-card', ({ target }) => {
    animate(
        target,
        { opacity: [0, 1], y: [20, 0] },
        { duration: 0.45, ease: 'easeOut' }
    );
}, { amount: 0.15 });

// ─── Flash / alert entrance ───────────────────────────────────────────────────
document.querySelectorAll('[role="alert"], [role="status"]').forEach(el => {
    if (el.id === 'page-loader') return;
    animate(el, { opacity: [0, 1], y: [-10, 0] }, { duration: 0.35, ease: 'easeOut' });
});

// ─── Button press (spring feedback) ──────────────────────────────────────────
// Applies to <button> elements and <a> link-buttons (inline-flex).
// Skips disabled controls and Alpine-controlled buttons (dark mode toggle).
document.querySelectorAll(
    'button:not([disabled]):not([x-data]), a.inline-flex[href]'
).forEach(el => {
    press(el, () => {
        animate(el, { scale: 0.95 }, { type: 'spring', stiffness: 600, damping: 30 });
        return () => animate(el, { scale: 1 }, { type: 'spring', stiffness: 400, damping: 25 });
    });
});
