window._ = require('lodash');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;
Pusher.logToConsole = false;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: process.env.MIX_LARAVEL_WEBSOCKETS_HOST || '127.0.0.1',
    wsPort: process.env.MIX_LARAVEL_WEBSOCKETS_PORT || 6001,
    wssPort: process.env.MIX_LARAVEL_WEBSOCKETS_PORT || 6001,
    forceTLS: true,
    encrypted: true,
    disableStats: true,
    enabledTransports: ['wss'],
});
