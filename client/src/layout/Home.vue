<template>
    <div>
        <el-container>
            <el-header class="header">
                <div class="head-top">
                    <div class="top-box">
                        <div v-if="isLogin && userInfo.type == 1" class="top-item">
                            <i class="ri-store-line" style="font-size: 18px;"></i>
                            <div>我的店铺</div>
                        </div>
                        <div v-if="!isLogin" class="top-item" @click="toLogin">
                            <el-avatar icon="el-icon-user-solid" :size="30"></el-avatar>
                            <div>登录</div>
                        </div>
                        <div v-else class="top-item" style="color: #909399;" @click="toMy">
                            <el-avatar v-if="userInfo.avatar" :src="userInfo.avatar" :size="30"></el-avatar>
                            <el-avatar v-else icon="el-icon-user-solid" :size="30"></el-avatar>
                            <div style="max-width: 100px;overflow: hidden;text-overflow: ellipsis;">{{ userInfo.nick_name }}
                            </div>
                        </div>
                        <div class="top-item">
                            <i class="el-icon-bell" style="font-size: 18px;"></i>
                            <div>我的通知</div>
                        </div>
                        <div class="top-item" @click="goTo({ path: '/User', query: { active: 1 } })">
                            <i class="el-icon-document" style="font-size: 18px;"></i>
                            <div>我的订单</div>
                        </div>
                        <div class="shopping-cart" @click="toShoppingCart">
                            <i class="el-icon-shopping-cart-2" style="font-size: 18px;"></i>
                            <el-badge v-if="shoppingCart.length > 0" :value="shoppingCart.length" :max="10">
                                <div>购物车</div>
                            </el-badge>
                            <div v-else>购物车(0)</div>
                        </div>
                    </div>
                </div>
            </el-header>
            <el-main class="main"
                style="width: 100%;margin: 0;padding: 0;box-sizing: border-box;background-color:rgba(227,228,229,0.2);box-sizing: border-box;">
                <div style="width: 100%;background-color: #FFFFFF;">
                    <div class="head-menu">
                        <img src="@/assets/yongtao.png" style="height: 150px;margin-left: 230px;" alt="">
                        <div style="height: 150px;display: flex;flex-direction: column;margin-left: 70px;">
                            <el-input placeholder="请输入内容" v-model="searh_key" style="width: 400px;margin-top: 40px;">
                                <el-button style="background-color: red;border-radius: 0;color: whitesmoke;" slot="append"
                                    @click="toSearch">搜索</el-button>
                            </el-input>
                            <el-menu :default-active="active" class="el-menu-demo" mode="horizontal" @select="handleSelect"
                                router>
                                <el-menu-item index="/">首页</el-menu-item>
                                <el-menu-item index="/Class">产品分类</el-menu-item>
                                <el-menu-item index="/Panic">限时秒杀</el-menu-item>
                                <el-menu-item index="/Coupon">领券中心</el-menu-item>
                            </el-menu>
                        </div>

                    </div>
                </div>
                <transition name="el-fade-in">
                    <router-view></router-view>
                </transition>
                <div style="position: fixed;width: 60px;right: 0px;top: 45%;background-color: #fff;z-index: 99999;
                        border-top-left-radius: 10px;border-bottom-left-radius: 10px;box-sizing: border-box;padding-top: 15px;
                        padding-bottom: 15px;box-shadow: 0 3px 20px rgba(58,58,58, 0.1);">
                    <div style="display: flex;flex-direction: column;align-items: center;width: 60px;height: 53px;font-size: 12px;
                        color: #303133;border-radius: 10px;cursor: pointer;" @click="goTo({ path: '/User', query: { active: 2 } })">
                        <div style="font-size: 25px;">
                            <i class="ri-star-line"></i>
                        </div>
                        <div>
                            收藏夹
                        </div>
                    </div>
                    <div style="display: flex;flex-direction: column;align-items: center;width: 60px;height: 53px;font-size: 12px;
                        color: #303133;border-radius: 10px;cursor: pointer;" @click="toShoppingCart">
                        <div style="font-size: 25px;">
                            <i class="ri-shopping-cart-2-line"></i>
                        </div>
                        <div>
                            购物车
                        </div>
                    </div>
                    <div style="display: flex;flex-direction: column;align-items: center;width: 60px;height: 53px;font-size: 12px;
                        color: #303133;border-radius: 10px;cursor: pointer;" @click="toShoppingCart">
                        <div style="font-size: 25px;">
                            <i class="ri-store-2-line"></i>
                        </div>
                        <div>
                            免费开店
                        </div>
                    </div>
                    <div v-if="toTop" class="show-item" style="display: flex;flex-direction: column;align-items: center;width: 60px;height: 53px;font-size: 12px;
                        color: #303133;border-radius: 10px;cursor: pointer;margin-top: 5px;" @click="retureTop()">
                        <div style="font-size: 25px;">
                            <i class="ri-rocket-line"></i>
                        </div>
                        <div>
                            回顶部
                        </div>
                    </div>
                </div>
            </el-main>
            <el-footer
                style="background-color: #E3E4E5;width: 100%;height: auto !important;font-size: 13px;color: #909399;padding: 20px;box-sizing: border-box;">
                <div style="width: 1250px;margin: 0 auto;">
                     <div style="width: 100%;text-align: center;margin-top: 5px;">
                        关于 | 帮助 | 条款 | 反馈
                    </div>
                    <div style="width: 100%;text-align: center;margin-top: 5px;">
                        © 2023 By Tao
                    </div>
                </div>
            </el-footer>
        </el-container>
    </div>
