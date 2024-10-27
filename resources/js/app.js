import './bootstrap';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.MIX_PUSHER_APP_KEY,
    cluster: import.meta.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    forceTLS: false,
    disableStats: true,
});


