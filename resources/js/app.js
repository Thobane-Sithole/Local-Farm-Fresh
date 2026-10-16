import './bootstrap';

import Alpine from 'alpinejs';
import { animate, inView, press, hover, stagger } from 'motion';

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
const ld = document.getElementById('page-loader');
const ls = document.getElementById('loader-svg');
const lw = document.getElementById('loader-wordmark');

if (ld && ls && lw) {
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

// ─── Hero text — staggered entrance ──────────────────────────────────────────
// Targets children of any element with class="motion-hero"
const heroItems = document.querySelectorAll('.motion-hero > *');
if (heroItems.length) {
    animate(
        heroItems,
        { opacity: [0, 1], y: [28, 0] },
        {
            duration: 0.55,
            ease: [0.22, 1, 0.36, 1], // easeOutQuint — expressive for large type
            delay: stagger(0.13),
        }
    );
}

// ─── Staggered grids — [data-grid] ───────────────────────────────────────────
// Add data-grid to any <ul> or <div> grid to get staggered child entrances.
inView('[data-grid]', ({ target }) => {
    animate(
        Array.from(target.children),
        { opacity: [0, 1], y: [20, 0] },
        { duration: 0.38, ease: 'easeOut', delay: stagger(0.07) }
    );
}, { amount: 0.05 });

// ─── Standalone section cards (product form panels, dashboard panels) ─────────
// <section class="rounded-card"> — not a grid item, no conflict with [data-grid]
inView('section.rounded-card', ({ target }) => {
    animate(target, { opacity: [0, 1], y: [16, 0] }, { duration: 0.4, ease: 'easeOut' });
}, { amount: 0.1 });

// ─── Product card hover lift ──────────────────────────────────────────────────
// <a class="... rounded-card ..."> in the shop grid — spring lift on hover
document.querySelectorAll('a[href].rounded-card').forEach(card => {
    hover(card, () => {
        animate(card, { y: -6 }, { type: 'spring', stiffness: 350, damping: 22 });
        return () => animate(card, { y: 0 }, { type: 'spring', stiffness: 350, damping: 22 });
    });
});

// ─── Table row stagger ────────────────────────────────────────────────────────
// All dashboard / admin tables get row-by-row entrance
inView('table tbody', ({ target }) => {
    animate(
        Array.from(target.querySelectorAll('tr')),
        { opacity: [0, 1], y: [8, 0] },
        { duration: 0.28, ease: 'easeOut', delay: stagger(0.04) }
    );
}, { amount: 0.05 });

// ─── Flash / alert entrance ───────────────────────────────────────────────────
document.querySelectorAll('[role="alert"], [role="status"]').forEach(el => {
    if (el.id === 'page-loader') return;
    animate(el, { opacity: [0, 1], y: [-10, 0] }, { duration: 0.35, ease: 'easeOut' });
});

// ─── Button press (spring tactile feedback) ───────────────────────────────────
document.querySelectorAll(
    'button:not([disabled]):not([x-data]), a.inline-flex[href]'
).forEach(el => {
    press(el, () => {
        animate(el, { scale: 0.95 }, { type: 'spring', stiffness: 600, damping: 30 });
        return () => animate(el, { scale: 1 }, { type: 'spring', stiffness: 400, damping: 25 });
    });
});
