window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

 import Echo from 'laravel-echo';

 window.Pusher = require('pusher-js');

 window.Echo = new Echo({
     broadcaster: 'pusher',
     key: process.env.MIX_PUSHER_APP_KEY,
     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
     forceTLS: true
 });

  Echo.private('ambulanceCallup')
    .listen('AmbulanceCallup', (e) => {
        console.log(e.callup.pick_up_location);
        console.log(e.callup.pick_up_address);
        console.log(e.callup.hospital_location);
        console.log(e.callup.hospital);
        console.log(e.partners);
});

 Echo.private('chat')
    .listen('MessageSent', (e) => {
        console.log(e.sender);
        console.log(e.receiver);
        console.log(e.message);
        console.log(e.message.body);
});
