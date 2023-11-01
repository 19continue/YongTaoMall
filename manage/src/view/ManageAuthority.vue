<template>
    <div style="padding: 30px;">
        <div class="head" style="">
            <div class="search" style="width: 200px;min-width: 150px;">
                <el-input v-model="search_key" class="input-with-select" size="mini" placeholder="输入关键字搜索">
                    <el-select v-model="select_key" slot="prepend" :placeholder="select_key">
                        <el-option label="ID" value="group_id"></el-option>
                        <el-option label="权限组名" value="group_name"></el-option>
                        <el-option label="创建人" value="nick_name"></el-option>
                    </el-select>
                </el-input>
            </div>
            <div class="add">
                <el-button size="mini" type="primary" @click="operate.add = true" icon="el-icon-circle-plus"
                    :disabled="!authority.add">创建</el-button>
            </div>
            <div class="add">
                <el-button size="mini" type="danger" @click=";" icon="el-icon-remove"
                    :disabled="!authority.delete">删除</el-button>
            </div>
        </div>
        <el-table
            :data="tableData.filter(data => !search_key || `${data[select_key]}`.toLowerCase().includes(search_key.toLowerCase()))"
            style="width: 100%;min-width: 400px;" max-height="510" empty-text v-loading="dataLoading"
            @selection-change="handleSelectionChange" border>
            <af-table-column type="selection" align="center"></af-table-column>
            <af-table-column sortable label="ID" prop="group_id" align="center"></af-table-column>
            <af-table-column label="权限组名" prop="group_name" align="center"></af-table-column>
            <af-table-column label="状态" width="80" align="center">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status > 0 ? 'success' : 'danger'">{{ scope.row.status > 0 ? '启用' : '禁用'
                    }}</el-tag>
                </template>
            </af-table-column>
            <af-table-column sortable label="创建时间" align="center">
                <template slot-scope="scope">
                    <div style="white-space: pre-line;">
                        {{ scope.row.create_time ? scope.row.create_time.replace(/\s/g, "\n") : '数据丢失' }}
                    </div>
                </template>
            </af-table-column>
            <af-table-column label="创建人ID" prop="create_people" align="center"></af-table-column>
            <af-table-column label="创建人昵称" prop="nick_name" align="center"></af-table-column>
            <af-table-column fixed="right" label="操作" width="120" align="center">
                <template slot-scope="scope">
                    <el-tooltip content="查看详情" placement="top">
                        <el-button @click="handleEdit(scope.row)" type="primary" :disabled="!authority.edit"
                            icon="el-icon-edit" size="small" style="padding: 5px;font-size: 16px;"></el-button>
                    </el-tooltip>
                    <el-tooltip content="删除" placement="top">
                        <el-button @click="handleDelete(scope.row)" type="danger" :disabled="!authority.delete"
                            icon="el-icon-delete" size="small" style="padding: 5px;font-size: 16px;"></el-button>
                    </el-tooltip>
                </template>
            </af-table-column>
        </el-table>
        <el-pagination style="margin-top: 12px;text-align: right;" background @size-change="sizeChange"
            @current-change="currentChange" :current-page="page.currentPage" :page-sizes="[6, 10, 20, 50]"
            :page-size="page.pageSize" layout="total, sizes, prev, pager, next, jumper" :total="page.pageTotal">
        </el-pagination>
        <el-drawer :visible.sync="operate.add" size="50%" class="drawer">
            <div slot="title" class="title">创建权限组<el-divider></el-divider></div>
            <div class="content" v-loading="addLoading" element-loading-text="正在创建"
                element-loading-background="rgba(255, 255, 255, 0.8)">
                <el-form :model="newGroup" :rules="groupRules" ref="newGroup" label-position="left" label-width="100px">
                    <el-form-item label="权限组名" prop="group_name">
                        <el-input v-model="newGroup.group_name" autocomplete="off" placeholder="权限组名"></el-input>
                    </el-form-item>
                    <el-form-item label="权限">
                        <el-tree ref="menuTree" :data="menuTree" show-checkbox node-key="id" label="label">
                        </el-tree>
                    </el-form-item>
                    <el-form-item label="状态" prop="status">
                        <el-switch v-model="newGroup.status" active-color="#1989FA" inactive-color="#F56C6C">
                        </el-switch>
                        <label style="padding-left: 30px;color: #909399;">{{ newGroup.status ? '启用' : '禁用' }}</label>
                    </el-form-item>
                </el-form>
                <el-footer class="footer-button">
                    <el-button type="primary" size="mini" @click="toAdd('newGroup')">创 建</el-button>
                    <el-button @click="operate.add = false" size="mini">取 消</el-button>
                </el-footer>
            </div>
        </el-drawer>
        <el-drawer :visible.sync="operate.edit" size="50%" class="drawer" @close="editClose()">
            <div slot="title" class="title">管理员信息<el-divider></el-divider></div>
            <div class="content" v-loading="addLoading" element-loading-text="正在保存"
                element-loading-background="rgba(255, 255, 255, 0.8)">
                <el-form :model="editGroup" :rules="groupRules" ref="editGroup" label-position="left" label-width="100px">
                    <el-form-item label="权限组名" prop="ggroup_name">
                        <el-input :disabled="!editGroup.show" v-model="editGroup.group_name" autocomplete="off"
                            placeholder="权限组名"></el-input>
                    </el-form-item>
                    <el-form-item label="权限" prop="group_id">
                        <el-tree v-show="editGroup.show" ref="editGroupTree" :data="menuTree" show-checkbox node-key="id"
                            label="label" :default-checked-keys="editGroup.defaultChecked">
                        </el-tree>
                        <el-tree v-show="!editGroup.show" :data="editGroup.menuTree" node-key="id" label="label">
                        </el-tree>
                    </el-form-item>
                    <el-form-item label="状态" prop="status">
                        <el-switch v-show="editGroup.show" v-model="editGroup.status" active-color="#1989FA"
                            inactive-color="#F56C6C">
                        </el-switch>
                        <label v-show="editGroup.show" style="padding-left: 30px;color: #909399;">{{ editGroup.status ? '启用'
                            : '禁用' }}</label>
                        <el-tag v-show="!editGroup.show" :type="editGroup.status ? 'success' : 'danger'">{{ editGroup.status
                            ? '启用' : '禁用' }}</el-tag>
                    </el-form-item>
                </el-form>
                <el-footer v-show="editGroup.show" class="footer-button">
                    <el-button @click="handleBack(editRow)" size="mini">返 回</el-button>
                    <el-button type="primary" size="mini" @click="toSave('editGroup')">保 存</el-button>
                </el-footer>
                <el-footer v-show="!editGroup.show" class="footer-button">
                    <el-button type="primary" size="mini" @click="editGroup.show = true">编 辑</el-button>
                </el-footer>
            </div>
        </el-drawer>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { get_menu_tree, handleTreeKey } from '@/utils/loadRoute.js';
