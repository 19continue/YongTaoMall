<template>  
        <el-menu-item v-if="!data.children || data.children.length === 0" :index="data.path" @click="send(data.name, data.path)">
            <i :class="`${data.icon}`"></i>
            <span slot="title">{{ data.name }}</span>
        </el-menu-item>
        <el-submenu v-else ref="subMenu" :index="data.path">
            <template slot="title">
                <i :class="data.icon"></i>
                <span slot="title">{{ data.name }}</span>
            </template>
            <menu-item v-for="child in data.children" :key="child.path" :data="child" @choosed="send"/>
        </el-submenu>
</template>
<script>
export default {
    name: 'MenuItem',
    props: ['data'],
    data() {
        return{

        }
    },
    methods: {
        send(name,path){
            this.$emit('choosed',name,path);
        }
    }
}
</script>