<template>
    <div style="width: 1200px;margin: 0 auto;margin-top: 10px;">
        <div @click="toBack()"
            style="margin-left: 10px;color: #606266 !important;cursor: pointer;width: 50px;font-size: 14px;">
            <i class="ri-arrow-left-s-line"></i><span>返回</span>
        </div>
        <div style="display: flex;margin-top: 10px;">
            <div style="display: flex;flex-direction: column;width: 180px;">
                <div
                    style="height: 180px;background-color: #fff;box-sizing: border-box;display: flex;align-items: center;flex-direction: column;">
                    <el-image shape="square" fit="contain" style="width: 100px;height: 100px;margin-top: 20px;"
                        :src="userInfo.avatar" :preview-src-list="[userInfo.avatar]"></el-image>
                    <div
                        style="overflow: hidden;text-overflow: ellipsis;max-width: 120px;margin: 0 auto;font-size: 14px;color: #606266;margin-top: 10px;">
                        {{ userInfo.nick_name }}
                    </div>
                </div>
                <div style="height: 312px;background-color: #fff;margin-top: 8px;box-sizing: border-box;padding-top: 20px;">
                    <div :class="active == 0 ? 'menu-item active' : 'menu-item'" @click="active = 0">
                        <i v-show="active == 0" class="ri-arrow-right-s-fill"></i>
                        <span>个人资料</span>
                        <i v-show="active == 0" class="ri-arrow-left-s-fill"></i>
                    </div>
                    <div :class="active == 1 ? 'menu-item active' : 'menu-item'" @click="active = 1, edit = false">
                        <i v-show="active == 1" class="ri-arrow-right-s-fill"></i>
                        <span>我的订单</span>
                        <i v-show="active == 1" class="ri-arrow-left-s-fill"></i>
                    </div>
                    <div :class="active == 2 ? 'menu-item active' : 'menu-item'" @click="active = 2, edit = false">
                        <i v-show="active == 2" class="ri-arrow-right-s-fill"></i>
                        <span>我的收藏</span>
                        <i v-show="active == 2" class="ri-arrow-left-s-fill"></i>
                    </div>
                    <div :class="active == 3 ? 'menu-item active' : 'menu-item'" @click="active = 3, edit = false">
                        <i v-show="active == 3" class="ri-arrow-right-s-fill"></i>
                        <span>地址管理</span>
                        <i v-show="active == 3" class="ri-arrow-left-s-fill"></i>
                    </div>
                    <div :class="active == 4 ? 'menu-item active' : 'menu-item'" @click="active = 4, edit = false">
                        <i v-show="active == 4" class="ri-arrow-right-s-fill"></i>
                        <span>我的优惠券</span>
                        <i v-show="active == 4" class="ri-arrow-left-s-fill"></i>
                    </div>
                </div>
            </div>
            <div
                style="width: 1012px;margin-left: 8px;background-color: #fff;min-height: 500px;
                box-sizing: border-box;padding-left: 56px;padding-right: 56px;padding-top: 15px;padding-bottom: 30px;margin-bottom: 40px;">
                <div v-if="active == 0" style="color: #282828;font-size: 16px;">
                    <div>个人资料</div>
                    <el-divider></el-divider>
                    <el-form label-position="left" label-width="100px" :model="userInfo">
                        <el-form-item label="我的头像:">
                            <div style="position: relative;width: 80px;" class="avatar">
                                <el-image style="width: 60px;height: 60px;border-radius: 50%;"
                                    :src="userInfo.avatar"></el-image>
                                <div class="button" style="width: 60px;height: 25px;position: absolute;background: rgba(0,0,0,.4);top: 40px;
                                    left: 0px;line-height: 26px;color: #fff;font-size: 12px;font-weight: bold;text-align: center;
                                    cursor: pointer;display: none;">
                                    修改头像
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="等级:">
                            <div v-if="userInfo.level == 0">
                                <i class="ri-vip-crown-2-line"></i>
                                <span style="font-size: 12px;"> {{ ' 普通用户' }} </span>
                            </div>
                            <div v-else>
                                <i class="ri-vip-crown-2-fill"></i>
                                <span style="font-size: 12px;">{{ ' vip用户' }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="用户名:">
                            <div v-if="edit">
                                <el-input v-model="editForm.login_name" :placeholder="userInfo.login_name"
                                    style="display: block;width: 500px;"></el-input>
                            </div>
                            <div v-else style="width: 700px;overflow: hidden;text-overflow: ellipsis;">
                                <span>{{ userInfo.login_name }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="昵称:">
                            <div v-if="edit">
                                <el-input v-model="editForm.nick_name" :placeholder="userInfo.nick_name"
                                    style="display: block;width: 500px;"></el-input>
                            </div>
                            <div v-else style="width: 700px;overflow: hidden;text-overflow: ellipsis;">
                                <span>{{ userInfo.nick_name }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="余额:">
                            <div style="width: 700px;overflow: hidden;text-overflow: ellipsis;">
                                <span>{{ userInfo.money }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="手机号:">
                            <div v-if="edit">
                                <el-button size="mini">修改手机号</el-button>
                            </div>
                            <div v-else style="width: 700px;overflow: hidden;text-overflow: ellipsis;">
                                <span>{{ userInfo.phone }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="邮箱:">
                            <div v-if="edit">
                                <el-input v-model="editForm.email" :placeholder="userInfo.email ? userInfo.email : '请输入邮箱'"
                                    style="display: block;width: 500px;"></el-input>
                            </div>
                            <div v-else style="width: 700px;overflow: hidden;text-overflow: ellipsis;">
                                <span>{{ userInfo.email }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="备注:">
                            <div v-if="edit">
                                <el-input type="textarea" v-model="editForm.remark"
                                    :placeholder="userInfo.remark ? userInfo.remark : '该用户很懒，什么也没留下...'" autosize
                                    style="display: block;width: 500px;"></el-input>
                            </div>
                            <div v-else style="width: 700px;overflow: hidden;text-overflow: ellipsis;">
                                <span>{{ userInfo.remark }}</span>
                            </div>
                        </el-form-item>
                        <el-form-item style="display: flex;justify-content: end;">
                            <el-button v-if="edit" type="primary" @click="edit = false">保存</el-button>
                            <el-button v-else type="primary" @click="edit = true">编辑资料</el-button>
                            <el-button type="primary" @click=";">修改密码</el-button>
                            <el-button type="danger" @click="toLogout()">退出登录</el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <div v-if="active == 2">
                    <div style="width: 100%;display: flex;flex-direction: row;flex-wrap: wrap;">
                        <div style="width: 182px;height: 240px;padding: 16px;box-sizing: border-box;margin: 15px 15px 0 0;
                        background-color: white;box-shadow: 0 3px 20px rgba(241,242,242, 1);position: relative;"
                            class="collect-item" v-for="(good, idx) in collectData" :key="idx">
                            <div class="collect-delete" @click="removeToCollect(good.gid)"><i class="ri-delete-bin-line"></i></div>
                            <div class="collect-store" @click=";">进入店铺</div>
                            <div @click="toThisGoods(good)">
                                <el-image shape="square" style="width: 150px; height: 150px" fit="cover"
                                    :src="good.src"></el-image>
                            </div>
                            <div
                                style="width: 150px;height: 31px;font-size: 12px;color: #5a5a5a;overflow: hidden;text-overflow: ellipsis;
                            display: -webkit-box;-webkit-line-clamp:2;line-clamp: 2;-webkit-box-orient: vertical;" @click="toThisGoods(good)">
                                {{ good.goods_name }}
                            </div>
                            <div v-if="good.delete_status == 1 && good.status == 1" style="width: 150px;margin-top: 5px;display: flex;flex-direction:row;align-items: center;">
                                <div
                                    style="font-size: 16px;font-weight: 700;color: #e93323;overflow: hidden;text-overflow: ellipsis;">
                                    ￥{{ good.s_price }}
                                </div>
                                <div
                                    style="color: #aaa;font-size: 12px;text-decoration: line-through;margin-left: 5px;margin-top: 2px;overflow: hidden;text-overflow: ellipsis;max-width: 65px;">
                                    ￥{{ good.o_price }}
                                </div>
                            </div>
                            <div v-else style="text-align: center;font-size: 16px;color: #909399;margin-top: 5px;">
                                <i class="ri-emotion-unhappy-fill"></i>
                                <span style="font-size: 14px;">商品失效了</span>
                            </div>
                        </div>
                    </div>
                    <el-empty v-if="collectData.length == 0" description="先收藏一些商品再来吧" style="margin-top: 50px;margin-bottom: 50px;"></el-empty>
                </div>
                <div v-if="active == 3" style="color: #282828;font-size: 16px;">
                    <div>地址管理</div>
                    <el-divider></el-divider>
                    <div style="width: 900px;display: flex;flex-wrap: wrap;">
                        <div class="address-item" v-for="(item, index) in addressData" :key="index">
                            <div
                                style="color: #282828;font-size: 16px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">
                                {{ item.name }}
                            </div>
                            <div style="    margin-top: 14px;margin-bottom: 4px;font-size: 12px;">
                                {{ item.phone }}
                            </div>
                            <div
                                style="color: #999;font-size: 14px;height: 75px;-webkit-line-clamp: 4;word-break: break-all;display: -webkit-box;-webkit-box-orient: vertical;overflow: hidden;">
                                {{ item.area + ' ' + item.detail }}
                            </div>
                            <div class="address-menu">
                                <span v-if="item.is_default != 1" style="margin-left: 5px;"
                                    @click="setToDefault(item.address_id)">设为默认地址</span>
                                <span style="margin-left: 5px;">修改</span>
                                <span style="margin-left: 5px;" @click="toAddressDelete(item.address_id)">删除</span>
                            </div>
                            <div v-if="item.is_default == 1" style="position: absolute;right: 0;top: 0;width: 56px;height: 23px;
                                line-height: 23px;text-align: center;color: #fff;background: #e93323;font-size: 14px;">
                                默认</div>
                        </div>
                        <div class="address-item" @click="addressDialog = true">
                            <div style="position: relative;top: 35%;text-align: center;color: #c8c8c8;font-size: 14px;">
                                <img style="width: 27px;height: 27px;" :src="require('@/assets/add.png')" alt="">
                                <p>添加新地址</p>
                            </div>
                        </div>
                    </div>
                    <el-dialog title="添加收货地址" :visible.sync="addressDialog" style="width: 1080px;margin: 0 auto;">
                        <div style="display: flex;flex-direction: column;">
                            <div style="display: flex;flex-direction: row;">
                                <div>
                                    <el-input v-model="newAddress.name" placeholder="姓名"
                                        style="display: block;width: 240px;"></el-input>
                                </div>
                                <div>
                                    <el-input v-model="newAddress.phone" placeholder="手机号"
                                        style="display: block;width: 245px;margin-left: 15px;"></el-input>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <el-cascader v-model="newAddress.area" :props="{ lazy: true, lazyLoad: lazyLoad }"
                                    placeholder="请选择省/市/区/街道"></el-cascader>
                            </div>
                            <div style="margin-top: 10px;">
                                <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 5 }" placeholder="详细地址"
                                    v-model="newAddress.detail" style="display: block;width: 500px;">
                                </el-input>
                            </div>
                            <div style="margin-top: 10px;">
                                <el-checkbox v-model="newAddress.default">设为默认</el-checkbox>
                            </div>
                        </div>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="addressDialog = false">取 消</el-button>
                            <el-button type="primary" @click="addAddress">确 定</el-button>
                        </div>
                    </el-dialog>
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
            addressData: [],
            collectData:[]
        }
    },
    computed: {
        ...mapGetters([
            'userInfo'
        ])
    },
    watch: {
        active: {
            handler(val, old) {
                if(this.active==2){
                    this.getCollectData()
                }
                else if(this.active==3){
                    this.getAddress()
                }
            },
            immediate: false,
            deep: true
        }
    },
    mounted() {
        if (this.$route.query.active !== undefined) {
            this.active = this.$route.query.active
        }
    },
    methods: {
        toBack() {
            this.$router.back();
        },
        getAddress() {
            let params = new FormData()
            params.append('uid', this.userInfo.uid)
            post('Address/get', params).then(res => {
                if (res.code == 200) {
                    this.addressData = res.data.records
                }
            })
        },
        addAddress() {
            let params = new FormData()
            params.append('name', this.newAddress.name)
            params.append('phone', this.newAddress.phone)
            params.append('area', JSON.stringify(this.newAddress.area))
            params.append('detail', this.newAddress.detail)
            params.append('uid', this.userInfo.uid)
            if (this.newAddress.default) {
                params.append('is_default', 1)
            } else {
                params.append('is_default', 0)
            }
            post('Address/save', params).then(res => {
                if (res.code == 200) {
                    this.$message.success(res.msg)
                    this.getAddress()
                }
                else {
                    this.$message.success(res.msg)
                }
            })
        },
        setToDefault(address_id) {
            let params = new FormData()
            params.append('address_id', address_id)
            params.append('is_default', 1)
            params.append('uid', this.userInfo.uid)
            post('Address/save', params).then(res => {
                if (res.code == 200) {
                    this.$message.success(res.msg)
                    this.getAddress()
                }
                else {
                    this.$message.success(res.msg)
                }
            })
        },
        toAddressDelete(address_id) {
            this.addressDelete([address_id])
        },
        addressDelete(idArray) {
            if (!idArray || idArray.length == 0) {
                return
            }
            let _this = this
            this.$confirm('确认要删除该地址吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new URLSearchParams();
                params.append('id', JSON.stringify(idArray));
                post('Address/delete', params).then(res => {
                    if (res.code == 200) {
                        _this.$message({
                            type: 'success',
                            message: res.msg
                        });
                        _this.getAddress();
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
                    message: '已取消删除'
                });
            });
        },
        lazyLoad(node, resolve) {
            let params = new FormData()
            if (node.level > 0) {
                params.append('parent', node.value)
            }
            post('City/get', params).then(res => {
                if (res.code == 200) {
                    let options = []
                    for (let i = 0; i < res.data.records.length; i++) {
                        options.push({
                            value: res.data.records[i].id,
                            label: res.data.records[i].name,
                            leaf: res.data.records[i].depth > 2
                        })
                    }
                    resolve(options)
                }
            })
        },
        getCollectData(){
            let params=new FormData()
            params.append('uid',this.userInfo.uid)
            post('Collect/get',params).then(res=>{
                if(res.code==200){
                    this.collectData=res.data.records
                }
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
        toLogout(){
            this.$store.commit('setIsLogin',false)
            this.$store.commit('setUserInfo', null)
            this.$store.commit('setShoppingCart', [])
            this.$router.replace('/')
        },
        removeToCollect(gid) {
            let _this = this
            this.$confirm('确认要从收藏夹移除该商品吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new FormData()
                params.append('uid', _this.userInfo.uid)
                params.append('gid', gid)
                post('Collect/delete', params).then(res => {
                    if (res.code == 200) {
                        _this.hasCollected = 0
                        _this.$message.success('移除成功')
                    }
                })
            }).catch(() => {
                _this.$message({
                    type: 'info',
                    message: '操作取消'
                });
            });
        }
    }
}
</script>
<style lang="scss" scoped>
.menu-item {
    margin-top: 20px;
    width: 180px;
    text-align: center;
    color: #666;
    cursor: pointer;
    font-size: 14px;
}

.active {
    color: #e93323;
}

.info-item {
    display: flex;
    flex-direction: row;

}

.avatar {
    &:hover>.button {
        display: block !important;
    }
}

.address-item {
    box-sizing: border-box;
    padding: 22px 27px;
    width: 250px;
    min-height: 200px;
    border: 1px solid #eaeaea;
    position: relative;
    cursor: pointer;
    margin-right: 30px;
    margin-bottom: 30px;

    .address-menu {
        opacity: 0;
        position: absolute;
        right: 14px;
        bottom: 12px;
        color: #e93323;
        margin-left: 5px;
    }

    &:hover .address-menu {
        animation-name: OpacityShow;
        animation-duration: 0.5s;
        opacity: 1;
    }

    @keyframes OpacityShow {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
}
.collect-delete{
    position: absolute;
    color: #fff;
    background: rgba(0,0,0,.4);
    height: 30px;
    width: 30px;
    right: 0px;
    top: 0px;
    z-index: 99;
    text-align: center;
    line-height: 30px;
    display: none;
    &:hover{
        background-color: #E93323;
    }
}
.collect-store{
    position: absolute;
    color: #fff;
    background: rgba(0,0,0,.4);
    height: 30px;
    width: 100%;
    left: 0px;
    bottom: 0px;
    z-index: 99;
    text-align: center;
    line-height: 30px;
    display: none;
    &:hover{
        background-color: #E93323;
    }
}
.collect-item:hover{
    box-shadow: 0 3px 20px rgba(0,0, 0, 0.15) !important;
    animation-name: showShadow;
    animation-duration: 0.3s;
    cursor: pointer;
    .collect-delete{
        display: block;
    }
    .collect-store{
        display: block;
    }
}

@keyframes showShadow{
    from{
        transform: scale(0.98);
    }
    to{
        transform: scale(0.995);
    }
}
</style>