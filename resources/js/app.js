import { createApp } from 'vue';
// import App from './App.vue';
import router from './router';
import '../css/app.css'; // 
// import './bootstrap';
import { createPinia } from 'pinia';
import * as lucide from 'lucide-vue-next';




const app = createApp({
    template: '<router-view></router-view>'
});

Object.entries(lucide).forEach(([name, component]) => {
    app.component(name, component);
  });
  
app.use(router);
app.use(createPinia());
app.mount('#app');
