import Vuex from 'vuex'
import Vue from 'vue'
Vue.use(Vuex)

const state = {
    isLogin: false,
    userInfo: null,
    classOption:[],
    shoppingCart:[],
    carousel:[]
}

const actions = {
}

const mutations = {
    setIsLogin: (state, isLogin) => {
        state.isLogin = isLogin;
        window.sessionStorage.setItem('isLogin', JSON.stringify(isLogin));
    },
    setUserInfo: (state, userInfo) => {
        state.userInfo = userInfo;
        window.sessionStorage.setItem('userInfo', JSON.stringify(userInfo));
    },
    setClassOption: (state, classOption) => {
        state.classOption = classOption;
        window.sessionStorage.setItem('classOption', JSON.stringify(classOption));
    },
    setShoppingCart: (state, shoppingCart) => {
        state.shoppingCart = shoppingCart;
        window.sessionStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));
    },
    setCarousel: (state, carousel) => {
        state.carousel = carousel;
        window.sessionStorage.setItem('carousel', JSON.stringify(carousel));
    },
}

const getters = {
    isLogin: state => {
        let isLogin = state.isLogin;
        if (!isLogin) {
            isLogin = JSON.parse(window.sessionStorage.getItem('isLogin') || false);
        }
        return isLogin;
    },
    userInfo: state => {
        let userInfo = state.userInfo;
        if (!userInfo) {
            userInfo = JSON.parse(window.sessionStorage.getItem('userInfo') || null);
        }
        return userInfo;
    },
    classOption: state => {
        let classOption = state.classOption;
        if (!classOption || classOption.length==0) {
            classOption = JSON.parse(window.sessionStorage.getItem('classOption')) || [];
        }
        return classOption;
    },
    shoppingCart: state => {
        let shoppingCart = state.shoppingCart;
        if (!shoppingCart || shoppingCart.length == 0) {
            shoppingCart = JSON.parse(window.sessionStorage.getItem('shoppingCart')) || [];
        }
        return shoppingCart;
    },
    carousel: state => {
        let carousel = state.carousel;
        if (!carousel || carousel.length == 0) {
            carousel = JSON.parse(window.sessionStorage.getItem('carousel')) || [];
        }
        return carousel;
    },
}

export default new Vuex.Store({
    actions,
    mutations,
    state,
    getters
})