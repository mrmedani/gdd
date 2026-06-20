import './bootstrap';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

const toastColors = {
    success: { light: '#16a34a', dark: '#22c55e' },
    error:   { light: '#dc2626', dark: '#ef4444' },
    warning: { light: '#d97706', dark: '#f59e0b' },
    info:    { light: '#2563eb', dark: '#3b82f6' },
};

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function showToast(message, type = 'success', duration = 3000) {
    const colors = toastColors[type] || toastColors.info;
    const bg = isDark() ? colors.dark : colors.light;

    Toastify({
        text: message,
        duration,
        close: true,
        gravity: 'top',
        position: 'right',
        stopOnFocus: true,
        style: { background: bg },
        className: `toastify-${type}`,
    }).showToast();
}

document.addEventListener('livewire:init', () => {
    Livewire.on('notify', (data) => {
        showToast(data.message, data.type, data.duration);
    });
});

window.showToast = showToast;
