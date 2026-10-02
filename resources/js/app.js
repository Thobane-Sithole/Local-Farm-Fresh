import './bootstrap';

import Alpine from 'alpinejs';

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
