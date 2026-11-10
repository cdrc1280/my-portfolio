import '../css/app.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Home from './Pages/Public/Home.vue';

const app = createApp(Home);
const pinia = createPinia();

app.use(pinia);
app.mount('#app');
