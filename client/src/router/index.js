import Vue from "vue";
import VueRouter from "vue-router";
import Router from "vue-router";

Vue.use(Router);
const originalPush = VueRouter.prototype.push;
VueRouter.prototype.push = function push(location) {
    return originalPush.call(this, location).catch(err => err);
}

const createRouter = () => new Router({
    routes: [
        {
            path: '/',
            name: 'Home',
            component: resolve => require(['@/layout/Home.vue'], resolve),
            redirect:'/',
            children:[
                {
                    path: '/',
                    name: 'Recommend',
                    component: resolve => require(['@/view/Recommend.vue'], resolve),
                },
                {
                    path: '/Class',
                    name: 'Class',
                    component: resolve => require(['@/view/Class.vue'], resolve),
                },
                {
                    path: '/Panic',
                    name: 'Panic',
                    component: resolve => require(['@/view/Panic.vue'], resolve),
                },
                {
                    path: '/Coupon',
                    name: 'Coupon',
                    component: resolve => require(['@/view/Coupon.vue'], resolve),
                },
                {
                    path: '/GoodsInfo',
                    name: 'GoodsInfo',
                    component: resolve => require(['@/view/GoodsInfo.vue'], resolve),
                },
                {
                    path: '/User',
                    name: 'User',
                    component: resolve => require(['@/view/User.vue'], resolve),
                    meta: {
                        requireAuth: true
                    }
                },
                {
                    path: '/ShoppingCart',
                    name: 'ShoppingCart',
                    component: resolve => require(['@/view/ShoppingCart.vue'], resolve),
                    meta: {
                        requireAuth: true
                    }
                },
                {
                    path: '/NoItem',
                    name: 'NoItem',
                    component: resolve => require(['@/view/NoItem.vue'], resolve),
                },
                {
                    path: '/Search',
                    name: 'Search',
                    component: resolve => require(['@/view/Search.vue'], resolve),
                },
            ]
        },
        {
            path: '/Login',
            name: 'Login',
            component: resolve => require(['@/view/Login.vue'], resolve),
        },
    ]
})
const router = createRouter();
export default router;