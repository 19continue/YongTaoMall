import Vue from 'vue'
import App from './App.vue'
import Vuex from 'vuex';
import store from './store';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import 'remixicon/fonts/remixicon.css'
import router from './router';
Vue.use(ElementUI);
Vue.use(Vuex);
Vue.config.productionTip = false
router.beforeEach((to, from, next) => {
  if(to.meta.requireAuth) {
    if(!store.getters.isLogin){
      next({ path: '/Login',query:{redirect:to} });
    }
  }
  next();  
})

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
