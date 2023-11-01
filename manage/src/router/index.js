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
            name: 'Login',
            component: resolve => require(['@/view/Login.vue'], resolve)
        },
        {
            path: '/Home',
            name: 'Home',
            redirect: '/Home/Main',
            component: resolve => require(['@/layout/Home.vue'], resolve),
            meta: {
                requireAuth: true,
                name: '主页'
            },
            children: [
                {
                    path: '/Home/Main',
                    name: 'Main',
                    component: resolve => require(['@/view/Main.vue'], resolve),
                    meta: {
                        requireAuth: true,
                        name: '主页'
                    },
                }
            ]
        }
    ]
})
const router = createRouter();
export function resetRouter() {
    const newRouter = createRouter();
    router.matcher = newRouter.matcher;
}
export default router;