import App from './components/App.vue'
import {createApp} from 'vue'
import store from './store/store'

createApp(App).use(store).mount('#vue')
