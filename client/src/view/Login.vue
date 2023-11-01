<template>
    <div style="background-color: #fff;">
        <div style="width: 100%;height: 110px;">
            <div style="width: 1200px;margin: 0 auto;overflow: hidden;display: flex;align-items: center;">
                <el-image shape="square" style="width: 95px; height: 110px;cursor: pointer;" fit="cover"
                    :src="require('@/assets/yongtao.png')" @click="goTo('/')"></el-image>
                <div style="font-size: 28px;margin-left: 20px;cursor: pointer;" @click="goTo('/')">
                    涌涛商城
                </div>
                <div style="margin-left: auto;font-size: 16px;color: #666;">
                    <span class="iconfont font-color">
                        <i class="ri-shopping-cart-2-line"></i>
                    </span>
                    品种齐全
                </div>
                <div style="margin-left: 40px;">
                    <span class="iconfont font-color">
                        <i class="ri-coupon-2-line"></i>
                    </span>
                    低价畅选
                </div>
                <div style="margin-left: 40px;">
                    <span class="iconfont font-color">
                        <i class="ri-checkbox-line"></i>
                    </span>
                    正品行货
                </div>
            </div>
        </div>
        <div style="position: relative;width: 100%; height: 608px;min-width: 1200px;">
            <el-image shape="square" style="width: 100%; height: 608px;position: absolute;" fit="cover"
                :src="require('@/assets/login_bg.jpg')"></el-image>
            <div v-if="currentForm == 'login'"
                style="width: 450px;height: 427px;position: absolute;top: 91px;right: 360px;background-color: #fff;box-sizing: border-box;padding: 70px 0;">
                <el-tabs v-model="activeName" @tab-click="handleClick" style="width: 80%;margin: 0 auto;">
                    <el-tab-pane label="密码登录" name="password">
                        <div class="form-item">
                            <div class="label">
                                <i class="ri-account-pin-circle-fill"></i>
                            </div>
                            <input class="input" type="text" placeholder="请输入用户名" v-model="passwordLoginForm.loginName">
                        </div>
                        <div class="form-item">
                            <div class="label">
                                <i class="ri-lock-2-fill"></i>
                            </div>
                            <input class="input" style="width: 260px;" :type="passwordType" placeholder="请输入密码" v-model="passwordLoginForm.password">
                            <div
                                style="width: 30px;height: 100%;border-left: 1px solid #dbdbdb;line-height: 50px;text-align: center;">
                                <i v-show="passwordType == 'text'" class="ri-eye-fill"
                                    @click="passwordType = 'password'"></i>
                                <i v-show="passwordType == 'password'" class="ri-eye-off-fill"
                                    @click="passwordType = 'text'"></i>
                            </div>
                        </div>
                        <div class="login-button" @click="login">
                            立即登录
                        </div>
                        <div style="width: 100%;display: flex;justify-content: flex-end;margin-top: 10px;">
                            <span style="font-size: 14px;color: #606266;cursor: pointer;margin-right: 5px;" @click="turnTo('register')">免费注册</span>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="短信登陆" name="messsage">
                        <div class="form-item">
                            <div class="label" style="font-size: 16px;">
                                +86
                            </div>
                            <input class="input" type="text" placeholder="请输入手机号">
                        </div>
                        <div class="form-item">
                            <input class="input" style="width: 266px;" type="text" placeholder="请输入验证码">
                            <div style="width: 90px;height: 100%;line-height: 50px;color: #e93323;
                                border-left: 1px solid #dbdbdb;text-align: center;font-size: 14px;cursor: pointer;">
                                获取验证码
                            </div>
                        </div>
                        <div class="login-button">
                            立即登录
                        </div>
                        <div style="width: 100%;display: flex;justify-content: flex-end;margin-top: 10px;">
                            <span style="font-size: 14px;color: #606266;cursor: pointer;margin-right: 5px;" @click="turnTo('register')">免费注册</span>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
            <div v-if="currentForm=='register'"
                style="width: 450px;height: 427px;position: absolute;top: 91px;right: 360px;background-color: #fff;box-sizing: border-box;padding: 40px 0;padding-left: 45px;padding-right: 45px;z-index: 9999;">
                <div style="width: 100%;font-size: 18px;font-weight: 500;color: #303133;">
                    用户注册
                </div>
                <div class="form-item">
                    <div class="label">
                        <i class="ri-phone-fill"></i>
                    </div>
                    <input class="input" type="text" placeholder="请输入手机号" v-model="registerForm.phone">
                </div>
                <div class="form-item">
                    <div class="label">
                        <i class="ri-account-pin-circle-fill"></i>
                    </div>
                    <input class="input" type="text" placeholder="请输入用户名" v-model="registerForm.loginName">
                </div>
                <div class="form-item">
                    <div class="label">
                        <i class="ri-lock-2-fill"></i>
                    </div>
                    <input class="input" style="width: 260px;" :type="passwordType2" placeholder="请输入密码" v-model="registerForm.password">
                    <div
                        style="width: 30px;height: 100%;border-left: 1px solid #dbdbdb;line-height: 50px;text-align: center;">
                        <i v-show="passwordType2 == 'text'" class="ri-eye-fill" @click="passwordType2 = 'password'"></i>
                        <i v-show="passwordType2 == 'password'" class="ri-eye-off-fill" @click="passwordType2 = 'text'"></i>
                    </div>
                </div>
                <div class="login-button" @click="register">
                    立即注册
                </div>
                <div style="width: 100%;display: flex;justify-content: flex-end;margin-top: 10px;">
                    <span style="font-size: 14px;color: #606266;cursor: pointer;margin-right: 5px;" @click="turnTo('login')">返回登录</span>
                </div>
            </div>
        </div>
        <el-footer
            style="background-color: #fff;width: 1250px;height: auto !important;font-size: 13px;color: #909399;padding: 20px;box-sizing: border-box;">
            <div style="width: 100%;text-align: center;margin-top: 5px;">
                关于 | 帮助 | 条款 | 反馈
            </div>
            <div style="width: 100%;text-align: center;margin-top: 5px;">
                © 2023 By Tao
            </div>
        </el-footer>
    </div>
