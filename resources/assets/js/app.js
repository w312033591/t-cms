require('./bootstrap');
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-default/index.css';
import App from './App.vue';
import router from './router'
Vue.use(ElementUI);

new Vue(Vue.util.extend({ router }, App)).$mount('#app');