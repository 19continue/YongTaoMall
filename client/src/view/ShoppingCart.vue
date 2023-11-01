<template>
    <div style="width: 100%;background-color: #fff;box-sizing: border-box;padding-top: 10px;">
        <div style="width: 1202px;margin: 0 auto;">
            <div @click="toBack()"
                style="margin-left: 10px;color: #606266 !important;cursor: pointer;width: 50px;font-size: 14px;margin-bottom: 10px;">
                <i class="ri-arrow-left-s-line"></i><span>返回</span>
            </div>
            <div style="box-sizing: border-box;padding-bottom: 20px;">
                <div style="width: 1202px;border: 1px solid #EBEEF5;border-bottom: none;">
                    <el-table ref="multipleTable" :data="shoppingCart" style="width: 1200px;" empty-text="购物车是空的"
                        :header-cell-style="{ background: '#f9f9f9' }" @selection-change="handleSelectionChange">
                        <el-table-column type="selection" width="100" align="center">
                        </el-table-column>
                        <el-table-column label="商品信息" width="400" align="center">
                            <template slot-scope="scope">
                                <div style="width: 400px;height: 146px;padding-left: 30px;box-sizing: border-box;display: flex;flex-direction: row;
                                align-items: center;justify-content: center;cursor: pointer;" @click="toThisGoods(scope.row)">
                                    <el-image shape="square" fit="contain" style="width: 90px;height: 90px;margin-right: 14px;"
                                        :src="scope.row.picture? scope.row.picture: scope.row.src"></el-image>
                                    <div style="height: 90px;width: 264px;display: flex;flex-direction: column;justify-content: center;text-align: left;">
                                        <div style="width: 264px;word-break: break-all;display: -webkit-box;-webkit-box-orient: vertical;overflow: hidden;
                                            -webkit-line-clamp: 2;color: #282828;font-size: 14px;">
                                            <span v-if="scope.row.status==0 || scope.row.delete_status == 0">
                                                <el-tag type="warning" effect="plain" size="mini" style="font-size: 14px;">商品已失效</el-tag>
                                            </span>
                                            {{ scope.row.goods_name || '暂无信息' }}
                                        </div>
                                        <div style="margin-top: 10px;font-size: 12px;color: #d0d0d0;word-break: break-all;display: -webkit-box;
                                            -webkit-box-orient: vertical;overflow: hidden;-webkit-line-clamp: 2;">
                                            {{ scope.row.sku_info }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="price" label="单价" width="215" align="center">
                            <template slot-scope="scope" style="font-size: 16px;color: #282828;">
                                ￥{{ scope.row.price }}
                            </template>
                        </el-table-column>
                        <el-table-column label="数量" width="150" align="center">
                            <template slot-scope="scope">
                                <el-input-number v-model="scope.row.num" @change="numChange(scope.row)" :min="1" :max="scope.row.amount!=undefined? scope.row.amount:99" style="width: 140px;"></el-input-number>
                                <span v-if="scope.row.amount!= undefined" style="font-size: 12px;margin-top: 3px;color: #90939C;">{{ '库存('+ scope.row.amount +')' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="金额" width="270" align="center">
                            <template slot-scope="scope">
                                <div style="color: #e93323;font-size: 16px;">
                                    ￥{{ scope.row.num?scope.row.price * scope.row.num:0}}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="65" align="center">
                            <template slot-scope="scope">
                                <i style="font-size: 20px;color:#d0d0d0;cursor: pointer;" class="el-icon-delete" @click="toDelete(scope.row.sid)"></i>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
                <div style="height: 82px;background: #f9f9f9;margin-top: 60px;padding-left: 30px;margin-bottom: 30px;align-items: center;
                    justify-content: space-between;display: flex;flex-wrap: wrap;">
                    <div style="color: #282828;font-size: 14px;">{{'已选 '+ choosed.length+' 件商品'}}</div>
                    <div style="display: flex;flex-wrap: wrap;align-items: center;color: #282828;font-size: 14px;">
                        <div style="font-size: 16px;">
                            {{"合计 : "}}
                            <span style="font-size: 22px;font-weight: 700;color: #e93323;">
                                ￥{{ total }}
                            </span>
                        </div>
                        <div style="width: 160px;height: 82px;text-align: center;line-height: 82px;font-size: 18px;color: #fff;
                            margin-left: 30px;background-color: #e93323;cursor: pointer;">去结算</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { post } from '@/api/MyAxios';
import { mapGetters } from 'vuex';
export default {
    data() {
        return {
            active: 0,
            edit: false,
            editForm: {
                login_name: '',
                nick_name: '',
                email: '',
                remark: ''
            },
            addressDialog: false,
            newAddress: {
                name: '',
                phone: '',
                area: '',
                detail: '',
                default: false
            },
            choosed:[],
            addressData: [],
            total:0.00
        }
    },
    computed: {
        ...mapGetters([
            'userInfo',
            'shoppingCart'
        ])
    },
    mounted() {
        this.getShoppingCart()
    },
    methods: {
        toBack() {
            this.$router.back();
        },
        handleSelectionChange(val) {
            this.choosed=val;
            this.total=0
            for(let i=0;i<this.choosed.length;i++){
                this.total+= this.choosed[i].price* this.choosed[i].num
            }
        },
        getShoppingCart(){
            let p = new FormData()
            p.append('uid', this.userInfo.uid)
            post('ShoppingCart/get', p).then(res => {
                if (res.code == 200) {
                    this.$store.commit('setShoppingCart', res.data.records)
                    this.$refs.multipleTable.toggleAllSelection()
                }
            })
        },
        numChange(row){
            this.total = 0
            for (let i = 0; i < this.choosed.length; i++) {
                this.total += this.choosed[i].price * (this.choosed[i].num || 0)
            }
            let p = new FormData()
            p.append('sid',row.sid)
            p.append('num', row.num)
            post('ShoppingCart/save', p)
        },
        toDelete(sid){
            let _this=this
            this.$confirm('确认要移除该商品吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new URLSearchParams();
                params.append('id', JSON.stringify([sid]));
                post('ShoppingCart/delete', params).then(res => {
                    if (res.code == 200) {
                        _this.getShoppingCart()
                        _this.$message({
                            type: 'success',
                            message: res.msg
                        });
                    }
                    else {
                        _this.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                }).catch(err => {
                    console.log(err)
                });
            }).catch(() => {
                _this.$message({
                    type: 'info',
                    message: '操作取消'
                });
            });
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
::v-deep .el-checkbox__inner{
    border-color: #E93323 !important;
}
::v-deep .el-checkbox__input.is-checked .el-checkbox__inner{
    background-color: #E93323 !important;
}
</style>