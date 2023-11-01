<template>
    <div style="padding: 30px;">
        <Table ref="myTable" :tableColumn="tableColumn" :tableButton="tableButton" :head="head" :authority="authority"
            @handleDelete="handleDelete" @handleMultiDelete="handleMultiDelete" @handleAdd="handleAdd"
            @handleEdit="handleEdit" @queryPage="queryPage" @handleSearch="handleSearch" @handlereset="handleReset">
        </Table>
        <Drawer ref="addDrawer" :show="show" :attribute="addAttribute" :labelData="tableColumn" :url="url.add"
            :rules="rules" @refresh="dataRefresh">
        </Drawer>
        <Drawer ref="editDrawer" :show="show" :attribute="editAttribute" :formData="formData" :labelData="tableColumn"
            :url="url.edit" :rules="rules" @refresh="dataRefresh"
            @getSelectOptionparent="(row) => { getSelectOptionParent('edit', row) }">
        </Drawer>
    </div>
</template>

<script>
import Table from '@/components/Table.vue';
import Drawer from '@/components/Drawer.vue';
import { selectButton } from '@/api';
import { mapGetters } from 'vuex';
import { post } from '@/api/MyAxios'
export default {
    components: {
        Table,
        Drawer
    },
    data() {
        var checkNumber = (rule, value, callback) => {
            if (!value) {
                return callback(new Error('请输入总学分'));
            }
            setTimeout(() => {
                if (!/^-?\d+\.?\d*$/.test(value)) {
                    callback(new Error('请输入数字类型'));
                }
                else {
                    callback();
                }
            }, 1000);
        }
        return {
            tableColumn: [
                { type: 'text', label: 'ID', prop: 'store_id', sortable: false, align: 'center', addHide: true, notEditable: true },
                { type: 'text', label: '店铺名称', prop: 'store_name', align: 'center', notEditable: true },
                { type: 'image', label: '图片', prop: 'avatar', align: 'center' },
                { type: 'number', label: '金额', prop: 'money', align: 'center', precision: 2, min: 0, max: 100 , editHide: true },
                { type: 'number', label: '收入', prop: 'income', addHide: true, editHide: true },
                { type: 'number', label: '总销售量', prop: 'sales', align: 'center', addHide: true, editHide: true },
                { type: 'number', label: '商品描述', prop: 'goods_score', align: 'center', precision: 1, min: 0, max:5 },
                { type: 'number', label: '卖家服务', prop: 'service_score', align: 'center', precision: 1, min: 0, max:5 },
                { type: 'number', label: '物流服务', prop: 'logistics_score', align: 'center', precision: 1, min: 0, max: 5},
                { type: 'text', label: '联系人', prop: 'name', notEditable: true },
                { type: 'text', label: '手机号', prop: 'phone', notEditable: true },
                {
                    type: 'switch', label: '营业状态', prop: 'status',
                    option: [{ label: '营业中', value: 1, type: 'success' },
                    { label: '已关门', value: 0, type: 'danger' }],
                    align: 'center', default: 1
                },
                { type: 'date', label: '创建时间', prop: 'create_time', align: 'center', notEditable: true, addHide: true, editHide: true },
            ],
            head: {
                placeholder: '输入顶级目录ID搜索',
                defaultSearch: 'uid',
                add: true,
                delete: true,
                selects: [
                    { label: '店铺ID', prop: 'store_id', type: 'text' },
                    { label: '用户ID', prop: 'uid', type: 'text' },
                    { label: '店铺名称', prop: 'store_name', type: 'text' },
                    { label: '联系人', prop: 'name', type: 'text' },
                    { label: '手机号', prop: 'phone', type: 'text' },
                    {
                        label: '营业状态', prop: 'status', type: 'select', default: '',
                        option: [
                            { label: '全部', value: '' },
                            { label: '营业中', value: true },
                            { label: '已关门', value: false }],
                    },
                    { label: '创建时间', prop: 'create_time', type: 'date', start: 'create_start', end: 'create_end' },
                ]
            },
            tableButton: [
                { handle: 'edit', content: '查看详情', type: 'primary', isIcon: true, icon: 'el-icon-edit', show: true },
                { handle: 'delete', content: '删除', type: 'danger', isIcon: true, icon: 'el-icon-delete', show: true }
            ],
            rules: {
            },

            show: false,
            formData: {},
            addAttribute: {
                show: true,
                type: 'add',
                title: '创建用户'
            },
            editAttribute: {
                show: false,
                type: 'edit',
                primaryKey: 'uid',
                title: '用户编辑'
            },
            authority: {
                edit: false,
                add: false,
                delete: false,
                reset: false
            },
            url: {
                get: 'Store/get',
                edit: 'User/save',
                add: 'User/save',
                delete: 'User/delete'
            }
        }
    },
    computed: {
        ...mapGetters([
            'info',
            'menuId'
        ])
    },
    created() {
        this.getButton()
    },
    mounted() {
        this.queryPage(10, 1)
    },
    methods: {
        getButton() {
            let params = new URLSearchParams();
            params.append("group_id", this.info.group_id);
            params.append("menu_id", this.menuId);
            selectButton(params).then(res => {
                if (res.code == 200) {
                    if (res.data) {
                        switch (res.data.button) {
                            case 7:
                                this.authority.add = true;
                                this.authority.edit = true;
                                this.authority.delete = true;
                                break;
                            case 6:
                                this.authority.add = false;
                                this.authority.edit = true;
                                this.authority.delete = true;
                                break;
                            case 5:
                                this.authority.add = true;
                                this.authority.edit = false;
                                this.authority.delete = true;
                                break;
                            case 4:
                                this.authority.add = false;
                                this.authority.edit = false;
                                this.authority.delete = true;
                                break;
                            case 3:
                                this.authority.add = true;
                                this.authority.edit = true;
                                this.authority.delete = false;
                                break;
                            case 2:
                                this.authority.add = false;
                                this.authority.edit = true;
                                this.authority.delete = false;
                                break;
                            case 1:
                                this.authority.add = true;
                                this.authority.edit = false;
                                this.authority.delete = false;
                                break;
                            default:
                                this.authority.add = false;
                                this.authority.edit = false;
                                this.authority.delete = false;
                                break;
                        }
                    }
                    else {
                        this.authority.add = false;
                        this.authority.edit = false;
                        this.authority.delete = false;
                        this.$message({
                            message: '按钮权限信息获取失败!',
                            type: 'error'
                        });
                    }
                }
            }).catch(err => {
                this.$message({
                    message: '按钮权限信息获取失败!',
                    type: 'error'
                });
            })
        },
        queryPage(pageSize, currentPage, condition) {
            this.$refs.myTable.dataLoading = true
            let params = new URLSearchParams();
            params.append('pageSize', pageSize);
            params.append('currentPage', currentPage);
            if (condition && Object.prototype.toString.call(condition) === '[object Object]') {
                if (condition.hasOwnProperty('uid')) {
                    params.append('uid', condition['uid'])
                }
                if (condition.hasOwnProperty('login_name')) {
                    params.append('login_name', condition['login_name'])
                }
                if (condition.hasOwnProperty('nick_name')) {
                    params.append('nick_name', condition['nick_name'])
                }
                if (condition.hasOwnProperty('phone')) {
                    params.append('phone', condition['phone'])
                }
                if (condition.hasOwnProperty('level')) {
                    params.append('level', condition['level'])
                }
                if (condition.hasOwnProperty('status')) {
                    params.append('status', condition['status'])
                }
                if (condition.hasOwnProperty('login_start') && condition.hasOwnProperty('login_end')) {
                    params.append('login_start', condition['login_start'] + ' 00:00:00')
                    params.append('login_end', condition['login_end'] + ' 23:59:59')
                }
                if (condition.hasOwnProperty('create_start') && condition.hasOwnProperty('create_end')) {
                    params.append('create_start', condition['create_start'] + ' 00:00:00')
                    params.append('create_end', condition['create_end'] + ' 23:59:59')
                }
            }
            post(this.url.get, params).then(res => {
                if (res.code == 200) {
                    this.$refs.myTable.getData(res.data.records);
                    this.$refs.myTable.changeTotal(res.data.total);
                }
                this.$refs.myTable.dataLoading = false
            }).catch(err => {
                console.log(err)
            });
        },
        dataRefresh() {
            this.$refs.myTable.queryPage();
        },
        handleDelete(row) {
            let arr = [];
            arr.push(row.uid)
            this.delete(arr)
        },
        handleReset(row) {
            let _this = this
            this.$confirm('确认要重置该用户的密码吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new FormData()
                params.append('uid', row.uid)
                post(_this.url.reset, params).then(res => {
                    if (res.code == 200) {
                        _this.$message({
                            type: 'success',
                            message: res.msg
                        });
                    }
                })
            }).catch(() => {
                _this.$message({
                    type: 'info',
                    message: '已取消操作'
                });
            });

        },
        handleMultiDelete(val) {
            let arr = [];
            for (let i = 0; i < val.length; i++) {
                arr.push(val[i].uid)
            }
            this.delete(arr)
        },
        delete(idArray) {
            if (!idArray || idArray.length == 0) {
                return
            }
            let _this = this
            this.$confirm('确认要删除该用户吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new URLSearchParams();
                params.append('id', JSON.stringify(idArray));
                post(_this.url.delete, params).then(res => {
                    if (res.code == 200) {
                        _this.dataRefresh();
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
                    message: '已取消删除'
                });
            });
        },
        handleAdd() {
            this.$refs.addDrawer.toShow();
        },
        handleEdit(row) {
            this.$refs.editDrawer.getNewData(row);
            this.$refs.editDrawer.toShow();
        },
        handleSearch(pageSize, currentPage, condition) {
            this.queryPage(pageSize, currentPage, condition)
        }
    }
}
</script>
<style lang="scss" scoped></style>