</template>

<script>
import { post } from '@/api/MyAxios'
export default {
    data() {
        return {
            activeName: 'password',
            passwordType: 'text',
            passwordType2: 'text',
            currentForm:'login',
            passwordLoginForm: {
                loginName: '',
                password: ''
            },
            messageLoginForm: {
                phone: '',
                loginName: '',
                password: ''
            },
            registerForm:{
                phone:'',
                loginName:'',
                password:''
            }
        }
    },
    methods: {
        handleClick(tab,e){
            this.activeName=tab.name
        },
        turnTo(to){
            this.currentForm=to
        },
        goTo(path){
            this.$router.push(path)
        },
        login(){
            let params = new FormData()
            params.append('login_name', this.passwordLoginForm.loginName)
            params.append('password', this.passwordLoginForm.password)
            post('User/login', params).then(res => {
                if (res.code == 200) {
                    this.$store.commit('setUserInfo',res.data)
                    this.$store.commit('setIsLogin', true)
                    this.getShoppingCart(res.data.uid)
                    this.$message.success(res.msg)
                    if(this.$route.query.redirect!==undefined){
                        this.$router.replace(this.$route.query.redirect)
                    }
                    else{
                        this.$router.back()
                    }
                }
                else {
                    this.$message.error(res.msg)
                }
            })
        },
        async getShoppingCart(uid) {
            let p = new FormData()
            p.append('uid', uid)
            await post('ShoppingCart/get', p).then(res => {
                if (res.code == 200) {
                    this.$store.commit('setShoppingCart', res.data.records)
                }
            })
        },
        register(){
            let params=new FormData()
            params.append('phone',this.registerForm.phone)
            params.append('login_name',this.registerForm.loginName)
            params.append('password',this.registerForm.password)
            post('User/register',params).then(res=>{
                if(res.code==200){
                    this.$message.success(res.msg)
                    this.registerForm.phone=''
                    this.registerForm.loginName=''
                    this.registerForm.password=''
                }
                else{
                    this.$message.error(res.msg)
                }
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.form-item {
    display: flex;
    align-items: center;
    width: 358px;
    height: 50px;
    margin-top: 20px;
    border: 1px solid #dbdbdb;
}

.label {
    width: 65px;
    height: 100%;
    line-height: 50px;
    color: #666;
    border-right: 1px solid #dbdbdb;
    text-align: center;
    font-size: 20px;
}

.login-button {
    width: 358px;
    height: 50px;
    text-align: center;
    line-height: 50px;
    margin: 24px auto 0;
    color: #fff;
    background-color: #e93323 !important;
    cursor: pointer;
}

.input {
    width: 291px;
    padding-left: 15px;
    height: 50px;
    border: 0;
    outline: none;
    font-size: 14px;
    box-sizing: border-box;
}

::v-deep .el-tabs__nav-wrap::after {
    background-color: transparent !important;
}

::v-deep .el-tabs__active-bar {
    background-color: #303133 !important;
}

::v-deep .el-tabs__item.is-active {
    color: #303133 !important;
}

::v-deep .el-tabs__item {
    color: #303133 !important;
}

.iconfont {
    font-family: "iconfont" !important;
    font-size: 20px;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.font-color {
    color: #e93323 !important;
}
</style>