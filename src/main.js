import Vue from 'vue'
import App from './App.vue'
import axios from 'axios';

window.Axios = axios;
window.ws = new WebSocket("ws://localhost:9001/");
window.ws.addEventListener("message", function(e) {
  console.log(e);
});

    Vue.config.productionTip = false;

new Vue({
  render: h => h(App),
}).$mount('#app');
