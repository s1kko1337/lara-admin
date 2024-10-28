import './bootstrap';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    cluster: 'mt1',
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,  
    forceTLS: false,
    disableStats: true,
    encrypted: false,
});


