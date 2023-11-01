<template>
    <div style="padding: 30px;">
        <div class="head" style="">
            <div class="search" style="width: 200px;min-width: 150px;">
                <el-input v-model="search_key" class="input-with-select" size="mini" placeholder="输入关键字搜索">
                    <el-select v-model="select_key" slot="prepend" :placeholder="select_key">
                        <el-option label="ID" value="admin_id"></el-option>
                        <el-option label="用户名" value="login_name"></el-option>
                        <el-option label="昵称" value="nick_name"></el-option>
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
            <el-table-column type="selection" align="center"></el-table-column>
            <el-table-column sortable label="ID" prop="admin_id" align="center"></el-table-column>
            <el-table-column label="头像" align="center">
                <template slot-scope="scope">
                    <el-avatar shape="square" :size="40" fit="cover" :src="scope.row.avatar"></el-avatar>
                </template>
            </el-table-column>
            <el-table-column label="用户名" prop="login_name" align="center"></el-table-column>
            <el-table-column label="昵称" prop="nick_name" align="center"></el-table-column>
            <el-table-column label="分组" prop="group_name" align="center"></el-table-column>
            <el-table-column label="状态" width="80" align="center">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status > 0 ? 'success' : 'danger'">{{ scope.row.status > 0 ? '启用' : '禁用'
                    }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column sortable label="创建时间" align="center">
                <template slot-scope="scope">
                    <div style="white-space: pre-line;">
                        {{ scope.row.create_time ? scope.row.create_time.replace(/\s/g, "\n") : '数据丢失' }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column sortable label="上次登录" align="center">
                <template slot-scope="scope">
                    <div style="white-space: pre-line;">
                        {{ scope.row.login_time ? scope.row.login_time.replace(/\s/g, "\n") : '未曾登录' }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column fixed="right" label="操作" width="120" align="center">
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
            </el-table-column>
        </el-table>
        <el-pagination style="margin-top: 12px;text-align: right;" background @size-change="sizeChange"
            @current-change="currentChange" :current-page="page.currentPage" :page-sizes="[6, 10, 20, 50]"
            :page-size="page.pageSize" layout="total, sizes, prev, pager, next, jumper" :total="page.pageTotal">
        </el-pagination>
        <el-drawer :visible.sync="operate.add" size="50%" class="drawer">
            <div slot="title" class="title">创建管理员<el-divider></el-divider></div>
            <div class="content" v-loading="addLoading" element-loading-text="正在创建"
                element-loading-background="rgba(255, 255, 255, 0.8)">
                <el-form :model="newAdmin" :rules="adminRules" ref="newAdmin" label-position="left" label-width="100px">
                    <el-form-item label="用户名" prop="login_name">
                        <el-input v-model="newAdmin.login_name" autocomplete="off" placeholder="管理员登录名"></el-input>
                    </el-form-item>
                    <el-form-item label="密码" prop="password">
                        <el-input v-model="newAdmin.password" autocomplete="off"
                            placeholder="设置密码 (为空时,默认初始密码123456)"></el-input>
                    </el-form-item>
                    <el-form-item label="权限组" prop="group_id">
                        <el-select v-model="newAdmin.group_id" placeholder="请选择权限组"
                            @blur.native.capture="checkGroup('newAdmin', 'group_id')">
                            <el-option v-for="item in authority_group" :key="item.group_id" :label="item.group_name"
                                :value="item.group_id" :disabled="item.status > 0 ? false : true">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="昵称" prop="nick_name">
                        <el-input v-model="newAdmin.nick_name" autocomplete="off" placeholder="请输入呢称"></el-input>
                    </el-form-item>
                    <el-form-item label="头像" prop="avatar">
                        <el-upload class="avatar-uploader" accept="image/jpeg,image/png" ref="uploadAvatar" name="avatar"
                            :action="uploadURL" :multiple="false" :auto-upload="false" :show-file-list="false" :drag="false"
                            :on-success="handleAvatarSuccess" :on-change="changeFile" :on-error="avatarFailed"
                            :before-upload="handleBeforeUpload">
                            <img v-if="newAdmin.avatar != null" :src="newAdmin.avatar" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                    </el-form-item>
                    <el-form-item label="状态" prop="status">
                        <el-switch v-model="newAdmin.status" active-color="#1989FA" inactive-color="#F56C6C">
                        </el-switch>
                        <label style="padding-left: 30px;color: #909399;">{{ newAdmin.status ? '启用' : '禁用' }}</label>
                    </el-form-item>
                </el-form>
                <el-footer class="footer-button">
                    <el-button type="primary" size="mini" @click="toAdd('newAdmin')">创 建</el-button>
                    <el-button @click="operate.add = false" size="mini">取 消</el-button>
                </el-footer>
            </div>
        </el-drawer>
        <el-drawer :visible.sync="operate.edit" size="50%" class="drawer" @close="editClose()">
            <div slot="title" class="title">管理员信息<el-divider></el-divider></div>
            <div class="content" v-loading="addLoading" element-loading-text="正在保存"
                element-loading-background="rgba(255, 255, 255, 0.8)">
                <el-form :model="editAdmin" :rules="adminRules" ref="editAdmin" label-position="left" label-width="100px">
                    <el-form-item label="用户名" prop="login_name">
                        <el-input :disabled="!editAdmin.show" v-model="editAdmin.login_name" autocomplete="off"
                            placeholder="管理员登录名"></el-input>
                    </el-form-item>
                    <el-form-item label="密码" prop="password">
                        <el-input :disabled="!editAdmin.show" v-model="editAdmin.password" autocomplete="off"
                            placeholder="修改密码 (为空时,则不修改)"></el-input>
                    </el-form-item>
                    <el-form-item label="权限组" prop="group_id">
                        <el-select :disabled="!editAdmin.show" v-model="editAdmin.group_id" placeholder="请选择权限组"
                            @blur.native.capture="checkGroup('editAdmin', 'group_id')">
                            <el-option v-for="item in authority_group" :key="item.group_id" :label="item.group_name"
                                :value="item.group_id" :disabled="item.status > 0 ? false : true">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="昵称" prop="nick_name">
                        <el-input :disabled="!editAdmin.show" v-model="editAdmin.nick_name" autocomplete="off"
                            placeholder="请输入呢称"></el-input>
                    </el-form-item>
                    <el-form-item label="头像" prop="avatar">
                        <el-upload v-show="editAdmin.show" class="avatar-uploader" accept="image/jpeg,image/png"
                            ref="saveAvatar" name="avatar" :action="uploadURL" :multiple="false" :auto-upload="false"
                            :show-file-list="false" :drag="false" :on-success="handleSaveSuccess" :on-change="changeFile"
                            :on-error="avatarFailed" :before-upload="handleBeforeSave">
                            <img v-if="editAdmin.avatar" :src="editAdmin.avatar" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <div v-show="!editAdmin.show">
                            <el-avatar v-if="editAdmin.avatar" shape="square" :size="178" :src="editAdmin.avatar"
                                fit="cover"></el-avatar>
                            <el-avatar v-else shape="square" :size="178">
                                <i class="el-icon-user-solid" style="font-size: 100px;line-height: 175px;"
                                    slot="default"></i>
                            </el-avatar>
                        </div>
                    </el-form-item>
                    <el-form-item label="状态" prop="status">
                        <el-switch v-show="editAdmin.show" v-model="editAdmin.status" active-color="#1989FA"
                            inactive-color="#F56C6C">
                        </el-switch>
                        <label v-show="editAdmin.show" style="padding-left: 30px;color: #909399;">{{ editAdmin.status ? '启用'
                            : '禁用' }}</label>
                        <el-tag v-show="!editAdmin.show" :type="editAdmin.status ? 'success' : 'danger'">{{ editAdmin.status
                            ? '启用' : '禁用' }}</el-tag>
                    </el-form-item>
                </el-form>
                <el-footer v-show="editAdmin.show" class="footer-button">
                    <el-button @click="handleEdit(editRow)" size="mini">返 回</el-button>
                    <el-button type="primary" size="mini" @click="toSave('editAdmin')">保 存</el-button>
                </el-footer>
                <el-footer v-show="!editAdmin.show" class="footer-button">
                    <el-button type="primary" size="mini" @click="editAdmin.show = true">编 辑</el-button>
                </el-footer>
            </div>
        </el-drawer>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { baseURL, getAllAdmin, selectButton, getAllGroup, addAdmin, deleteAdminById, saveAdminById } from '@/api';
export default {
    data() {
        var checkName = (rule, value, callback) => {
            if (!value) {
                return callback(new Error('用户名不能为空'));
            } else if (!(/^[a-zA-Z0-9_]{5,12}$/.test(value))) {
                callback(new Error("用户名为5到12位由字母,数字或下划线组成"));
            } else {
                callback();
            }
        };
        var validatePass = (rule, value, callback) => {
            if (value === '') {
                callback();
            }
            else if (!(/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_.]+)$)^[\w~!@#$%^&*()_.]{8,16}$/.test(value))) {
                callback(new Error('密码应为字母,数字,特殊符号,两种及以上组合,8-16位'));
            }
            else {
                callback();
            }
        };
        var checkGroup = (rule, value, callback) => {
            if (value === null || value === '') {
                callback(new Error('请选择权限组'));
            }
            else {
                callback();
            }
        }
        var checkNickName = (rule, value, callback) => {
            if (!value) {
                return callback(new Error('昵称不能为空'));
            } else if (!(/^[\u4e00-\u9fa5_a-zA-Z0-9]{1,10}$/.test(value))) {
                callback(new Error("昵称由汉字,字母,数字或下划线组成且不超过10位"));
            } else {
                callback();
            }
        };
        return {
            dataLoading: true,
            addLoading: false,
            baseURL: '',
            uploadURL: '',
            admins: [],
            search_key: '',
            select_key: 'admin_id',
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
            newAdmin: {
                admin_id: null,
                login_name: '',
                password: '',
                nick_name: '',
                avatar: null,
                status: true,
                group_id: null,
                file: null
            },
            editAdmin: {
                show: false,
                admin_id: null,
                login_name: '',
                password: '',
                nick_name: '',
                avatar: null,
                status: true,
                group_id: null
            },
            adminRules: {
                login_name: [
                    { validator: checkName, trigger: 'blur' }
                ],
                password: [
                    { validator: validatePass, trigger: 'blur' }
                ],
                group_id: [
                    { validator: checkGroup, trigger: 'blur' }
                ],
                nick_name: [
                    { validator: checkNickName, trigger: 'blur' }
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
                    this.tableData = this.admins;
                }
                else {
                    this.tableData = this.page.pageData;
                }
            },
            immediate: false,
            deep: true
        }
    },
    created() {
    },
    mounted() {
        this.baseURL = baseURL;
        this.getAdmins();
        this.getButton();
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
        })
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
        getAdmins() {
            this.dataLoading = true;
            getAllAdmin().then(res => {
                if (res.code == 200) {
                    this.admins = res.data;
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
            this.tableData = this.admins.slice(start, end);
            return this.admins.slice(start, end);
        },
        handleBeforeUpload(file) {
            return new Promise((resolve, reject) => {
                this.uploadURL = `${this.baseURL}/admin/uploadAvatar?admin_id=${this.newAdmin.admin_id}`;
                this.$nextTick(() => resolve());
            })
        },
        handleBeforeSave(file) {
            return new Promise((resolve, reject) => {
                this.uploadURL = `${this.baseURL}/admin/uploadAvatar?admin_id=${this.editAdmin.admin_id}`;
                this.$nextTick(() => resolve());
            })
        },
        handleAvatarSuccess(res, file) {
            if (res.code == 200) {
                this.$message({
                    message: '创建成功!',
                    type: 'success'
                });
                this.clear();
            }
            else {
                this.$message.error(res.msg);
            }
            this.addLoading = false;
            this.getAdmins();
        },
        handleSaveSuccess(res, file) {
            if (res.code == 200) {
                this.$message({
                    message: '保存成功!',
                    type: 'success'
                });
                this.newAdmin.file = null;
                this.uploadURL = '';
                this.editAdmin.show = false;
                this.$refs.saveAvatar.clearFiles();
            }
            else {
                this.$message.error(res.msg);
            }
            this.addLoading = false;
            this.getAdmins();
        },
        clear() {
            this.newAdmin.id = null;
            this.newAdmin.login_name = '';
            this.newAdmin.password = '';
            this.newAdmin.nick_name = '';
            this.newAdmin.group_id = null;
            this.newAdmin.avatar = null;
            this.newAdmin.file = null;
            this.uploadURL = '';
        },
        avatarFailed() {
            this.addLoading = false;
            this.$message.error('头像上传失败');
            this.clear();
        },
        changeFile(file) {
            if (file.response) {
                return false;
            }
            let type = file.name.replace(/.+\./, '');
            const isType = ['jpg', 'png'].indexOf(type.toLowerCase()) === -1;
            const isLt = ((file.size / 1024 / 1024) < 10);
            if (isType) {
                this.$message.error('上传头像图片类型只能为 JPG 或 PNG !');
                return false;
            }
            if (!isLt) {
                this.$message.error('上传头像图片大小不能超过 10MB!');
                return false;
            }
            this.newAdmin.avatar = URL.createObjectURL(file.raw);
            this.editAdmin.avatar = URL.createObjectURL(file.raw);
            this.newAdmin.file = file;
            return true;
        },
        checkGroup(formName, type) {
            this.$refs[formName].validateField(type);
        },
        toAdd(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.addLoading = true;
                    let params = new URLSearchParams();
                    params.append("login_name", this.newAdmin.login_name);
                    if (this.newAdmin.password) {
                        params.append("password", this.newAdmin.password);
                    }
                    params.append("nick_name", this.newAdmin.nick_name);
                    if (this.newAdmin.status) {
                        params.append("status", 1);
                    }
                    else {
                        params.append("status", 0);
                    }
                    params.append("group_id", this.newAdmin.group_id);
                    addAdmin(params).then(res => {
                        if (res.code == 200) {
                            this.newAdmin.admin_id = res.data.admin_id;
                            if (this.newAdmin.file) {
                                this.$refs.uploadAvatar.submit();
                            }
                            else {
                                this.$message({
                                    message: res.msg,
                                    type: 'success'
                                });
                                this.addLoading = false;
                                this.getAdmins();
                            }
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
        toSave(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.addLoading = true;
                    let params = new URLSearchParams();
                    params.append("admin_id", this.editAdmin.admin_id);
                    params.append("login_name", this.editAdmin.login_name);
                    if (this.editAdmin.password) {
                        params.append("password", this.editAdmin.password);
                    }
                    params.append("nick_name", this.editAdmin.nick_name);
                    if (this.editAdmin.status) {
                        params.append("status", 1);
                    }
                    else {
                        params.append("status", 0);
                    }
                    params.append("group_id", this.editAdmin.group_id);
                    saveAdminById(params).then(res => {
                        if (res.code == 200) {
                            if (this.newAdmin.file) {
                                this.$refs.saveAvatar.submit();
                            }
                            else {
                                this.$message({
                                    message: res.msg,
                                    type: 'success'
                                });
                                this.addLoading = false;
                                this.editAdmin.show = false;
                                this.getAdmins();
                            }
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
        handleEdit(row) {
            this.editAdmin.show = false;
            this.editAdmin.admin_id = row.admin_id;
            this.editAdmin.login_name = row.login_name;
            this.editAdmin.password = '';
            this.editAdmin.nick_name = row.nick_name;
            this.editAdmin.avatar = row.avatar;
            this.editAdmin.status = row.status > 0 ? true : false;
            this.editAdmin.group_id = row.group_id;
            this.operate.edit = true;
            this.editRow = row;
        },
        handleDelete(row) {
            this.$confirm('确认要删除该用户吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let params = new URLSearchParams();
                params.append("admin_id", row.admin_id);
                deleteAdminById(params).then(res => {
                    if (res.code == 200) {
                        this.getAdmins();
                        this.$message({
                            message: res.msg,
                            type: 'success'
                        });
                    }
                    else {
                        this.$message.error('删除失败!');
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
            this.editAdmin.show = false;
            this.newAdmin.avatar = null;
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

.avatar-uploader {
    border: 1px dashed #d9d9d9;
    width: 178px;
    height: 178px;
    border-radius: 6px;
    padding: 8px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.avatar-uploader:hover {
    border-color: #409EFF;
}

.avatar-uploader-icon {
    font-size: 30px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
}


.avatar {
    width: 178px;
    height: 178px;
    display: block;
}
</style>