<template>
    <el-drawer :visible.sync="showDrawer" size="50%" class="drawer" @close="close">
        <div slot="title" class="title">{{ attribute.title }}<el-divider></el-divider></div>
        <div class="content" v-loading="loading" element-loading-text="请稍等"
            element-loading-background="rgba(255, 255, 255, 0.8)">
            <el-form :model="newData" label-position="left" label-width="100px" :rules="rules" ref="ruleForm"
                hide-required-asterisk>
                <template v-for="(item, index) in labelData">
                    <template v-if="attribute.type == 'add' && !item.addHide || attribute.type == 'edit' && !item.editHide">
                        <el-form-item :key="index" v-if="item.type == 'text'" :label="item.label" :prop="item.prop">
                            <el-input v-model="newData[item.prop]"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable"
                                autocomplete="off"
                                :placeholder="attribute.type == 'edit' ? '' : ('请输入' + item.label)"></el-input>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'textarea'" :label="item.label"
                            :prop="item.prop">
                            <el-input type="textarea" :placeholder="attribute.type == 'edit' ? '' : ('请输入' + item.label)"
                                v-model="newData[item.prop]"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable">
                            </el-input>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'selectTag'" :label="item.label"
                            :prop="item.prop">
                            <el-select v-model="newData[item.prop]" :multiple="item.multiple" size="small"
                                :placeholder="attribute.type == 'edit' ? '' : ('请选择' + item.label)"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable"
                                @visible-change="(e) => { remoteMethod(e, item.prop) }"
                                :loading="item.loading ? item.loading : false">
                                <el-option v-for="(op, index) in item.option" :key="index" :label="op.label"
                                    :value="op.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'select'" :label="item.label" :prop="item.prop">
                            <el-radio-group v-model="newData[item.prop]" size="small"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable">
                                <el-radio v-for="(op, index) in item.option" :label="op.value" :key="index" border>{{
                                    op.label }}</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'switch'" :label="item.label" :prop="item.prop">
                            <el-switch v-model="newData[item.prop]" active-color="#409EFF" inactive-color="#ff4949"
                                :active-value="item.option && item.option.length > 0 ? item.option[0].value : 1"
                                :inactive-value="item.option && item.option.length > 1 ? item.option[1].value : 0"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable">
                            </el-switch>
                            <label style="padding-left: 30px;color: #909399;">{{ getSelect(item.option, newData[item.prop])
                            }}</label>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'number'" :label="item.label" :prop="item.prop">
                            <el-input-number v-model="newData[item.prop]" 
                                :precision="item.precision? item.precision : 0" 
                                :step="item.step ? item.step : 1" 
                                :min="item.min ? item.min:0" 
                                :max="item.max ? item.max : 100" 
                                :step-strictly="item.strictly? item.strictly:false"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable"></el-input-number>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'image'" :label="item.label" :prop="item.prop">
                            <el-upload v-show="property.show" class="avatar-uploader" accept="image/jpeg,image/png"
                                ref="uploadImage" :name="item.prop" action="#" :multiple="false" :auto-upload="false"
                                :show-file-list="false" :drag="false"
                                :on-change="(file, fileList) => { changeFile(file, item.prop) }">
                                <img v-if="newData[item.prop]" :src="newData[item.prop]" class="avatar">
                                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <div v-show="!property.show || attribute.type == 'edit' && item.notEditable">
                                <el-avatar v-if="newData[item.prop]" shape="square" :size="178" :src="newData[item.prop]"
                                    fit="cover"></el-avatar>
                                <el-avatar v-else shape="square" :size="178">
                                    <i class="el-icon-user-solid" style="font-size: 100px;line-height: 175px;"
                                        slot="default"></i>
                                </el-avatar>
                            </div>
                        </el-form-item>
                        <el-form-item :key="index" v-else-if="item.type == 'date'" :label="item.label" :prop="item.prop">
                            <el-date-picker v-model="newData[item.prop]"
                                :disabled="!property.show || attribute.type == 'edit' && item.notEditable" type="date"
                                value-format="yyyy-MM-dd"
                                :placeholder="attribute.type == 'edit' ? '' : ('请选择' + item.label)">
                            </el-date-picker>
                        </el-form-item>
                    </template>
                </template>
            </el-form>
            <el-footer class="footer-button" v-if="attribute.type == 'add'">
                <el-button type="primary" size="mini" @click="toAdd()">确 定</el-button>
            </el-footer>
            <template v-else-if="attribute.type == 'edit'">
                <el-footer v-show="property.show" class="footer-button">
                    <el-button @click="handleEdit()" size="mini">返 回</el-button>
                    <el-button type="primary" size="mini" @click="toSave()">保 存</el-button>
                </el-footer>
                <el-footer v-show="!property.show" class="footer-button">
                    <el-button type="primary" size="mini" @click="property.show = true">编 辑</el-button>
                </el-footer>
            </template>
        </div>
    </el-drawer>
