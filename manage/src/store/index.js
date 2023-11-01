import Vuex from 'vuex'
import Vue from 'vue'
Vue.use(Vuex)

const state = {
    islogin: false,
    id: '',
    menu_id: '',
    info: [],
    routeArray: [],
    hasRoute: false,
    menuArray: []
}

const actions = {
}

const mutations = {
    setIslogin: (state, islogin) => {
        state.islogin = islogin;
        window.sessionStorage.setItem('islogin', JSON.stringify(islogin));
    },
    setId: (state, id) => {
        state.id = id;
        window.sessionStorage.setItem('id', JSON.stringify(id));
    },
    setMenuId: (state, menuId) => {
        state.menuId = menuId;
        window.sessionStorage.setItem('menuId', JSON.stringify(menuId));
    },
    setInfo: (state, info) => {
        state.info = info;
        window.sessionStorage.setItem('info', JSON.stringify(info));
    },
    setRouteArray: (state, route) => {
        state.routeArray = route;
        window.sessionStorage.setItem('routeArray', JSON.stringify(route));
    },
    setHasRoute: (state, bool) => {
        state.hasRoute = bool;
        window.sessionStorage.setItem('hasRoute', JSON.stringify(bool));
    },
    setMenuArray: (state, menuArray) => {
        state.menuArray = menuArray;
        window.sessionStorage.setItem('menuArray', JSON.stringify(menuArray));
    }
}

const getters = {
    islogin: state => {
        let islogin = state.islogin;
        if (!islogin) {
            islogin = JSON.parse(window.sessionStorage.getItem('islogin') || false);
        }
        return islogin;
    },
    id: state => {
        let id = state.id;
        if (!id) {
            id = JSON.parse(window.sessionStorage.getItem('id') || null);
        }
        return id;
    },
    menuId: state => {
        let menuId = state.menuId;
        if (!menuId) {
            menuId = JSON.parse(window.sessionStorage.getItem('menuId') || null);
        }
        return menuId;
    },
    info: state => {
        let info = state.info;
        if (!info) {
            info = JSON.parse(window.sessionStorage.getItem('info') || null);
        }
        return info;
    },
    routeArray: state => {
        let routeArray = state.routeArray;
        if (!routeArray) {
            routeArray = JSON.parse(window.sessionStorage.getItem('routeArray') || null);
        }
        return routeArray;
    },
    hasRoute: state => {
        let bool = state.hasRoute;
        if (!hasRoute) {
            hasRoute = JSON.parse(window.sessionStorage.getItem('hasRoute') || false);
        }
        return hasRoute;
    },
    menuArray: state => {
        let menuArray = state.menuArray;
        if (!menuArray) {
            menuArray = JSON.parse(window.sessionStorage.getItem('menuArray') || null);
        }
        return menuArray;
    }
}

export default new Vuex.Store({
    actions,
    mutations,
    state,
    getters
})