</template>
<script>
import { post } from '@/api/MyAxios';
import { mapGetters } from 'vuex';
export default {
    name: 'Home',
    data() {
        return {
            searh_key: '',
            active: '/',
            toTop: false
        }
    },
    computed: {
        ...mapGetters([
            'isLogin',
            'userInfo',
            'shoppingCart'
        ])
    },
    watch: {
        $route(val, old) {
            this.active = this.$route.path
        },
    },
    mounted() {
        this.active = this.$route.path
        this.getCarousel()
        window.addEventListener('scroll', this.showToTop)
    },
    methods: {
        getCarousel() {
            let params = new FormData()
            params.append('status', 1)
            post('Carousel/get', params).then(res => {
                if (res.code == 200) {
                    this.$store.commit('setCarousel', res.data.records)
                }
            })
        },
        showToTop() {
            let scrollTop = document.documentElement.scrollTop
            let clientHeight = document.documentElement.clientHeight
            if (scrollTop + 200 > clientHeight) {
                this.toTop = true
            }
            else {
                this.toTop = false
            }
        },
        handleSelect(index) {
            this.active = index;
        },
        toLogin() {
            this.$router.push('/Login');
        },
        toMy() {
            this.$router.push('/User');
        },
        retureTop() {
            this.animateScroll(window, 0)
        },
        animateScroll(obj, target) {
            clearInterval(obj.timer)
            obj.timer = setInterval(function () {
                let location = window.scrollY - 50
                if (location <= 0) {
                    clearInterval(obj.timer)
                    return
                }
                window.scrollTo(0, location)
            }, 5)
        },
        judgeLogin() {
            if (!this.isLogin) {
                this.$router.push('/Login')
            }
        },
        toShoppingCart() {
            if (!this.isLogin) {
                this.$router.push('/Login')
            }
            this.$router.push('/ShoppingCart')
        },
        toSearch() {
            this.$router.push({
                path: '/Search',
                query: {
                    key_words: this.searh_key
                }
            })
        },
        goTo(params) {
            this.$router.push(params)
        }
    }
}
</script>
<style lang="scss" scoped>
@mixin row {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}

@mixin column {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

::v-deep .el-menu--horizontal>.el-menu-item.is-active {
    border-bottom-color: #E83927 !important;
}

::v-deep .el-menu-item {
    padding: 0 !important;
    margin-left: 20px !important;
    margin-right: 20px !important;
}

.el-menu.el-menu--horizontal {
    border: none;
}

.show-item {
    animation-name: show;
    animation-duration: 0.3s;
    overflow: hidden;
}

@keyframes show {
    from {
        height: 0px;

    }

    to {
        height: 53px;
    }
}

.header {
    width: 100%;
    background: #E3E4E5;
    height: 45px !important;

    .head-top {
        height: 45px;
        width: 1250px;
        margin: 0 auto;
        display: flex;
        flex-direction: row-reverse;

        .top-box {
            @include row();

            .top-item {
                height: 100%;
                padding-left: 10px;
                padding-right: 10px;
                @include row();
                color: #909399;

                div {
                    margin-left: 3px;
                    font-size: 12px;
                }

                &:hover {
                    cursor: pointer;
                    color: #F56C6C !important;
                }
            }

            .shopping-cart {
                height: 100%;
                padding-left: 30px;
                padding-right: 30px;
                background-color: rgba(40, 40, 40, 0.9);
                @include row();
                color: whitesmoke;

                div {
                    margin-left: 3px;
                    font-size: 12px;
                }

                &:hover {
                    cursor: pointer;
                    color: #F56C6C;
                }
            }
        }
    }


}

.head-menu {
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 1200px;
    height: 150px;
    margin: 0 auto;
}

.el-zoom-in-top-in-leave-to {
    display: none !important;
}

.el-fade-in-leave-to {
    display: none !important;
}

.main::-webkit-scrollbar {
    display: none;
}
</style>