</template>
<script>
import { upload } from '@/api/MyAxios'
export default {
    name: 'Drawer',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        labelData: {
            type: Array,
            default() {
                return []
            }
        },
        formData: {
            type: Object,
            default() {
                return {}
            }
        },
        attribute: {
            type: Object,
            default() {
                return {
                    show: false,
                    type: 'add',
                    title: ''
                }
            }
        },
        url: {
            type: String,
            default: ''
        },
        rules: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    data() {
        var getNewData = () => {
            if (this.attribute.type == 'edit') {
                return this.formData;
            }
            let obj = {};
            for (let i = 0; i < this.labelData.length; i++) {
                if (this.labelData[i].prop && this.labelData[i].default != undefined) {
                    this.$set(obj, this.labelData[i].prop, this.labelData[i].default);
                }
                else if (this.labelData[i].prop) {
                    this.$set(obj, this.labelData[i].prop, '');
                }
            }
            return obj;
        }
        return {
            newData: getNewData(),
            emptyData: getNewData(),
            row: {},
            showDrawer: this.show,
            loading: false,
            property: {
                show: this.attribute.show
            },
            uploadData: {},
            uploadFile: {}
        }
    },
    methods: {
        toShow() {
            this.showDrawer = !this.showDrawer
        },
        getNewData(data) {
            this.newData = JSON.parse(JSON.stringify(data))
            this.row = JSON.parse(JSON.stringify(data))
        },
        changeFile(file, prop) {
            if (file.response) {
                return
            }
            let type = file.name.replace(/.+\./, '');
            const isType = ['jpg', 'png'].indexOf(type.toLowerCase()) === -1;
            const isLt = ((file.size / 1024 / 1024) < 10);
            if (isType) {
                this.$message.error('上传头像图片类型只能为 JPG 或 PNG !');
                return
            }
            if (!isLt) {
                this.$message.error('上传头像图片大小不能超过 10MB!');
                return
            }
            this.newData[prop] = URL.createObjectURL(file.raw);
            this.uploadFile[prop] = file.raw
        },
        toAdd() {
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    this.loading = true;
                    let params = new FormData()
                    for (let key in this.uploadFile) {
                        params.append(key, this.uploadFile[key])
                    }
                    for (let key in this.newData) {
                        if (this.newData[key] !== undefined && this.newData[key] !== '' && this.newData[key] !== null) {
                            params.append(key, this.newData[key])
                        }
                    }
                    upload(this.url, params).then(res => {
                        this.loading = false;
                        if (res.code == 200) {
                            this.$emit('refresh');
                            this.newData = JSON.parse(JSON.stringify(this.emptyData))
                            this.uploadFile = {}
                            this.$message({
                                message: res.msg,
                                type: 'success'
                            });
                        }
                        else {
                            this.$message.error(res.msg);
                        }
                    })
                } else {
                    return false;
                }
            });
        },
        handleEdit() {
            this.newData = JSON.parse(JSON.stringify(this.row))
            this.property.show = !this.property.show
        },
        toSave() {
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    this.loading = true;
                    let params = new FormData()
                    for (let key in this.uploadFile) {
                        params.append(key, this.uploadFile[key])
                    }
                    for (let key in this.newData) {
                        if (this.newData[key] !== undefined && this.newData[key] !== '' && this.newData[key] !== null && this.newData[key] != this.row[key] && this.newData[key].length !== 0) {
                            params.append(key, this.newData[key])
                        }
                    }
                    if (!params.entries().next()) {
                        this.loading = false;
                        this.$message({
                            message: '您未做任何修改',
                            type: 'info'
                        });
                        return
                    }
                    if (this.attribute.primaryKey) {
                        params.append(this.attribute.primaryKey, this.newData[this.attribute.primaryKey])
                    }
                    params.append('data', params)
                    upload(this.url, params).then(res => {
                        this.loading = false;
                        if (res.code == 200) {
                            this.row = JSON.parse(JSON.stringify(this.newData))
                            this.uploadFile = {}
                            this.$emit('refresh');
                            this.$message({
                                message: res.msg,
                                type: 'success'
                            });
                            this.property.show = !this.property.show
                        }
                        else {
                            this.$message.error(res.msg);
                        }
                    })
                } else {
                    return false;
                }
            });
        },
        close() {
            if (this.attribute.type == 'edit') {
                this.property.show = false
            }
        },
        getSelect(option, select) {
            if (!option || option.length == 0) {
                return ''
            }
            for (let i in option) {
                if (option[i].value != undefined && option[i].label != undefined) {
                    if (option[i].value == select) {
                        return option[i].label
                    }
                }
                else {
                    return ''
                }
            }
            return ''
        },
        remoteMethod(e, prop) {
            if (e) {
                this.$emit('getSelectOption' + prop, this.row);
            }
        }
    }
}
</script>
<style lang="scss" scoped>
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
}</style>