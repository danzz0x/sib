import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// --- IMPRIMIR VALORES EN CONSOLA (PARA DEPURAR) ---
console.log('--- DEPURANDO PUSHER ---');
console.log('KEY:', import.meta.env.VITE_PUSHER_APP_KEY);
console.log('CLUSTER:', import.meta.env.VITE_PUSHER_APP_CLUSTER);
// --------------------------------------------------

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
