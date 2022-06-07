/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

window.Vue = require('vue').default;

import VueKonva from 'vue-konva';
Vue.use(VueKonva);

import Notifications from 'vue-notification';
Vue.use(Notifications);

// import Pusher from 'pusher-js';
window.Pusher = require('pusher-js');

// import Echo from 'laravel-echo';
// window.Echo = new Echo({
// 	broadcaster: process.env.MIX_PUSHER_APP_BROADCASTER,
// 	key: process.env.MIX_PUSHER_APP_KEY,
// 	cluster: process.env.MIX_PUSHER_APP_CLUSTER,
// 	wsHost: process.env.MIX_PUSHER_APP_WS_HOST,
// 	wsPort: parseInt(process.env.MIX_PUSHER_APP_WS_PORT),
// 	forceTLS: process.env.MIX_PUSHER_APP_FORCE_TLS == 'true' ? true : false,
// 	disableStats: process.env.MIX_PUSHER_APP_DISABLE_STATS == 'true' ? true : false,
// });

/**
* The following block of code may be used to automatically register your
* Vue components. It will recursively scan this directory for the Vue
* components and automatically register them with their "basename".
*
* Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
*/

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('board-tools', require('./components/ToolsComponent.vue').default);
Vue.component('board-zoom', require('./components/ZoomComponent.vue').default);
Vue.component('canvas-board', require('./components/BoardComponent.vue').default);
Vue.component('board-text', require('./components/nodes/TextNode.vue').default);
Vue.component('board-text-bootstrap', require('./components/bootstrap/TextNodeBootstrap.vue').default);
Vue.component('board-image', require('./components/nodes/ImageNode.vue').default);
Vue.component('board-image-bootstrap', require('./components/bootstrap/ImageNodeBootstrap.vue').default);
Vue.component('board-transformer', require('./components/nodes/TransformerNode.vue').default);
// Vue.component('board-image', require('./components/ImageComponent.vue').default);
// Vue.component('board-image-loader', require('./components/ImagesLoaderComponent.vue').default);

/**
* Next, we will create a fresh Vue application instance and attach it to
* the page. Then, you may begin adding components to this application
* or customize the JavaScript scaffolding to fit your unique needs.
*/

const app = new Vue({
	el: '#app',
});
