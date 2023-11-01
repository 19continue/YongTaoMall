<template>
    <div style="padding: 30px;">
        <div  style="width: 800px;margin: 0 auto;">
            <el-carousel :interval="5000" arrow="hover" height="350px">
                <el-carousel-item v-for="(img, index) in Images" :key="index">
                    <el-image shape="square" :fit="img.fit ? img.fit : 'cover'"
                        style="width: 800px;height: 350px;"
                        :src="img.src"></el-image>
                </el-carousel-item>
            </el-carousel>
        </div>
        <Table style="margin-top: 10px;" ref="myTable" :tableColumn="tableColumn" :tableButton="tableButton" :head="head" :attribute="tableAttribute"
            :authority="authority" @handleDelete="handleDelete" @handleMultiDelete="handleMultiDelete"
            @handleAdd="handleAdd" @handleEdit="handleEdit" @queryPage="queryPage" @handleSearch="handleSearch"
            @load="onLoad">
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
                { type: 'text', label: 'ID', prop: 'id',  align: 'center', addHide: true, notEditable: true },
                { type: 'image', label: '图片', prop: 'src', align: 'center' },
                { type: 'text', label: 'URL', prop: 'url', salign: 'center'},
                { type: 'selectTag', label: 'fit', prop: 'fit', salign: 'center' , 
                    option: [{ label: 'cover', value: 'cover', type: 'success' },
                            { label: 'contain', value: 'contain', type: 'success' },
                            { label: 'fill', value: 'fill', type: 'success' },
                            { label: 'scale-down', value: 'scale-down', type: 'success' },
                            { label: 'none', value: 'none', type: 'success' }],
                    align: 'center', default: 'cover'
                },
                { type: 'number', label: 'sort', prop: 'sort', align: 'center',default:0 },
                {
                    type: 'switch', label: '状态', prop: 'status',
                    option: [{ label: '显示', value: 1, type: 'success' },
                    { label: '隐藏', value: 0, type: 'danger' }],
                    align: 'center', default: 1
                }
            ],
            head: {
                placeholder: '输入ID搜索',
                defaultSearch: 'id',
                add: true,
                addName:'添加',
                delete: true,
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
                title: '添加轮播图'
            },
            editAttribute: {
                show: false,
                type: 'edit',
                primaryKey: 'id',
                title: '编辑轮播图'
            },
            authority: {
                edit: false,
                add: false,
                delete: false
            },
            url: {
                get: 'Carousel/get',
                edit: 'Carousel/save',
                add: 'Carousel/save',
                delete: 'Carousel/delete'
            },
            Images:[]
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
                if (condition.hasOwnProperty('id')) {
                    params.append('id', condition['id'])
                }
            }
            post(this.url.get, params).then(res => {
                if (res.code == 200) {
                    this.Images=[]
                    for (let i = 0; i < res.data.records.length; i++) {
                        if (res.data.records[i].status==1) {
                            this.Images.push(res.data.records[i])
                        }
                    }
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
            arr.push(row.id)
            this.delete(arr)
        },
        handleMultiDelete(val) {
            let arr = [];
            for (let i = 0; i < val.length; i++) {
                arr.push(val[i].id)
            }
            this.delete(arr)
        },
        delete(idArray) {
            if (!idArray || idArray.length == 0) {
                return
            }
            let params = new URLSearchParams();
            params.append('id', JSON.stringify(idArray));
            post(this.url.delete, params).then(res => {
                if (res.code == 200) {
                    this.dataRefresh();
                    this.$message({
                        type: 'success',
                        message: res.msg
                    });
                }
                else {
                    this.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            }).catch(err => {
                console.log(err)
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
