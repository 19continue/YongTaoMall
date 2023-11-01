<template>
    <div class="container">
        <div
            style="display: flex;flex-direction: row;width: 100%;background-color: #F9F9F9;padding-top: 20px;padding-bottom: 20px;">
            <div
                style="width: 200px;height: 350px;margin-left: 25px;background-color: rgba(59,59,59,0.05);position: relative;padding-top: 10px;box-sizing: border-box;">
                <div @mouseleave="menu.show = false, menu.parentId = ''">
                    <div style="width: 200px;" @mouseover="menu.show = true">
                        <div v-for="(option, index) in classOption" :key="index"
                            :class="menu.parentId == option.class_id ? 'category-active' : 'category'"
                            style="display: flex;flex-direction: row;box-sizing: border-box;padding:10px 0px 10px 30px;color: #909399;"
                            @mouseover="menu.list = option.children, menu.parentId = option.class_id"
                            @click="toClassPage(option.class_id, false)">
                            <div
                                style="overflow: hidden;font-size:12px;text-overflow: ellipsis;white-space: nowrap;width: 130px;">
                                {{ option.name }}</div>
                            <i class="el-icon-arrow-right" style="margin-left: 10px;"></i>
                        </div>
                    </div>
                    <div v-show="menu.show" class="menu" @mouseleave="menu.show = false" style="position: absolute;background-color:white;
                        width: 600px;height: 350px;left: 200px;top:0px;z-index: 9999;padding: 20px;box-sizing: border-box;
                        display: flex;flex-wrap: wrap;">
                        <div class="menu-list-item" v-for="(child, idx) in menu.list" :key="idx"
                            @click="toClassPage(menu.parentId, child.class_id)"
                            style="height: 40px;display: flex;flex-direction: row;align-items: center;margin-left: 20px;cursor: pointer;color: #909399;">
                            <el-image v-if="child.picture" shape="square" fit="cover" style="width: 30px;height: 30px;"
                                :src="child.picture"></el-image>
                            <div style="font-size: 12px;margin-left: 10px;width: 50px;">{{
                                child.name }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <el-carousel :interval="5000" arrow="hover" height="350px">
                <el-carousel-item v-for="(img, index) in carousel" :key="index">
                    <el-image shape="square" :fit="img.fit ? img.fit : 'cover'" style="width: 800px;height: 350px;cursor: pointer;"
                        :src="img.src" @click="carouselClick(img.url)"></el-image>
                </el-carousel-item>
            </el-carousel>
        </div>
        <div
            style="width: 1250px;margin-top: 20px;background-color: #F9F9F9;padding: 25px;padding-right:0px;box-sizing: border-box;">
            <div v-for="(item, index) in category" :key="index" style="margin-bottom: 20px;">
                <div style="display: flex;flex-direction: row;align-items: center;">
                    <div style="font-size: 1.3rem;font-weight: bolder;">
                        {{ item.name }}
                    </div>
                    <div class="hover" @click="toClassPage(item.class_id, false)"
                        style="border: 1px solid #C6C6C6;color: #606266;font-size: 0.7rem;padding: 0.2rem;padding-left: 0.6rem;padding-right:0.6rem;margin-left: auto;margin-right: 25px;">
                        更多
                    </div>
                </div>
                <div
                    style="width: 100%;display: flex;flex-direction: row;flex-wrap: wrap;box-sizing: border-box;align-items: center;">
                    <div class="hover" @click="toClassPage(item.class_id, false)" style="margin: 20px 20px 0 0;">
                        <el-image shape="square" style="width: 468px; height: 340px" fit="cover"
                            :src="item.picture"></el-image>
                    </div>
                    <div style="width: 224px;height: 340px;padding: 16px;box-sizing: border-box;margin: 20px 20px 0 0;background-color: white;box-shadow: 0 3px 20px rgba(241,242,242, 1);"
                        class="item" v-for="(good, idx) in item.goods" :key="idx" @click="toThisGoods(good)">
                        <div>
                            <el-image shape="square" style="width: 192px; height: 192px" fit="cover"
                                :src="good.src"></el-image>
                        </div>
                        <div style="width: 192px;margin-top: 12px;display: flex;flex-direction:row;align-items: center;">
                            <div
                                style="font-size: 22px;font-weight: 700;color: #e93323;overflow: hidden;text-overflow: ellipsis;">
                                ￥{{ good.s_price }}
                            </div>
                            <div
                                style="color: #aaa;font-size: 12px;text-decoration: line-through;margin-left: 8px;margin-top: 6px;overflow: hidden;text-overflow: ellipsis;width: 70px;">
                                ￥{{ good.o_price }}
                            </div>
                        </div>
                        <div
                            style="width: 192px;height: 37px;font-size: 14px;color: #5a5a5a;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp:2;line-clamp: 2;-webkit-box-orient: vertical;">
                            {{ good.goods_name }}
                        </div>
                        <div
                            style="font-size: 12px;color: #aaa;margin-top: 10px;margin-right: 5px;display: flex;flex-direction: row-reverse;">
                            <span>{{ good.sales }}人付款</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p v-if="loadingData"
            style="width: 1200px;margin: 0 auto;padding: 40px;box-sizing: border-box;text-align: center;color: #909399;">
            加载中<i class="el-icon-loading"></i></p>
        <p v-if="!loadingData && !noMoreData"
            style="width: 1200px;margin: 0 auto;padding: 40px;box-sizing: border-box;text-align: center;color: #909399;">
            下拉加载更多...</p>
        <p v-if="noMoreData"
            style="width: 1200px;;margin: 0 auto;padding: 40px;box-sizing: border-box;text-align: center;color: #909399;">
            没有更多了</p>
    </div>
</template>

<script>
import { post } from '@/api/MyAxios';
import router from '@/router';
import { mapGetters } from 'vuex';
export default {
    data() {
        return {
            category: [],
            menu: {
                show: false,
                parentId: '',
                list: []
            },
            currentPage: 1,
            loadingData: false,
            noMoreData: false
        }
    },
    computed: {
        ...mapGetters([
            'classOption',
            'carousel'
        ])
    },
    mounted() {
        this.getClassOption()
        this.getPage()
        window.addEventListener('scroll', this.scrollBottom)
    },
    methods: {
        getClassOption() {
            let params = new FormData()
            params.append('pageSize', 8)
            params.append('currentPage', 1)
            post('Categorize/get', params).then(res => {
                if (res.code == 200) {
                    this.$store.commit('setClassOption', res.data.records)
                }
            })
        },
        getPage() {
            let params = new FormData()
            params.append('pageSize', 3)
            params.append('currentPage', this.currentPage)
            post('Index/get_category_product', params).then(res => {
                if (res.code == 200) {
                    let _this = this
                    if (res.data && res.data.length < 1) {
                        setTimeout(function () {
                            _this.noMoreData = true
                            _this.loadingData = false
                        }, 500)
                        return
                    }
                    setTimeout(function () {
                        _this.category.push(...res.data)
                        _this.currentPage += 1
                        _this.loadingData = false
                    }, 500)
                }
            }).catch(() => {
                this.$message.error('获取数据失败')
            })
        },
        toThisGoods(item) {
            this.$router.push({
                path: '/GoodsInfo',
                query: {
                    gid: item.gid
                }
            })
        },
        toClassPage(category_parent, category_child) {
            this.$router.push({
                path: '/Class',
                query: {
                    category_parent: category_parent,
                    category_child: category_child
                }
            })
        },
        scrollBottom() {
            let scrollTop = document.documentElement.scrollTop
            let clientHeight = document.documentElement.clientHeight
            let scrollHeight = document.documentElement.scrollHeight
            if (this.currentPage !== 1 && !this.loadingData && !this.noMoreData && (scrollHeight - clientHeight < scrollTop + 10)) {
                this.loadingData = true
                this.getPage()
            }
        },
        carouselClick(url){
            if(url){
                window.location.href=url
                // this.$router.push(url)
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.container {
    padding-top: 20px;
    width: 1250px;
    margin: 0 auto;
}

.hover:hover {
    cursor: pointer;
}

.item:hover {
    box-shadow: 0 3px 20px rgba(0, 0, 0, 0.15) !important;
    animation-name: showShadow;
    animation-duration: 0.7s;
    cursor: pointer;
}

@keyframes showShadow {
    from {
        transform: scale(0.98);
    }

    to {
        transform: scale(1);
    }
}

.category:hover {
    background-color: #E43E31;
    color: #fff !important;
    cursor: pointer;
}

.category-active {
    background-color: #E43E31;
    color: #fff !important;
}

.menu {
    animation-name: menuShow;
    animation-duration: 0.5s;
}

.menu-list-item:hover {
    color: #E43E31 !important;
}

@keyframes menuShow {
    from {
        transform-origin: left;
        transform: rotateY(-90deg);
    }

    to {
        transform-origin: left;
        transform: rotateY(0deg);
    }
}

.el-menu-item {
    text-align: center;
}

.el-carousel {
    width: 800px;
    margin: 0 auto;
    margin: 0;
}

.el-carousel__item {
    width: 100%;
    height: 100%;
}

.el-carousel__item h3 {
    color: #475669;
    font-size: 18px;
    opacity: 0.75;
    line-height: 350px;
    text-align: center;
}

.el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
}

.el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
}</style>