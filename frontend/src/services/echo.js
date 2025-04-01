import Echo from 'laravel-echo';

const echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.VUE_APP_PUSHER_APP_KEY,
    wsPort: 8080, // Reverb default portu
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

export { echo };
