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
                { type: 'text', label: 'ID', prop: 'uid', sortable: false, align: 'center', addHide: true, notEditable: true },
                { type: 'text', label: '用户名', prop: 'login_name', align: 'center',notEditable: true },
                { type: 'text', label: '昵称', prop: 'nick_name', align: 'center' },
                { type: 'text', label: '密码', prop: 'password', align: 'center',tableHide: true, editHide: true},
                { type: 'image', label: '头像', prop: 'avatar' },
                { type: 'text', label: '手机号', prop: 'phone', align: 'center',notEditable: true },
                { type: 'text', label: '邮箱', prop: 'email',  align: 'center' },
                { type: 'textarea', label: '备注', prop: 'remark', align: 'center' , tableHide: true },
                { type: 'number', label: '余额', prop: 'money', align: 'center', precision:2, min:0 , max:99999, notEditable: true },
                {
                    type: 'switch', label: '状态', prop: 'status',
                    option: [{ label: '正常', value: 1, type: 'success' },
                    { label: '封禁', value: 0, type: 'danger' }],
                    align: 'center', default: 1
                },
                { type: 'date', label: '最后登录', prop: 'login_time', align: 'center' , addHide: true, editHide: true },
                { type: 'date', label: '创建时间', prop: 'create_time', align: 'center', notEditable: true, addHide: true, editHide: true },
                { type: 'text', label: '等级', prop: 'level', align: 'center',notEditable: true },
            ],
            head: {
                placeholder: '输入顶级目录ID搜索',
                defaultSearch: 'uid',
                add: true,
                delete: true,
                selects: [
                    { label: 'ID', prop: 'uid', type: 'text' },
                    { label: '用户名', prop: 'login_name', type: 'text' },
                    { label: '昵称', prop: 'nick_name', type: 'text' },
                    { label: '手机号', prop: 'phone', type: 'text' },
                    { label: '最小等级', prop: 'level', type: 'text' },
                    {
                        label: '状态', prop: 'status', type: 'select', default: '',
                        option: [
                            { label: '全部', value: '' },
                            { label: '正常', value: true },
                            { label: '封禁', value: false }],
                    },
                    { label: '登录时间', prop: 'login_time', type: 'date', start: 'login_start', end: 'login_end' },
                    { label: '创建时间', prop: 'create_time', type: 'date', start: 'create_start', end: 'create_end' },
                ]
            },
            tableButton: [
                { handle: 'reset', content: '重置密码', type: 'primary', isIcon: true, icon: 'el-icon-key', show: true },
                { handle: 'edit', content: '查看详情', type: 'primary', isIcon: true, icon: 'el-icon-edit', show: true },
                { handle: 'delete', content: '删除', type: 'danger', isIcon: true, icon: 'el-icon-delete', show: true }
            ],
            rules: {
                // id: [
                //     { required: true, message: '请输入学号', trigger: 'blur' },
                //     { min: 6, max: 6, message: '长度必须为6个字符', trigger: 'blur' }
                // ],
                // username: [
                //     { required: true, message: '请输入姓名', trigger: 'blur' },
                //     { min: 1, max: 8, message: '长度必须小于8个字符', trigger: 'blur' }
                // ],
                // CSSJ: [
                //     { required: true, message: '请选择日期', trigger: 'blur' }
                // ],
                // ZY: [
                //     { required: true, message: '请输入专业', trigger: 'blur' },
                //     { min: 1, max: 12, message: '长度必须小于12个字符', trigger: 'blur' }
                // ],
                // ZXF: [
                //     { validator: checkNumber, trigger: 'blur' }
                // ]
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
                reset:false
            },
            url: {
                get: 'User/get',
                edit: 'User/save',
                add: 'User/save',
                delete: 'User/delete',
                reset:'User/reset'
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
                        this.authority.reset= this.authority.edit
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
            this.$refs.myTable.dataLoading=true
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
                if (condition.hasOwnProperty('login_start')&& condition.hasOwnProperty('login_end')) {
                    params.append('login_start', condition['login_start'] + ' 00:00:00')
                    params.append('login_end', condition['login_end'] + ' 23:59:59')
                }
                if (condition.hasOwnProperty('create_start') && condition.hasOwnProperty('create_end')) {
                    params.append('create_start', condition['create_start']+' 00:00:00')
                    params.append('create_end', condition['create_end']+' 23:59:59')
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
        handleReset(row){
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