import { getGroup, getLevelAllMenu, getMenu, getMenuIdArray, selectButton, getAllGroup, addGroup, addAuthority, getAuthorityByGroupId, deleteGroup, saveGroup } from '@/api';
export default {
    data() {
        var checkGroupName = (rule, value, callback) => {
            if (!value) {
                return callback(new Error('权限组名不能为空'));
            } else if (!(/^[\u4e00-\u9fa5_a-zA-Z0-9]{1,10}$/.test(value))) {
                callback(new Error("权限组名由汉字,字母,数字或下划线组成且不超过10位"));
            } else {
                callback();
            }
        };
        return {
            checkList: [],
            menuTree: [],
            menuIdArray: [],
            menus: [],
            groupMenus: [],
            groupAuthority: [],
            dataLoading: true,
            addLoading: false,
            groups: [],
            search_key: '',
            select_key: 'group_id',
            tableData: [],
            multipleSelection: [],
            editRow: [],
            page: {
                pageData: [],
                currentPage: 1,
                pageSize: 6,
                pageTotal: 0
            },
            authority: {
                delete: false,
                edit: false,
                add: false
            },
            authority_group: [],
            operate: {
                add: false,
                edit: false
            },
            newGroup: {
                group_name: '',
                status: true
            },
            editGroup: {
                menuTree: [],
                group_id: 0,
                defaultChecked: [],
                group_name: '',
                status: true,
                show: false
            },
            groupRules: {
                group_name: [
                    { validator: checkGroupName, trigger: 'blur' }
                ]
            }
        }
    },
    computed: {
        ...mapGetters([
            'info',
            'menuId'
        ])
    },
    watch: {
        search_key: {
            handler(val, old) {
                if (val != '') {
                    this.tableData = this.groups;
                }
                else {
                    this.tableData = this.page.pageData;
                }
            },
            immediate: false,
            deep: true
        }
    },
    mounted() {
        this.getGroups();
        this.getButton();
        this.getMenus();
        getAllGroup().then(res => {
            if (res.code == 200 && res.data) {
                this.authority_group = res.data;
            }
            else {
                this.authority_group = [];
                this.$message({
                    message: '权限组信息获取失败!',
                    type: 'error'
                });
            }
        });
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
        getGroups() {
            this.dataLoading = true;
            getGroup().then(res => {
                if (res.code == 200) {
                    this.groups = res.data;
                    this.page.pageTotal = res.data.length;
                    this.page.pageData = this.queryPage();
                }
                else {
                    this.$message({
                        message: '数据获取失败！',
                        type: 'warning'
                    });
                }
                this.dataLoading = false;
            });
        },
        getMenus() {
            let params = new URLSearchParams();
            params.append("group_id", this.info.group_id);
            getLevelAllMenu(params).then(res => {
                if (res.code == 200) {
                    this.menus = res.data;
                    this.getMenuIdArrayData();
                }
                else {
                    this.$message({
                        message: res.msg,
                        type: 'error'
                    });
                }
            });
        },
        getMenuIdArrayData() {
            let params = new URLSearchParams();
            params.append("group_id", this.info.group_id);
            getMenuIdArray(params).then(res => {
                this.getGroupAuthority();
                if (res.code == 200) {
                    this.menuIdArray = res.data;
                }
                else {
                    this.$message({
                        message: res.msg,
                        type: 'error'
                    });
                }
            })
        },
        getGroupAuthority() {
            let params = new URLSearchParams();
            params.append("group_id", this.info.group_id);
            getAuthorityByGroupId(params).then(res => {
                if (res.code == 200) {
                    this.groupAuthority = res.data;
                    this.menuTree = get_menu_tree(this.menus, this.groupAuthority, this.menuIdArray);
                }
            })
        },
        handleSelectionChange(val) {
            this.multipleSelection = val;
        },
        sizeChange(val) {
            this.page.pageSize = val;
            this.page.pageData = this.queryPage();
        },
        currentChange(val) {
            this.page.currentPage = val;
            this.page.pageData = this.queryPage();
        },
        queryPage() {
            const start = (this.page.currentPage - 1) * this.page.pageSize;
            const end = this.page.currentPage * this.page.pageSize;
            this.tableData = this.groups.slice(start, end);
            return this.groups.slice(start, end);
        },
        toAdd(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.addLoading = true;
                    let checkedKeys = this.$refs.menuTree.getCheckedKeys();
                    let halfCheckedKeys = this.$refs.menuTree.getHalfCheckedKeys();
                    let finalKeys = handleTreeKey(checkedKeys, halfCheckedKeys);
                    let params = new URLSearchParams();
                    params.append("group_name", this.newGroup.group_name);
                    params.append("create_people", this.info.admin_id);
                    if (this.newGroup.status) {
                        params.append("status", 1);
                    }
                    else {
                        params.append("status", 0);
                    }
                    addGroup(params).then(res => {
                        if (res.code == 200) {
                            let list = new URLSearchParams();
                            list.append("group_id", res.data.group_id);
                            list.append("list", finalKeys);
                            addAuthority(list).then(res => {
                                if (res.code == 200) {
                                    this.$message({
                                        message: res.msg,
                                        type: 'success'
                                    });
                                    this.treeReset('menuTree', this.menuTree);
                                    this.newGroup.group_name = '';
                                    this.newGroup.status = true;
                                }
                                else {
                                    this.$message.error(res.msg);
                                }
                                this.getGroups();
                                this.addLoading = false;
                            });
                        }
                        else {
                            this.$message.error(res.msg);
                            this.addLoading = false;
                        }
                    });
                } else {
                    return false;
                }
            });
        },
        treeReset(treeName, treeArray) {
            treeArray.forEach(ele => {
                this.$nextTick(() => {
                    this.$refs[treeName].setChecked(ele.id, false, true);
                })
            })
        },
        toSave(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.addLoading = true;
                    let checkedKeys = this.$refs.editGroupTree.getCheckedKeys();
                    let halfCheckedKeys = this.$refs.editGroupTree.getHalfCheckedKeys();
                    let finalKeys = handleTreeKey(checkedKeys, halfCheckedKeys);
                    let params = new URLSearchParams();
                    params.append("group_id", this.editGroup.group_id);
                    params.append("group_name", this.editGroup.group_name);
                    params.append("create_people", this.info.admin_id);
                    if (this.editGroup.status) {
                        params.append("status", 1);
                    }
                    else {
                        params.append("status", 0);
                    }
                    saveGroup(params).then(res => {
                        if (res.code == 200) {
                            let list = new URLSearchParams();
                            list.append("group_id", this.editGroup.group_id);
                            list.append("list", finalKeys);
                            addAuthority(list).then(res => {
                                if (res.code == 200) {
                                    this.$message({
                                        message: res.msg,
                                        type: 'success'
                                    });
                                    this.getRowMenuTree(this.editGroup.group_id);
                                    this.editGroup.show = false;
                                }
                                else {
                                    this.$message.error(res.msg);
                                }
                                this.getGroups();
                                this.addLoading = false;
                            });
                        }
                        else {
                            this.$message.error(res.msg);
                            this.addLoading = false;
                        }
                    });
                } else {
                    return false;
                }
            });
        },
        handleBack(row) {
            this.editGroup.show = false;
            this.editGroup.group_name = row.group_name;
            this.editGroup.status = row.status > 0 ? true : false;
        },
        handleEdit(row) {
            this.addLoading = true;
            this.editGroup.show = false;
            this.editGroup.group_id = row.group_id;
            this.editGroup.group_name = row.group_name;
            this.editGroup.status = row.status > 0 ? true : false;
            this.editRow = row;
            this.operate.edit = true;
            this.getRowMenuTree(row.group_id);
        },
        getRowMenuTree(id) {
            let params = new URLSearchParams();
            params.append("group_id", id);
            getAuthorityByGroupId(params).then(res => {
                if (res.code == 200) {
                    let handleData = res.data || [];
                    this.editGroup.defaultChecked = this.handleDefultChecked(handleData);
                    this.$refs.editGroupTree.setCheckedKeys(this.editGroup.defaultChecked);
                }
                else {
                    this.$message({
                        message: res.msg,
                        type: 'error'
                    });
                }
            });
            getMenu(params).then(res => {
                if (res.code == 200) {
                    this.groupMenus = res.data;
                    this.editGroup.menuTree = get_menu_tree(this.groupMenus, this.groupAuthority);
                    this.addLoading = false;
                }
                else {
                    this.$message({
                        message: res.msg,
                        type: 'error'
                    });
                }
            });
        },
        handleDelete(row) {
            this.$confirm('确认要删除该用户吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new URLSearchParams();
                params.append("group_id", row.group_id);
                deleteGroup(params).then(res => {
                    if (res.code == 200) {
                        this.getGroups();
                        this.$message({
                            message: res.msg,
                            type: 'success'
                        });
                    }
                    else {
                        this.$message.error(res.msg);
                    }
                }).catch(err => {
                    this.$message.error('删除失败!');
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },
        editClose() {
            this.editGroup.show = false;
        },
        handleDefultChecked(menuIdArr) {
            let arr = [];
            menuIdArr.forEach(item => {
                switch (item.button) {
                    case 7:
                        arr.push(item.menu_id + '.4');
                        arr.push(item.menu_id + '.2');
                        arr.push(item.menu_id + '.1');
                        break;
                    case 6:
                        arr.push(item.menu_id + '.4');
                        arr.push(item.menu_id + '.2');
                        break;
                    case 5:
                        arr.push(item.menu_id + '.4');
                        arr.push(item.menu_id + '.1');
                        break;
                    case 4:
                        arr.push(item.menu_id + '.4');
                        break;
                    case 3:
                        arr.push(item.menu_id + '.2');
                        arr.push(item.menu_id + '.1');
                        break;
                    case 2:
                        arr.push(item.menu_id + '.2');
                        break;
                    case 2:
                        arr.push(item.menu_id + '.1');
                        break;
                    default:
                        break;
                }
            });
            return arr;
        }
    }
}
</script>
<style lang="scss" scoped>
@mixin row {
    display: flex;
    flex-direction: row;
}

@mixin column {
    display: flex;
    flex-direction: column;
}

.head {
    @include row();

    &>div {
        margin-bottom: 15px;
        margin-right: 10px;
    }
}

.drawer {
    .title {
        width: 100%;
        min-width: 450px;
        padding-left: 20px;
        font-size: 16px;
        font-weight: bold;
    }

    .content {
        width: 100%;
        height: 100%;
        min-width: 450px;
        padding-right: 30px;
        padding-left: 45px;
        padding-top: 0px;
        position: relative;

        .footer-button {
            width: 100%;
            border-top: 1px solid #DCDFE6;
            padding-top: 20px;
            background-color: white;

            .el-button {
                margin-right: 20px;
            }
        }
    }
}
</style>