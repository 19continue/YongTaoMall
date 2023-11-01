<template>
    <div style="width: 1200px;text-align: center;margin: 0 auto;">
        <div style="width: 1200px;background-color: white;margin-top: 10px;">
            <div
                style="width: 1200px;display: flex;flex-wrap: wrap;padding-left: 20px;padding-top:20px;padding-right: 20px;padding-bottom: 25px;box-sizing: border-box;font-size: 14px;">
                <div style="color: #909399;margin-right: 20px;">排序:</div>
                <div class="more" @click="searchByFileter('default')"
                    :style="'margin-right: 20px;' + (filter == 'default' ? 'color:#E83927;' : '')">综合</div>
                <div style="margin-right: 20px;display: flex;flex-direction: row;align-items: center;position: relative;">
                    <div class="more" @click="searchByFileter(filter == 'price-asc' ? 'price-desc' : 'price-asc')"
                        :style="'padding-right: 20px;position: relative;z-index: 2;' + (filter == 'price-asc' || filter == 'price-desc' ? 'color:#E83927;' : '')">
                        价格
                    </div>
                    <div style="font-size: 16px;">
                        <i class="ri-arrow-up-s-fill"
                            :style="'position: absolute;top: -1px;left: 35px;z-index: 1;' + (filter == 'price-asc' ? 'color:#E83927;' : 'color: #909399;')"></i>
                        <i class="ri-arrow-down-s-fill"
                            :style="'position: absolute;top: 5px;left: 35px;z-index: 1;color: #909399;' + (filter == 'price-desc' ? 'color:#E83927;' : 'color: #909399;')"></i>
                    </div>
                </div>
                <div class="more" @click="searchByFileter('sales')"
                    :style="'margin-right: 20px;' + (filter == 'sales' ? 'color:#E83927;' : '')">销量</div>
            </div>
        </div>
        <div
            style="width: 1200px;display: flex;flex-direction: row;flex-wrap: wrap;box-sizing: border-box;align-items: center;">
            <div style="width: 224px;height: 340px;padding: 16px;box-sizing: border-box;margin: 20px 20px 0 0;background-color: white;box-shadow: 0 3px 20px rgba(241,242,242, 1);"
                class="item" v-for="(good, idx) in goods" :key="idx" @click="toThisGoods(good)">
                <div>
                    <el-image shape="square" style="width: 192px; height: 192px" fit="cover" :src="good.src"></el-image>
                </div>
                <div style="width: 192px;margin-top: 12px;display: flex;flex-direction:row;align-items: center;">
                    <div style="font-size: 22px;font-weight: 700;color: #e93323;overflow: hidden;text-overflow: ellipsis;">
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
        <el-empty v-if="goods.length == 0" description="搜索不到商品哦，换个关键词试试吧！" style="margin-top: 50px;margin-bottom: 50px;"></el-empty>
        <div v-else-if="goods.length >= 20">
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
        <div v-else style="box-sizing: border-box;padding: 30px;width: 1200px;margin: 0 auto;">

        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { post } from '@/api/MyAxios';
export default {
    data() {
        return {
            key_words:'',
            total:0,
            filter: 'default',
            goods: [],
            currentPage: 1,
            loadingData: false,
            noMoreData: false
        }
    },
    computed: {
        ...mapGetters([
        ])
    },
    mounted() {
        if (this.$route.query.key_words !== undefined) {
            this.key_words=this.$route.query.key_words
            this.getGoods()
        }
        window.addEventListener('scroll', this.scrollBottom)
    },
    watch: {
        $route: {
            handler(val, old) {
                if (this.$route.query.key_words !== undefined) {
                    this.key_words = this.$route.query.key_words
                    this.getGoods()
                }
            },
            immediate: false,
            deep: true
        }
    },
    methods: {
        searchByFileter(filter) {
            this.filter = filter
            this.getGoods()
        },
        getGoods(replace = true) {
            if (replace) {
                this.currentPage = 1
                this.noMoreData = false
            }
            let params = new FormData();
            console.log(this.key_words)
            if (this.key_words != '') {
                params.append('key_words', this.key_words)
            } else {
                return
            }
            if (this.filter !== 'default') {
                params.append('order_by', this.filter)
            }
            params.append('pageSize', 20)
            params.append('currentPage', this.currentPage)
            post('Index/search', params).then(res => {
                if (res.code == 200) {
                    console.log(res.data)
                    if (replace) {
                        this.goods = res.data.records
                        this.currentPage += 1
                    }
                    else {
                        let _this = this
                        if (res.data.records && res.data.records.length < 1) {
                            setTimeout(function () {
                                _this.noMoreData = true
                                _this.loadingData = false
                            }, 500)
                            return
                        }
                        setTimeout(function () {
                            _this.goods.push(...res.data.records)
                            _this.currentPage += 1
                            _this.loadingData = false
                        }, 500)
                    }
                }
            })

        },
        scrollBottom() {
            let scrollTop = document.documentElement.scrollTop
            let clientHeight = document.documentElement.clientHeight
            let scrollHeight = document.documentElement.scrollHeight
            if (this.currentPage !== 1 && !this.loadingData && !this.noMoreData && (scrollHeight - clientHeight < scrollTop + 10)) {
                this.loadingData = true
                this.getGoods(false)
            }
        },
        toThisGoods(item) {
            this.$router.push({
                path: '/GoodsInfo',
                query: {
                    gid: item.gid
                }
            })
        },
    }
}
</script>
<style lang="scss" scoped>
::v-deep .el-tabs__nav-wrap::after {
    background-color: transparent !important;
}

::v-deep .el-tabs__active-bar {
    background-color: #E83927 !important;
}

::v-deep .el-tabs__item.is-active {
    color: #E83927 !important;
}

::v-deep .el-tabs__item:hover {
    color: #E83927 !important;
}

::v-deep .el-tabs__header {
    margin: 0px;
}

::v-deep .el-tabs__item {
    height: 50px;
    max-width: 200px;
    line-height: 55px;
    font-size: 14px;
    overflow: hidden;
    text-overflow: ellipsis;
}

::v-deep .el-tabs__nav-next,
::v-deep .el-tabs__nav-prev {
    line-height: 56px;
}

.more:hover {
    cursor: pointer;
    color: #E83927;
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
}</style>