import Vue from 'vue'
import App from './App.vue'
import Vuex from 'vuex';
import store from './store';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import AFTableColumn from 'af-table-column';
import router from './router';
import { handle_routes, add_routes } from './utils/loadRoute'
import { getMenu } from './api';
Vue.config.productionTip = false
Vue.use(ElementUI);
Vue.use(Vuex);
Vue.use(AFTableColumn);

router.beforeEach((to, from, next) => {
  if (to.meta.requireAuth) {
    if (sessionStorage.getItem('islogin')) {
      if (store.state.hasRoute) {
        next();
        return;
      }
      let routeArray = JSON.parse(window.sessionStorage.getItem('routeArray'));
      if (routeArray) {
        add_routes(routeArray);
        store.commit('setHasRoute', true);
        next({ path: to.path });
        return;
      }
      let params = new URLSearchParams();
      params.append("group_id", store.state.info.group_id);
      getMenu(params).then(res => {
        if (res.code == 200) {
          let finalRoutes = handle_routes(res.data);
          add_routes(finalRoutes);
          store.commit('setRouteArray', finalRoutes);
          store.commit('setHasRoute', true);
        }
        else {
          this.$message({
            message: res.msg,
            type: 'error'
          });
        }
        next({ path: to.path });
        return;
      })
      next();
    }
    else {
      next({ path: '/' });
    }
  }
  else {
    let routeArray = JSON.parse(window.sessionStorage.getItem('routeArray'));
    if (routeArray) {
      add_routes(routeArray);
      store.commit('setHasRoute', true);
      next({ path: to.path });
      return;
    }
    next();
  }
})
new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')