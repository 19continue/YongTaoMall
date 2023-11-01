<template>
    <div>
        <el-container>
            <el-aside width="100" ref="aside">
                <el-menu :default-active="active" ref="menu" width="300px" style="height: 100vh;" class="el-menu-vertical"
                    @select="changeSelect" router unique-opened :collapse="isCollapse" background-color="#334157"
                    text-color="#fff" active-text-color="#ffd04b">
                    <div style="padding-top: 15px;margin-bottom: 15px;">
                        <transition name="el-fade-in-linear">
                            <span v-show="showTitle" style="color: #F5F5F5;margin-left: 20px;font-size: 22px;">涌涛商城管理</span>
                        </transition>
                    </div>
                    <el-menu-item index="/Home/Main" @click="addTabsEdit('主页', '/Home/Main')">
                        <i class="el-icon-s-home"></i>
                        <span slot="title">主页</span>
                    </el-menu-item>
                    <menu-item v-for="(item, index) in routeArray" :key="index" :data="item"
                        @choosed="addTabsEdit"></menu-item>
                </el-menu>
            </el-aside>
            <el-container style="height: 100vh;">
                <el-header class="header" style="padding: 0;" height="50">
                    <div class="header-box">
                        <el-popover placement="bottom" width="250" trigger="click">
                            <div class="avatar" slot="reference">
                                <el-avatar v-if="info.avatar" :src="info.avatar" size="small"></el-avatar>
                                <el-avatar v-else icon="el-icon-user-solid" size="small"></el-avatar>
                                <div>{{ info.nick_name || '' }}</div>
                            </div>
                            <div class="admin-info">
                                <el-avatar v-if="info.avatar" :src="info.avatar" :size="80"
                                    style="margin-bottom: 5px;"></el-avatar>
                                <el-avatar v-else icon="el-icon-user-solid" :size="80"
                                    style="margin-bottom: 5px;"></el-avatar>
                                <div>{{ info.nick_name }}</div>
                                <div>{{ info.login_time }}</div>
                                <el-row>
                                    <el-button type="success" size="small" plain
                                        style="margin-right: 20px;">个人信息</el-button>
                                    <el-button type="danger" size="small" plain @click="logout">注销</el-button>
                                </el-row>
                            </div>
                        </el-popover>

                        <div class="full" @click="toFull">
                            <el-tooltip content="全屏" placement="bottom">
                                <i :class="isFull ? 'el-icon-crop' : 'el-icon-full-screen'"></i>
                            </el-tooltip>
                        </div>
                        <div class="history">
                            <div class="tag">历史标签</div>
                            <el-switch v-model="showTab" active-color="#1989FA" inactive-color="#C0C4CC">
                            </el-switch>
                        </div>
                        <div>
                            <i :class="isCollapse ? 'el-icon-s-unfold' : 'el-icon-s-fold'" @click="fold()"
                                style="font-size: 25px;color: #C0C4CC;position: absolute;left: 10px;line-height: 50px;"></i>
                        </div>
                    </div>
                </el-header>
                <el-main style="margin: 0;padding: 0;background-color: #F8F8F8;">
                    <transition name="el-zoom-in-top">
                        <div v-show="showTab">
                            <el-tabs v-model="editableTabsValue" style="height: 38px" type="card" :closable="tabClosable"
                                @tab-remove="removeTab" :before-leave="tabChange">
                                <el-tab-pane style="height: 0px" :key="item.name" v-for="item in editableTabs"
                                    :label="item.title" :name="item.name">
                                </el-tab-pane>
                            </el-tabs>
                        </div>
                    </transition>
                    <transition name="el-fade-in">
                        <router-view></router-view>
                    </transition>
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { resetRouter } from '@/router';
import screenfull from 'screenfull';
import MenuItem from '@/components/MenuItem.vue';
export default {
    name: 'Home',
    components: {
        MenuItem
    },
    data() {
        return {
            isCollapse: false,
            showTitle: true,
            active: '/Home/Main',
            editableTabsValue: '/Home/Main',
            editableTabs: [{
                title: '主页',
                name: '/Home/Main'
            }],
            tabClosable: false,
            showTab: true,
            isFull: false
        }
    },
    computed: {
        ...mapGetters([
            'routeArray',
            'info'
        ])
    },
    watch: {
        $route(to, from) {
            this.active = this.$route.path;
            if (to.meta.menu_id) {
                this.$store.commit('setMenuId', to.meta.menu_id);
            }
        }
    },
    mounted() {
        this.active = this.$route.path;
        this.editableTabs = [{
            title: this.$route.meta.name,
            name: this.$route.path
        }];
        this.editableTabsValue= this.$route.path
    },
    methods: {
        button(path) {
            this.$router.push(path);
        },
        logout() {
            this.$confirm('确认退出登录吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消'
            }).then(() => {
                this.$store.commit('setIslogin', false);
                this.$store.commit('setId', 0);
                this.$store.commit('setInfo', []);
                this.$store.commit('setHasRoute', false);
                this.$store.commit('setRouteArray', []);
                sessionStorage.clear();
                localStorage.clear();
                resetRouter();
                this.$router.replace('/');
                this.$message({
                    type: 'success',
                    message: '退出成功!'
                });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消注销'
                });
            });
        },
        fold() {
            if (this.isCollapse) {
                this.showTitle = !this.showTitle;
                this.isCollapse = !this.isCollapse;
            }
            else {
                this.showTitle = !this.showTitle;
                setTimeout(() => {
                    this.isCollapse = !this.isCollapse;
                }, 50)
            }

        },
        toFull() {
            if (!screenfull.isEnabled) {
                this.$message('浏览器不支持全屏');
                return;
            }
            screenfull.toggle();
            this.isFull = !this.isFull;
        },
        changeSelect(index, path) {
            this.active = index;

        },
        tabChange(activeName, oldActiveName) {
            this.$router.push(activeName);
        },
        addTabsEdit(name, path) {
            for (let i = 0; i < this.editableTabs.length; i++) {
                if (this.editableTabs[i].name == path) {
                    this.editableTabsValue = path;
                    return;
                }
            };
            this.editableTabs.push({
                title: name,
                name: path
            });
            this.editableTabsValue = path;
            this.tabClosable = true;
        },
        removeTab(targetName) {
            let tabs = this.editableTabs;
            let activeName = this.editableTabsValue;
            if (activeName === targetName) {
                tabs.forEach((tab, index) => {
                    if (tab.name === targetName) {
                        let nextTab = tabs[index + 1] || tabs[index - 1];
                        if (nextTab) {
                            activeName = nextTab.name;
                        }
                    }
                });
            }
            this.editableTabsValue = activeName;
            this.editableTabs = tabs.filter(tab => tab.name !== targetName);
            if (this.editableTabs.length < 2) {
                this.tabClosable = false;
            }
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

@mixin word {
    color: #909399;
    font-size: 12px;
}

@mixin hover {
    height: 100%;
    padding-left: 10px;
    padding-right: 10px;

    &:hover {
        cursor: pointer;
        background-color: #F5F5F5;
    }
}

@media screen and (max-width: 720px) {
    .el-aside {
        display: none;
    }
}

@media screen and (max-width: 450px) {

    .full,
    .history {
        display: none !important;
    }
}

.el-menu-vertical:not(.el-menu--collapse) {
    width: 250px;
    min-height: 100vh;
}

.header {
    height: 51px;
    overflow: hidden;
}

.header-box {
    position: relative;
    box-sizing: border-box;
    background-color: white;
    width: 100%;
    height: 100%;
    border-bottom: 1px solid #DBDCDD;
    display: flex;
    flex-direction: row-reverse;

    .avatar {
        @include row();
        @include word();
        @include hover();

        div {
            margin-left: 5px;
        }
    }

    .history {
        @include row();
        @include word();
        padding-left: 10px;
        padding-right: 10px;

        div {
            margin-right: 5px;
        }
    }

    .full {
        @include row();
        @include hover();
    }
}

.admin-info {
    @include column();
    @include word();
    font-size: 16px;
    padding-top: 10px;
    padding-bottom: 10px;

    div {
        margin-bottom: 10px;
    }
}

.el-zoom-in-top-in-leave-to {
    display: none !important;
}

.el-fade-in-leave-to {
    display: none !important;
}

.el-aside::-webkit-scrollbar {
    display: none;
}
</style>
