<template>
    <div>
        <div class="container">
            <el-form :model="loginForm" :status-icon="false" :rules="rules" ref="loginForm" label-width="atuo"
                class="login">
                <el-form-item prop="username">
                    <el-input clearable v-model="loginForm.username" placeholder="请输入账号" autocomplete="off"><i slot="prefix"
                            class="el-icon-user-solid"></i></el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" clearable v-model="loginForm.password" placeholder="请输入密码"
                        @keyup.enter.native="submitForm('loginForm')" autocomplete="off" show-password><i slot="prefix"
                            class="el-icon-s-tools"></i></el-input>
                </el-form-item>
                <el-form-item>
                    <el-checkbox v-model="keep">保持登录状态</el-checkbox>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" round size="large" @click="submitForm('loginForm')"
                        class="login-button">登录</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
import { getCurrentScope } from 'vue';
import { login, selectAdminById } from '../api';
export default {
    name: 'Login',
    data() {
        var validateUsername = (rule, value, callback) => {
            if (!value) {
                callback(new Error('请输入账号'));
            }
            return callback()
        };
        var validatePassword = (rule, value, callback) => {
            if (!value) {
                callback(new Error('请输入密码'));
            }
            return callback()
        }
        return {
            loginForm: {
                username: '',
                password: '',
            },
            rules: {
                username: [
                    { validator: validateUsername, trigger: 'blur' }
                ],
                password: [
                    { validator: validatePassword, trigger: 'blur' }
                ]
            },
            keep: false
        };
    },
    created() {
        if (localStorage.getItem('islogin')) {
            let params = new URLSearchParams();
            params.append("admin_id", localStorage.getItem('id'));
            selectAdminById(params).then(res => {
                if (res.code == 200) {
                    this.$store.commit('setIslogin', true);
                    this.$store.commit('setId', res.data.admin_id);
                    this.$store.commit('setInfo', res.data);
                    this.$router.push('/Home/Main');
                }
                else {
                    this.$message({
                        message: '登录状态已过期,请重新登录!',
                        type: 'error'
                    });
                }
            })
        }
    },
    methods: {
        login() {
            let params = new URLSearchParams();
            params.append("name", this.loginForm.username);
            params.append("password", this.loginForm.password);
            login(params).then(res => {
                if (res.code == 200) {
                    this.$message({
                        message: res.msg,
                        type: 'success'
                    });
                    if (this.keep) {
                        localStorage.setItem('islogin', true);
                        localStorage.setItem('id', res.data.admin_id);
                    }
                    this.$store.commit('setIslogin', true);
                    this.$store.commit('setId', res.data.admin_id);
                    this.$store.commit('setInfo', res.data);
                    this.$router.push('/Home/Main');
                }
                else {
                    this.$message({
                        message: res.msg,
                        type: 'error'
                    });
                }
            })
        },
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.login();
                } else {
                    return false;
                }
            });
        }
    }
}
</script>
<style lang="scss" scoped>
.container {
    position: absolute;
    background: #1E1E1E;
    top: 0;
    display: flex;
    width: 100%;
    height: 100%;
    align-items: center;
    justify-content: center;

    .login {
        position: relative;
        padding: 100px 40px 40px 40px;
        background: whitesmoke;
        margin: 0;
        border-radius: 5px;

        i {
            margin: 0px 5px 0px 5px;
        }

        .login-button {
            width: 100%;
        }
    }
}

@media screen and (min-width: 720px) {
    .container {
        display: flex;
        align-items: center;
        justify-content: center;

        .login {
            width: 340px;
            margin-top: 0;
        }
    }
}</style>