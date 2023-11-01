<template>
    <div
        style="width: 1250px;margin: 0 auto;box-sizing: border-box;margin-top: 20px;background-color: white;padding-top: 10px;padding-bottom: 20px;margin-bottom: 40px;">
        <div @click="toBack()"
            style="margin-left: 10px;color: #606266 !important;cursor: pointer;width: 50px;font-size: 14px;">
            <i class="ri-arrow-left-s-line"></i><span>返回</span>
        </div>
        <div v-if="goodsInfo"
            style="width: 100%;box-sizing: border-box;padding: 20px;padding-left: 100px;padding-right: 100px;display: flex;flex-direction: row;">
            <div style="display: flex;flex-direction: column;width: 400px;">
                <div>
                    <el-image style="width: 385px;height: 385px;"
                        :src="currentSku && currentSku.picture ? currentSku.picture : currentImg" fit="cover"></el-image>
                </div>
                <div style="display: flex;flex-direction: row;margin-top: 10px;">
                    <div :class="currentImg == item.src ? 'img-active hover' : 'hover'" v-for="(item, index) in goodsImg"
                        :key="index"
                        style="margin-right: 3px;width: 62px;height: 62px;box-sizing: border-box;padding-top: 1px;padding-left: 1px;">
                        <el-image style="width: 60px;height: 60px;" :src="item.src" fit="cover"
                            @mouseover="currentImg = item.src"></el-image>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <div v-if="hasCollected == 0" style="color: #606266;margin-left: 5px;cursor: pointer;"
                        @click="addToCollect">
                        <i class="ri-star-smile-line"></i>
                        <span style="font-size: 14px;">收藏</span>
                    </div>
                    <div v-else style="color: #E93323;margin-left: 5px;cursor: pointer;" @click="removeToCollect">
                        <i class="ri-star-smile-fill"></i>
                        <span style="font-size: 14px;">已收藏</span>
                    </div>
                </div>
            </div>
            <div
                style="display: flex;flex-direction: column;width: 700px;box-sizing: border-box;padding-left: 30px;word-wrap: break-word;white-space: normal;">
                <div>
                    <div style="font-size: 20px;font-weight: 600;color: #303133;width: 570px;">
                        {{ goodsInfo.goods_name || '暂无信息' }}
                    </div>
                    <div style="width: 100%;background-color: #F7F7F7;margin-top: 20px;">
                        <div
                            style="background-color: #E93323;width: 100%;color: #fff;display: flex;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
                            <div style="padding-left: 32px;box-sizing: border-box;width: 500px;">
                                <del
                                    style="margin-bottom: 10px;font-size: 14px;width: 450px;word-wrap: break-word;white-space: normal;">原价：￥{{
                                        goodsInfo.o_price }}</del>
                                <div
                                    style="display: flex;flex-wrap: wrap;align-items: center;font-size: 30px;font-weight: bolder;width: 450px;word-wrap: break-word;white-space: normal;">
                                    ￥{{ currentSku ? currentSku.price : goodsInfo.s_price }}
                                </div>
                            </div>
                            <div
                                style="width: 120px;border-left: 1px solid hsla(0,0%,100%,.24);box-sizing: border-box;padding-left: 40px;padding-right: 20px;">
                                <div
                                    style="width: 100%;word-wrap: break-word;white-space: normal;text-align: center;margin-top: 5px;">
                                    {{ goodsInfo.sales }}</div>
                                <div style="width: 100%;text-align: center;margin-top: 5px;">销量</div>
                            </div>
                        </div>
                        <div style="height: 172px;width: 100%;background-color: #F7F7F7;">
                            <div
                                style="height: 136px;width: 100%;padding-top: 18px;padding-bottom: 18px;box-sizing: border-box;display: flex;color: #5a5a5a;font-size: 12px;">
                                <div style="margin-left: 20px;">优惠券</div>
                                <div>

                                </div>
                            </div>
                            <div
                                style="height: 36px;width: 100%;background-color: #f2f2f2;display: flex;justify-content: flex-end;padding-right: 24px;align-items: center;box-sizing: border-box;">
                                <div style="color: #666;font-size: 12px;cursor: pointer;">更多优惠</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 25px;">
                        <div v-for="(item, index) in attr" :key="index" style="display: flex;margin-top: 5px;">
                            <div
                                style="font-size: 12px;color: #5a5a5a;min-width: 78px;max-width: 150px;padding-left: 20px;box-sizing: border-box;margin-top: 8px;">
                                {{ item.key }}：</div>
                            <div ref="attrValue" style="display: flex;flex-wrap: wrap;margin-left: 10px;">
                                <template v-for="(value, idx) in item.value">
                                    <div v-if="notDisabled(item.attr_id, value.value_id) == 'show'" :key="idx"
                                        :class="attrChoose[item.attr_id] == value.value_id ? 'attr-value-active' : 'attr-value'"
                                        style="height: 36px;border-radius: 6px;background-color: rgba(0,0,0,.06);
                                            color: #5a5a5a;margin-right: 12px;margin-bottom: 16px;font-size: 12px;
                                            line-height: 36px;padding-left: 25px;padding-right: 25px;box-sizing: border-box;"
                                        @click="chooseAttrValue(item.attr_id, value.value_id, value.attr_value)">
                                        {{ value.attr_value }}
                                    </div>
                                    <div v-if="notDisabled(item.attr_id, value.value_id) == 'disabled'" :key="idx"
                                        class="attr-value-disabled"
                                        style="height: 36px;border-radius: 6px;background-color: rgba(0,0,0,.06);
                                            color: #5a5a5a;margin-right: 12px;margin-bottom: 16px;font-size: 12px;
                                            line-height: 36px;padding-left: 25px;padding-right: 25px;box-sizing: border-box;">
                                        {{ value.attr_value }}
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex;align-items: center;margin-top: 10px;">
                        <div style="color: #5a5a5a;font-size: 12px;margin-left: 20px;width: 70px;">数量：</div>
                        <el-input-number v-model="buyNum" :min="currentSku && currentSku.amount > 0 ? 1 : 0"
                            :max="currentSku && currentSku.amount > 0 ? currentSku.amount : 0" label="数量"></el-input-number>
                        <div v-if="currentSku" style="margin-left:15px;font-size: 14px;color: #5a5a5a;">{{
                            currentSku.amount > 0 ? '(库存：' + currentSku.amount + ')' : '(已售罄)' }}</div>
                    </div>
                    <div style="margin-top: 60px;margin-bottom: 30px;margin-left: 10px;">
                        <button :class="buyNum > 0 ? 'hover' : 'button-add-disabled'" style="background-color: #e93323;color: #fff;width: 158px;height: 50px;border: 1px solid #e93323;
                            border-radius: 4px;font-size: 16px;" @click="addToShoppingCart">加入购物车</button>
                        <button :class="buyNum > 0 ? 'hover' : 'button-buy-disabled'" style="width: 158px;height: 50px;border: 1px solid #e93323;border-radius: 4px;font-size: 16px;
                            color: #e93323;background-color: #fff;margin-left: 18px;" @click="toBuy">立即购买</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="store" style="width: 1050px;height: 150px;margin-left: 100px;margin-bottom: 20px;background-color: #F2F2F2;box-sizing: border-box;
            padding: 20px;border-radius: 100px;padding-left: 100px;padding-right: 0px;">
            <div style="width: 940px;height: 110px;display: flex;flex-direction: row;">
                <div style="">
                    <el-image style="width: 60px;height: 60px;border-radius: 5px;" :src="store.avatar"
                        fit="cover"></el-image>
                </div>
                <div style="display: flex;flex-direction: column;margin-left: 10px;width: 700px;">
                    <div style="font-size: 22px;color: #303133;font-weight: 700;margin-left: 10px;width: 590px;">{{ store.store_name }}</div>
                    <div style="width: 200px;height: 30px;background-color: rgba(96,98,102,.35);border-radius: 30px;
                        box-sizing: border-box;padding-left: 22px;display: flex;align-items: center;margin-top: 10px;">
                        <span style="font-size: 14px;color: #fff;margin-right: 3px;">综合体验</span>
                        <el-rate :value="store.total_sore" disabled show-score 
                            :colors="['#E93323','#E93323','#E93323']" text-color="#E93323" 
                            :score-template="store.total_sore.toFixed(1)"
                            style="margin-top: 1px;">
                        </el-rate>
                    </div>
                    <div style="width: 100%;height: 100%;display: flex;flex-direction: row;font-size: 14px;color: #e93323;margin-top: 15px;">
                        <div style="padding: 7px;border-radius: 18px;border: 1px solid #e93323;
                            padding-left: 12px;padding-right: 12px;cursor: pointer;margin-left: 10px;">进店逛逛</div>
                        <div style="padding: 7px;border-radius: 18px;border: 1px solid #e93323;
                            padding-left: 12px;padding-right: 12px;cursor: pointer;margin-left: 10px;">全部商品</div>
                    </div>
                </div>
                <div style="font-size: 12px;color: #5a5a5a;box-sizing: border-box;padding-top: 10px;margin-left: 30px;">
                    <div style="margin-top: 5px;">{{ '商品描述 ' + this.store.goods_score+' ' }}
                        <span style="color: #909399;">{{ this.store.goods_score<4.9?(this.store.goods_score > 4.7 ?'中':'低'):'高' }}</span>
                    </div>
                    <div style="margin-top: 5px;">{{ '卖家服务 ' + this.store.service_score+' ' }}
                        <span style="color: #909399;">{{ this.store.service_score < 4.9 ? (this.store.service_score > 4.7 ? '中' : '低') : '高' }}</span>
                    </div>
                    <div style="margin-top: 5px;">{{ '物流服务 ' + this.store.logistics_score+' ' }}
                        <span style="color: #909399;">{{ this.store.logistics_score < 4.9 ? (this.store.logistics_score > 4.7 ? '中' : '低') : '高' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-left: 100px;width: 1050px;height: 50px;color: #606266;
            line-height: 50px;background-color: #F2F2F2;padding-left: 30px;box-sizing: border-box;">
            商品详情
        </div>
        <div v-if="goodsInfo" class="detail" v-html="goodsInfo.detail">
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { get, post } from '@/api/MyAxios';
export default {
    data() {
        return {
            gid: null,
            goodsInfo: null,
            attrChoose: {},
            goodsImg: [],
            currentImg: '',
            attr: [],
            sku: [],
            store: null,
            currentSku: null,
            buyNum: 0,
            sku_info: {},
            hasCollected: 0
        }
    },
    computed: {
        ...mapGetters([
            'isLogin',
            'userInfo'
        ])
    },
    mounted() {
        this.gid = this.$route.query.gid
        this.getGoodsInfo()
        if (this.isLogin) {
            this.getHasCollected()
        }
    },
    methods: {
        getGoodsInfo() {
            get(`Index/getProductInfo?gid=${this.gid}`).then(res => {
                if (res.code == 200) {
                    this.goodsInfo = res.data.goods
                    this.goodsImg = res.data.picture
                    if (res.data.picture.length > 0) {
                        this.currentImg = res.data.picture[0].src
                    }
                    this.sku = res.data.sku
                    this.store = res.data.store
                    this.store['total_sore']=((Number)(this.store.goods_score) + (Number)(this.store.service_score) + (Number)(this.store.logistics_score)) / 3
                    this.dealAttrValue(res.data.attr, res.data.attr_value)
                }
                else if (res.code == 400) {
                    this.$router.replace('/NoItem')
                }
                else {
                    this.$message.error(res.msg)
                }
            })
        },
        getHasCollected() {
            let params = new FormData()
            params.append('uid', this.userInfo.uid)
            params.append('gid', this.gid)
            post('Collect/hasCollected', params).then(res => {
                if (res.code == 200) {
                    this.hasCollected = res.data
                }
            })
        },
        dealAttrValue(attr, attr_value) {
            if (attr.length == 0) {
                this.currentSku = this.sku[0]
            }
            for (let i = 0; i < attr.length; i++) {
                this.attrChoose[attr[i].attr_id] = ''
                let value = []
                for (let j = 0; j < attr_value.length; j++) {
                    if (attr[i].attr_id == attr_value[j].attr_id) {
                        value.push(attr_value[j])
                    }
                }
                attr[i]['value'] = value
            }
            this.attr = attr
        },
        chooseAttrValue(attr_id, value_id, attr_value) {
            this.buyNum = 0
            if (this.attrChoose[attr_id] == value_id) {
                this.attrChoose[attr_id] = ''
                this.currentSku = null
            }
            else {
                this.attrChoose[attr_id] = value_id
                this.sku_info[attr_id] = attr_value
                let attrPath = '-'
                let have = true
                for (let i in this.attrChoose) {
                    attrPath = attrPath + i + ':' + this.attrChoose[i] + '-'
                }
                for (let i = 0; i < this.sku.length; i++) {
                    if (attrPath == this.sku[i].attr_path) {
                        this.currentSku = this.sku[i]
                        if (this.sku[i].amount > 0) {
                            this.buyNum = 1
                        }
                        have = false
                        break
                    }
                }
                if (have) {
                    this.currentSku = null
                }
            }
            this.$forceUpdate()
        },
        notDisabled(attr_id, value_id) {
            let attrPath = '-'
            for (let i in this.attrChoose) {
                if (i != attr_id && this.attrChoose[i] == '') {
                    return 'show'
                }
                else if (i == attr_id) {
                    attrPath = attrPath + attr_id + ':' + value_id + '-'
                } else {
                    attrPath = attrPath + i + ':' + this.attrChoose[i] + '-'
                }
            }
            for (let i = 0; i < this.sku.length; i++) {
                if (attrPath == this.sku[i].attr_path) {
                    if (this.sku[i].amount > 0 && this.sku[i].status == 1) {
                        return 'show'
                    }
                    return 'disabled'
                }
            }
            return 'notShow'
        },
        toBack() {
            this.$router.back();
        },
        judgeLogin() {
            if (!this.isLogin) {
                this.$router.push('/Login')
                return false
            }
            return true
        },
        addToShoppingCart() {
            if(this.buyNum == 0){
                return
            }
            if(!this.judgeLogin()){
                return
            }
            let sku_info = '属性：'
            for (let i in this.sku_info) {
                sku_info = sku_info + this.sku_info[i] + ' '
            }
            let params = new FormData()
            params.append('uid', this.userInfo.uid)
            params.append('gid', this.gid)
            params.append('sku_id', this.currentSku.sku_id)
            params.append('store_id', 1)
            params.append('sku_info', sku_info)
            params.append('num', this.buyNum)
            post('ShoppingCart/save', params).then(res => {
                if (res.code == 200) {
                    this.$message.success(res.msg)
                    let p = new FormData()
                    p.append('uid', this.userInfo.uid)
                    post('ShoppingCart/get', p).then(res => {
                        if (res.code == 200) {
                            this.$store.commit('setShoppingCart', res.data.records)
                        }
                    })
                }
                else {
                    this.$message.error(res.msg)
                }
            })
        },
        toBuy() {
            if (this.buyNum == 0) {
                return
            }
            if (!this.judgeLogin()) {
                return
            }
        },
        addToCollect() {
            if (!this.judgeLogin()) {
                return
            }
            let params = new FormData()
            params.append('uid', this.userInfo.uid)
            params.append('gid', this.gid)
            post('Collect/save', params).then(res => {
                if (res.code == 200) {
                    this.hasCollected = 1
                    this.$message.success(res.msg)
                }
            })
        },
        removeToCollect() {
            let params = new FormData()
            params.append('uid', this.userInfo.uid)
            params.append('gid', this.gid)
            post('Collect/delete', params).then(res => {
                if (res.code == 200) {
                    this.hasCollected = 0
                    this.$message.success('已取消收藏')
                }
            })
        }
    }
}
</script>
<style lang="scss" scoped>
::v-deep .el-rate__item{
    width: 14px;
}
::v-deep .el-rate__text{
    margin-left: 3px;
    line-height: 15px;
}
::v-deep .el-rate__icon{
    font-size: 14px;
}
.detail {
    width: 1050px;
    min-height: 200px;
    margin: 0 auto;
    background-color: #F7F7F7;
    overflow: hidden;
    box-sizing: border-box;
    padding-bottom: 40px;
    text-align: center;
}

.hover:hover {
    cursor: pointer;
}

.attr-value:hover {
    background-color: rgba(0, 0, 0, .11) !important;
    color: #ff5000 !important;
    cursor: pointer;
}

.attr-value-active {
    color: #ff5000 !important;
    border: 1px solid #ff5000 !important;
    padding-left: 24px !important;
    padding-right: 24px !important;
    line-height: 35px !important;
    background-color: #fff7f3 !important;
}

.attr-value-active:hover {
    cursor: pointer;
}

.attr-value-disabled {
    color: #C0C4D1 !important;
    background-color: rgba(0, 0, 0, .03) !important;
}

.attr-value-disabled:hover {
    cursor: not-allowed;
}

.img-active {
    border: 1px solid #ff5000 !important;
    padding-top: 0px !important;
    padding-left: 0px !important;
}

.button-buy-disabled {
    background-color: #FFFFFF !important;
    border: 1px solid #FAB6B6 !important;
    color: #FAB6B6 !important;
}

.button-buy-disabled:hover {
    cursor: not-allowed;
}

.button-add-disabled {
    background-color: #FAB6B6 !important;
    border: 1px solid #FAB6B6 !important;
    color: #FFFFFF !important;
}

.button-add-disabled:hover {
    cursor: not-allowed;
}
</style>