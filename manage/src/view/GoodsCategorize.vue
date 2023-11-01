<template>
    <div style="padding: 30px;">
        <Table ref="myTable" :tableColumn="tableColumn" :tableButton="tableButton" :head="head" :authority="authority"
            @handleDelete="handleDelete" @handleMultiDelete="handleMultiDelete" @handleAdd="handleAdd"
            @handleEdit="handleEdit" @queryPage="queryPage" @handleSearch="handleSearch">
        </Table>
        <Drawer ref="addDrawer" :show="show" :attribute="addAttribute" :labelData="tableColumn" :url="url.add"
            :rules="rules" @refresh="dataRefresh" @getSelectOptionparent="(row) => { getSelectOptionParent('add', row) }">
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
                { type: 'text', label: 'ID', prop: 'class_id', sortable: false,width:120 ,align: 'center', addHide: true, notEditable: true },
                { type: 'image', label: '图片', prop: 'picture' },
                { type: 'text', label: '分类名称', prop: 'name', align: 'center' },
                { type: 'text', label: '排序', prop: 'sort', sortable: true, align: 'center', default: 0, },
                {
                    type: 'selectTag', label: '上级分类', prop: 'parent', tableHide: true, default: 0, option: [
                        { label: '顶级类目', value: 0 }], loading: false
                },
                {
                    type: 'switch', label: '状态', prop: 'status',
                    option: [{ label: '显示', value: 1, type: 'success' },
                    { label: '隐藏', value: 0, type: 'danger' }],
                    align: 'center', default: 1
                },
            ],
            head: {
                placeholder: '输入顶级目录ID搜索',
                defaultSearch: 'class_id',
                add: true,
                delete: true
            },
            tableButton: [
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
                title: '创建商品分类'
            },
            editAttribute: {
                show: false,
                type: 'edit',
                primaryKey: 'class_id',
                title: '商品分类编辑'
            },
            authority: {
                edit: false,
                add: false,
                delete: false
            },
            url: {
                get: 'Categorize/get',
                edit: 'Categorize/save',
                add: 'Categorize/save',
                delete: 'Categorize/delete'
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
        async getSelectOptionParent(way, row) {
            this.tableColumn[4].loading = true
            this.tableColumn[4].option = []
            this.tableColumn[4].option.push({ label: '顶级类目', value: 0 })
            if (way == 'add') {
                await post('Categorize/getTop').then(res => {
                    if (res.code == 200) {
                        for (let i = 0; i < res.data.length; i++) {
                            this.tableColumn[4].option.push({
                                label: res.data[i].name,
                                value: res.data[i].class_id
                            })
                        }

                    }
                }).catch(err => {
                    console.log(err)
                });
            } else if (way == 'edit') {
                await post('Categorize/getTop').then(res => {
                    if (res.code == 200) {
                        for (let i = 0; i < res.data.length; i++) {
                            if (row.class_id == res.data[i].class_id) {
                                continue
                            }
                            this.tableColumn[4].option.push({
                                label: res.data[i].name,
                                value: res.data[i].class_id
                            })
                        }

                    }
                }).catch(err => {
                    console.log(err)

                });
            }
            this.tableColumn[4].loading = false
        },
        queryPage(pageSize, currentPage, condition) {
            this.$refs.myTable.dataLoading = true
            let params = new URLSearchParams();
            params.append('pageSize', pageSize);
            params.append('currentPage', currentPage);
            if (condition && Object.prototype.toString.call(condition) === '[object Object]') {
                if (condition.hasOwnProperty('class_id')) {
                    params.append('class_id', condition['class_id'])
                }
                if (condition.hasOwnProperty('name')) {
                    params.append('name', condition['name'])
                }
                if (condition.hasOwnProperty('status')) {
                    params.append('status', condition['status'])
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
            arr.push(row.class_id)
            this.delete(arr)
        },
        handleMultiDelete(val) {
            let arr = [];
            for (let i = 0; i < val.length; i++) {
                arr.push(val[i].class_id)
            }
            this.delete(arr)
        },
        delete(idArray) {
            if (!idArray || idArray.length == 0) {
                return
            }
            let _this = this
            this.$confirm('确认要删除该类别吗?', '提示', {
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