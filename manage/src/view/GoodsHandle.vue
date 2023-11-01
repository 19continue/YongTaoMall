<template>
    <div>
        <div v-if="!addShow" style="padding: 30px;">
            <div class="head" style="">
                <div class="search" style="width: 200px;min-width: 150px;">
                    <el-input v-model="search_key" class="input-with-select" size="mini" placeholder="输入关键字搜索">
                        <el-select v-model="select_key" slot="prepend" :placeholder="select_key">
                            <el-option label="ID" value="gid"></el-option>
                            <el-option label="商品名称" value="goods_name"></el-option>
                        </el-select>
                    </el-input>
                </div>
                <div class="add">
                    <el-button size="mini" type="primary" @click="addShow = true" icon="el-icon-circle-plus"
                        :disabled="!authority.add">添加</el-button>
                </div>
                <div class="add">
                    <el-button size="mini" type="danger" @click="multiDelete" icon="el-icon-remove"
                        :disabled="!authority.delete">删除</el-button>
                </div>
            </div>
            <el-table
                :data="tableData.filter(data => !search_key || `${data[select_key]}`.toLowerCase().includes(search_key.toLowerCase()))"
                style="width: 100%;min-width: 400px;" empty-text v-loading="dataLoading"
                @selection-change="handleSelectionChange" border>
                <af-table-column type="selection" align="center"></af-table-column>
                <af-table-column sortable label="ID" prop="gid" align="center"></af-table-column>
                <af-table-column label="商品图" align="center">
                    <template slot-scope="scope">
                        <el-image :preview-src-list="[scope.row.src]" shape="square" style="width: 40px; height: 40px" fit="cover" :src="scope.row.src"></el-image>
                    </template>
                </af-table-column>
                <af-table-column label="商品名称" prop="goods_name" align="center"></af-table-column>
                <af-table-column label="商品类型" align="center">
                    <template slot-scope="scope">
                        <el-tag type="success">
                            {{ scope.row.type==0?'普通商品':'虚拟商品'}}
                        </el-tag>
                    </template>
                </af-table-column>
                <af-table-column label="售价" align="center">
                    <template slot-scope="scope">
                        {{ '原价: ' + scope.row.o_price }}<br>{{ '现价: ' + scope.row.s_price }}
                    </template>
                </af-table-column>
                <af-table-column label="库存" prop="total" align="center"></af-table-column>
                <af-table-column label="排序" prop="sort" align="center"></af-table-column>
                <af-table-column label="状态" align="center">
                    <template slot-scope="scope">
                        <el-switch :disabled="!authority.edit" :value="scope.row.status > 0 ? true : false"
                            @change="changeStatus(scope.row)">
                        </el-switch>
                        <label style="padding-left: 5px;color: #909399;">{{ scope.row.status > 0 ? '上架'
                            : '下架' }}</label>
                    </template>
                </af-table-column>
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
        </div>
        <div v-else style="padding-top: 10px;padding: 10px;">
            <el-page-header @back="addShow = false" content="商品添加">
            </el-page-header>
            <div style="padding: 10px;padding-left: 20px;padding-right: 20px;min-width: 400px;"
                v-loading="newGoods.loading">
                <el-tabs v-model="step">
                    <el-tab-pane label="基础信息" name="1">
                        <el-form :model="newGoods" :rules="goodsRules" ref="newGoods" label-position="left"
                            label-width="100px">
                            <el-form-item label="商品名称" prop="goods_name">
                                <el-input style="width: 50%;" v-model="newGoods.goods_name" autocomplete="off"
                                    placeholder="请输入商品名称,不超过60个字" maxlength="60"></el-input>
                            </el-form-item>
                            <el-form-item label="原价" prop="goods_name">
                                <el-input-number size="mini" :min="0" :precision="2" :max="1000000"
                                    v-model="newGoods.o_price"></el-input-number>
                            </el-form-item>
                            <el-form-item label="商品类型" prop="type">
                                <el-radio-group v-model="newGoods.type" size="small">
                                    <el-radio :label="0" border>普通商品</el-radio>
                                    <el-radio :label="1" border>虚拟商品</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="商品分类" prop="class_id">
                                <el-cascader ref="classCascader" @visible-change="getClassOptions" :options="classOptions" :props="{ multiple: true, checkStrictly: true,value:'class_id',label:'name',children:'children'}"
                                    clearable></el-cascader>
                            </el-form-item>
                            <el-form-item label="商品轮播图" prop="file">
                                <el-upload action="#" name="goodsImg" list-type="picture-card" :auto-upload="false"
                                    ref="goodsImg" :limit="6" :on-exceed="outLimit" :http-request="submitGoodsImgUpload">
                                    <i slot="default" class="el-icon-plus"></i>
                                    <div slot="file" slot-scope="{file}">
                                        <img class="el-upload-list__item-thumbnail" :src="file.url" alt="">
                                        <span class="el-upload-list__item-actions">
                                            <span v-if="!disabled" class="el-upload-list__item-delete"
                                                @click="handleMove(file)">
                                                <i class="el-icon-rank"></i>
                                            </span>
                                            <span v-if="!disabled" class="el-upload-list__item-delete"
                                                @click="handleRemove(file)">
                                                <i class="el-icon-delete"></i>
                                            </span>
                                        </span>
                                    </div>
                                </el-upload>
                            </el-form-item>
                            <el-form-item label="排序" prop="sort">
                                <el-input-number size="mini" :min="0" v-model="newGoods.sort"></el-input-number>
                            </el-form-item>
                            <el-form-item label="状态" prop="status">
                                <el-switch v-model="newGoods.status" active-color="#1989FA" inactive-color="#F56C6C">
                                </el-switch>
                                <label style="padding-left: 30px;color: #909399;">{{
                                    newGoods.status ? '上架' : '下架' }}</label>
                            </el-form-item>
                        </el-form>
                        <el-row>
                            <el-button size="mini" type="primary" @click="handleNext(1)">下一步</el-button>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="规格存储" name="2">
                        <div style="min-width: 400px;margin-bottom: 20px;">
                            <label style="font-size: 14px;margin-right: 72px;">规格:</label>
                            <el-radio v-model="sku_mode" label="1">单规格</el-radio>
                            <el-radio v-model="sku_mode" label="2">多规格</el-radio>
                        </div>
                        <div v-show="sku_mode == '1'">
                            <el-form :model="sku_single" label-position="left" label-width="100px">
                                <el-form-item label="价格(元)" prop="price">
                                    <el-input-number size="mini" :min="0" :precision="2" :max="1000000"
                                        v-model="sku_single.price"></el-input-number>
                                </el-form-item>
                                <el-form-item label="库存" prop="amount">
                                    <el-input-number size="mini" :min="0" v-model="sku_single.amount"></el-input-number>
                                </el-form-item>
                                <el-form-item label="状态" prop="status">
                                    <el-switch v-model="sku_single.status" :active-value="1" :inactive-value="0">
                                    </el-switch>
                                    <label style="padding-left: 5px;color: #909399;">{{ sku_single.status >
                                        0 ? '上架'
                                        : '下架' }}</label>
                                </el-form-item>
                            </el-form>
                        </div>
                        <div v-show="sku_mode == '2'">
                            <div v-for="(item, index) in sku.newSku" :key="index">
                                <el-row class="tag-row">
                                    <div class="tag-group">
                                        <span class="tag-group__title">规格名称:</span>
                                        <el-tag type="warning" effect="dark" closable @close="handleAttrRemove(index)">{{
                                            item.key }}</el-tag>
                                    </div>
                                    <div class="tag-group">
                                        <span class="tag-group__title" style="margin-right: 26px;">规格值:</span>
                                        <el-tag :key="id" v-for="(tag, id) in item.value" closable
                                            :disable-transitions="false" @close="handletagClose(id, item)">
                                            {{ tag }}
                                        </el-tag>
                                        <el-input class="input-new-tag" v-if="item.showInput" v-model="item.newValue"
                                            :ref="`saveTagInput${index}`" size="small"
                                            @keyup.enter.native="addAttrValue(item)" @blur="addAttrValue(item)">
                                        </el-input>
                                        <el-button v-else class="button-new-tag" size="small"
                                            @click="showInput(`saveTagInput${index}`, item)">+ 规格值</el-button>
                                    </div>
                                    <el-divider></el-divider>
                                </el-row>
                            </div>
                            <el-row v-show="!sku.addShow">
                                <el-button size="mini" type="primary" @click="sku.addShow = true">添加规格</el-button>
                                <el-button size="mini" type="success" @click="createSkuTable">立即生成</el-button>
                            </el-row>
                            <el-form v-show="sku.addShow" :inline="true">
                                <el-form-item label="规格">
                                    <el-input size="mini" v-model="sku.sku_attr" autocomplete="off"
                                        placeholder="规格名称不超过20个字" maxlength="20" @keyup.enter.native="addSku"></el-input>
                                </el-form-item>
                                <el-form-item label="规格值">
                                    <el-input size="mini" v-model="sku.sku_value" autocomplete="off"
                                        placeholder="规格值不超过30个字" maxlength="30" @keyup.enter.native="addSku"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button size="mini" type="primary" @click="addSku">确定</el-button>
                                    <el-button size="mini" @click="sku.addShow = false">返回</el-button>
                                </el-form-item>
                            </el-form>
                            <el-upload v-show="false" action="#" list-type="picture-card" :auto-upload="false" ref="skuImg"
                                :on-change="skuImgChange">
                            </el-upload>
                            <el-table v-if="sku.showTable" :data="this.sku.tableColumn"
                                style="width: 100%;min-width: 400px;margin-top: 20px;" border>
                                <af-table-column v-for="(attr, index) in sku.newSku" :key="index" :label="attr.key"
                                    align="center">
                                    <template slot-scope="scope">
                                        {{ sku.newSku[index].value[scope.row[index]] }}
                                    </template>
                                </af-table-column>
                                <af-table-column label="图片" align="center">
                                    <template slot-scope="scope">
                                        <el-avatar v-if="sku.tableData[scope.$index].picture" shape="square" :size="40"
                                            fit="cover" @click.native="handleSkuImgSelect(scope.$index)"
                                            :src="sku.tableData[scope.$index].picture.url"></el-avatar>
                                        <el-avatar v-else shape="square" :size="40" fit="cover"
                                            @click.native="handleSkuImgSelect(scope.$index)" src=""></el-avatar>
                                    </template>
                                </af-table-column>
                                <af-table-column label="价格(元)" align="center">
                                    <template slot-scope="scope">
                                        <el-input-number size="mini" :min="0" :precision="2" :max="1000000"
                                            v-model="sku.tableData[scope.$index].price"></el-input-number>
                                    </template>
                                </af-table-column>
                                <af-table-column label="库存" align="center">
                                    <template slot-scope="scope">
                                        <el-input-number size="mini" :min="0"
                                            v-model="sku.tableData[scope.$index].amount"></el-input-number>
                                    </template>
                                </af-table-column>
                                <af-table-column label="状态" align="center">
                                    <template slot-scope="scope">
                                        <el-switch :value="sku.tableData[scope.$index].status > 0 ? true : false"
                                            @change="changeSkuStatus($event, scope.$index)">
                                        </el-switch>
                                        <label style="padding-left: 5px;color: #909399;">{{
                                            sku.tableData[scope.$index].status >
                                            0 ? '上架'
                                            : '下架' }}</label>
                                    </template>
                                </af-table-column>
                                <af-table-column fixed="right" label="操作" width="60" align="center">
                                    <template slot-scope="scope">
                                        <el-tooltip content="删除" placement="top">
                                            <el-button @click="removeSkuTableData(scope.$index)" type="danger"
                                                icon="el-icon-delete" size="small"
                                                style="padding: 5px;font-size: 15px;"></el-button>
                                        </el-tooltip>
                                    </template>
                                </af-table-column>
                            </el-table>
                        </div>
                        <el-row style="margin-top: 20px;">
                            <el-button size="mini" @click="handleNext(-1)">上一步</el-button>
                            <el-button size="mini" type="primary" @click="handleNext(1)">下一步</el-button>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="物流设置" name="3">
                        <div>
                            <el-form :inline="true">
                                <el-form-item label="运费模板">
                                    <el-select v-model="newGoods.tid" placeholder="请选择">
                                        <el-option label="全国包邮" :value="1">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-form>
                        </div>
                        <el-row>
                            <el-button size="mini" @click="handleNext(-1)">上一步</el-button>
                            <el-button size="mini" type="primary" @click="handleNext(1)">下一步</el-button>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="商品详情" name="4">
                        <quillEditor style="width: 80%;margin: 0 auto;margin-top: 30px;" ref="myQuillEditor"
                            v-model="newGoods.content" :options="editorOption" @blur="onEditorBlur($event)">
                        </quillEditor>
                        <!-- @focus="onEditorFocus($event)"
                            @ready="onEditorReady($event)"
                            @change="onEditorChange($event)"> -->
                        <el-row style="width: 80%;margin: 0 auto;margin-top: 20px;">
                            <el-button size="mini" @click="handleNext(-1)">上一步</el-button>
                            <el-button size="mini" type="primary" @click="toAddGoods()">完成</el-button>
                        </el-row>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { descartes, resDescartes } from '@/utils/goodsUtils';
import axios from "axios";
import { baseURL, getAllGoods, selectButton, insertGoods, uploadSku } from '@/api';
import { post, upload } from '@/api/MyAxios'
import { quillEditor } from 'vue-quill-editor';
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import { f } from 'af-table-column';
export default {
    components: {
        quillEditor
    },
    data() {
        return {
            dataLoading: false,
            addShow: false,
            goods: [],
            search_key: '',
            select_key: 'admin_id',
            tableData: [],
            step: '1',
            classOptions: [],
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
            newGoods: {
                loading: false,
                gid: null,
                goods_name: '',
                type: 0,
                o_price: 0,
                s_price: 0,
                total: 0,
                sort: 0,
                status: true,
                addVedio: false,
                class_id: [],
                tid:1,
                file: [],
                content: '',
                upload_status: true
            },
            sku: {
                addShow: false,
                sku_attr: '',
                sku_value: '',
                newSku: [],
                showTable: false,
                tableLoading: false,
                tableColumn: [],
                tableData: [],
                select_index: 0,
                upload_status: false
            },
            sku_mode: '1',
            sku_single: {
                price: 0,
                amount: 0,
                status: 1
            },
            dialogImageUrl: '',
            dialogVisible: false,
            disabled: false,
            goodsRules: {
                goods_name: [
                    { validator: null, trigger: 'blur' }
                ],
                type: [
                    { validator: null, trigger: 'blur' }
                ]
            },
            editorOption: {
                placeholder: '在此编辑商品详情',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ header: [1, 2, 3, 4] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ script: 'sub' }, { script: 'super' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        [{ direction: 'rtl' }],
                        [{ size: ['small', false, 'large', 'huge'] }],
                        [{ color: [] }, { background: [] }],
                        [{ font: [] }],
                        [{ align: [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
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
                    this.tableData = this.goods;
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
        this.getButton();
        this.getGoods();
    },
    methods: {
        onEditorBlur($event) {
            console.log(this.newGoods.content);
            console.log($event);
        },
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
        getClassOptions(open){
            if(open){
                let params=new FormData()
                params.append('status', 1)
                post('Categorize/get',params).then(res=>{
                    if(res.code==200){
                        this.classOptions=res.data.records
                    }
                })
            }
        },
        getGoods() {
            this.dataLoading = true;
            getAllGoods().then(res => {
                if (res.code == 200) {
                    this.goods = res.data;
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
        changeStatus(row) {
            row.status = 1 - row.status;
            let params=new FormData()
            params.append('id',row.gid)
            params.append('status', row.status)
            post('Goods/change_status',params).then(res=>{
                if(res.code==200){
                    this.$message.success(res.msg)
                }
                else{
                    this.$message.error(res.msg)
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
            this.tableData = this.goods.slice(start, end);
            return this.goods.slice(start, end);
        },
        addSku() {
            if (this.sku.sku_attr == '') {
                this.$message('请填入规格名称!');
                return;
            }
            else if (this.sku.sku_value == '') {
                this.$message('请填入规格值!');
                return;
            }
            let newAttr = {
                key: this.sku.sku_attr,
                value: [
                    this.sku.sku_value
                ],
                newValue: '',
                showInput: false
            }
            this.sku.newSku.push(newAttr);
            this.sku.sku_attr = '';
            this.sku.sku_value = '';
        },
        showInput(name, item) {
            item.showInput = true;
            this.$nextTick(_ => {
                this.$refs[name][0].focus();
            });
        },
        addAttrValue(item) {
            if (item.newValue) {
                item.value.push(item.newValue);
            }
            item.showInput = false;
            item.newValue = '';
        },
        handleAttrRemove(index) {
            if (this.sku.showTable) {
                this.sku.showTable = false;
                this.sku.tableColumn = [];
                this.sku.tableData = [];
            }
            this.sku.newSku.splice(index, 1);
        },
        handletagClose(id, item) {
            if (this.sku.showTable) {
                this.sku.showTable = false;
                this.sku.tableColumn = [];
                this.sku.tableData = [];
            }
            item.value.splice(id, 1);
        },
        createSkuTable() {
            this.sku.tableLoading = true;
            this.sku.showTable = true;
            const len = this.sku.newSku.length;
            if (len > 1) {
                let res = descartes(this.sku.newSku[0].value, this.sku.newSku[1].value);
                if (res === false) {
                    this.sku.showTable = false;
                    this.sku.tableLoading = false;
                    return;
                }
                for (let i = 2; i < len; i++) {
                    if (res === false) {
                        this.sku.showTable = false;
                        this.sku.tableLoading = false;
                        return;
                    }
                    res = resDescartes(res, this.sku.newSku[i].value, i + 1 === len)
                }
                this.sku.tableColumn = res;
                this.sku.tableData = []
                for (let j = 0; j < res.length; j++) {
                    this.sku.tableData.push({
                        picture: null,
                        price: 0.00,
                        amount: 0,
                        status: 1
                    })
                }
            } else if (len === 1) {
                const l = this.sku.newSku[0].value.length;
                if (l < 1) {
                    this.sku.showTable = false;
                    return;
                }
                for (let idx in this.sku.newSku[0].value) {
                    this.sku.tableColumn.push(idx)
                }
                this.sku.newSku[0].value.forEach(val => {
                    this.sku.tableColumn.push(val);
                })
                for (let j = 0; j < l; j++) {
                    this.sku.tableData.push({
                        picture: null,
                        price: 0.00,
                        amount: 0,
                        status: 1
                    })
                }
            }
            else {
                this.sku.showTable = false;
            }
            this.sku.tableLoading = false;
            console.log(this.sku)
        },
        changeSkuStatus($event, index) {
            if ($event) {
                this.sku.tableData[index].status = 1;
            }
            else {
                this.sku.tableData[index].status = 0;
            }
        },
        removeSkuTableData(index) {
            this.sku.tableColumn.splice(index, 1);
            this.sku.tableData.splice(index, 1);
        },
        skuImgChange(file, fileList) {
            this.sku.tableData[this.sku.select_index].picture = file;
            console.log(this.sku)
            this.$refs.skuImg.uploadFiles = [];
        },
        handleSkuImgSelect(index) {
            this.sku.select_index = index;
            this.$refs.skuImg.$refs['upload-inner'].handleClick();
        },
        handleEdit(row) {

        },
        multiDelete() {
            let _this = this
            this.$confirm('确认要删除该商品吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let arr = [];
                for (let i = 0; i < this.multipleSelection.length; i++) {
                    arr.push(this.multipleSelection[i].gid)
                }
                _this.delete(arr)
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },
        handleDelete(row) {
            let _this = this
            this.$confirm('确认要删除该商品吗?', '提示', {
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            }).then(() => {
                let id = [];
                id.push(row.gid);
                _this.delete(id)
            }).catch((e) => {
                console.log(e)
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },
        delete(idArray) {
            if (idArray && idArray.length < 1) {
                return
            }
            let params = new FormData()
            params.append('id', JSON.stringify(idArray))
            post('Goods/delete', params).then(res => {
                if (res.code == 200) {
                    this.$message({
                        message: res.msg,
                        type: 'success'
                    });
                    this.getGoods()
                } else {
                    this.$message({
                        message: res.msg,
                        type: 'error'
                    });
                }
            })
        },
        handleNext(val) {
            if (val > 0 && this.step < '4') {
                this.step = `${parseInt(this.step) + 1}`;
            }
            if (val < 0 && this.step > '1') {
                this.step = `${parseInt(this.step) - 1}`;
            }
        },
        handleRemove(file) {
            let uploadList = [];
            this.$refs.goodsImg.uploadFiles.forEach(imgFile => {
                if (file.uid !== imgFile.uid) {
                    uploadList.push(imgFile);
                }
            });
            this.$refs.goodsImg.uploadFiles = uploadList;
        },
        handleMove(file) {
            console.log('2', this.$refs.goodsImg);
            console.log('1', file);
        },
        outLimit(file, fileList) {
            this.$message('最多上传6张图片');
        },
        toAddGoods() {
            if (this.$refs.goodsImg.uploadFiles.length <= 0) {
                this.$message({
                    message: '请添加商品轮播图!',
                    type: 'error'
                });
                return
            }
            if (this.sku_mode == '2' && this.sku.tableData.length < 1) {
                this.$message({
                    message: '请添加商品规格!',
                    type: 'error'
                });
                return
            }
            this.newGoods.class_id=[]
            let presentTags= this.$refs.classCascader.presentTags
            for(let i=0;i<presentTags.length;i++){
                this.newGoods.class_id.push(presentTags[i].node.value)
                this.getParentValue(presentTags[i].node, this.newGoods.class_id)
            }
            this.newGoods.class_id=[...new Set(this.newGoods.class_id)]
            if (this.newGoods.upload_status) {
                this.newGoods.loading = true;
                let params = new URLSearchParams();
                params.append("goods_name", this.newGoods.goods_name);
                params.append("type", this.newGoods.type);
                params.append("o_price", this.newGoods.o_price);
                params.append("sort", this.newGoods.sort);
                params.append("status", this.newGoods.status ? 1 : 0);
                params.append("class_id", JSON.stringify(this.newGoods.class_id));
                params.append("tid", this.newGoods.tid);
                params.append("detail", this.newGoods.content);
                insertGoods(params).then(res => {
                    if (res.code == 200) {
                        this.newGoods.gid = res.data.gid;
                        this.$refs.goodsImg.submit();
                        let sku_attr = new FormData()
                        sku_attr.append('gid', this.newGoods.gid)
                        if (this.sku_mode == '1') {
                            sku_attr.append('sku', JSON.stringify(this.sku_single))
                            sku_attr.append('multi', false)
                            uploadSku(sku_attr).then(res => {
                                if (res.code == 200) {
                                    this.skuClear()
                                }
                            })
                        } else {
                            let sku = []
                            let attr = []
                            for (let idx in this.sku.tableData) {
                                sku.push({
                                    price: this.sku.tableData[idx].price,
                                    amount: this.sku.tableData[idx].amount,
                                    status: this.sku.tableData[idx].status,
                                    attr_path: this.sku.tableColumn[idx]
                                })
                            }
                            for (let idx in this.sku.newSku) {
                                attr.push({
                                    key: this.sku.newSku[idx].key,
                                    value: this.sku.newSku[idx].value
                                })
                            }
                            sku_attr.append('sku', JSON.stringify(sku))
                            sku_attr.append('attr', JSON.stringify(attr))
                            sku_attr.append('multi', true)
                            uploadSku(sku_attr).then(res => {
                                if (res.code == 200) {
                                    let fileData = new FormData()
                                    let sku_id = []
                                    for (let i = 0; i < res.data.length; i++) {
                                        if (this.sku.tableData[i].picture) {
                                            fileData.append('files[]', this.sku.tableData[i].picture.raw)
                                            sku_id.push(res.data[i].sku_id)
                                        }
                                    }
                                    if (sku_id.length == 0) {
                                        return
                                    }
                                    fileData.append('sku_id', JSON.stringify(sku_id))
                                    upload('goods/uploadSkuImg', fileData).then(res => {
                                        if (res.code == 200) {
                                            this.skuClear()
                                        }
                                    })
                                }
                            })
                        }
                    }
                    else {
                        this.$message({
                            message: '商品添加失败!',
                            type: 'error'
                        });
                    }
                })
            }
        },
        dealClassId(node){
            let arr=[]
            arr.push(node.value)
            this.getParentValue(node,arr)
            return arr
        },
        getParentValue(node,arr){
            if(node.parent){
                arr.push(node.parent.value)
                this.getParentValue(node.parent,arr)
            }
        },
        submitGoodsImgUpload() {
            if (this.newGoods.upload_status) {
                let url = `${baseURL}/goods/uploadImg?gid=${this.newGoods.gid}`;
                this.multiFileUpload(this.$refs.goodsImg.uploadFiles, url);
                this.newGoods.upload_status = false;
            }
        },
        multiFileUpload(fileArray, url, type = 'goods') {
            if (fileArray.constructor === Array && fileArray.length > 0) {
                let params = new FormData()
                fileArray.forEach(file => {
                    params.append('files[]', file.raw);
                })
                const config = {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    }
                }
                axios.post(url, params, config).then(res => {
                    if (res.data.code == 200) {
                        if (type === 'goods') {
                            this.addSuccess();
                        }
                        else {

                        }
                    }
                }).catch(err => {

                });
            }
        },
        addSuccess() {
            this.$refs.goodsImg.clearFiles();
            this.step = '1';
            this.newGoodsClear();
            this.newGoods.loading = false;
            this.$message({
                message: '商品添加成功!',
                type: 'success'
            });
        },
        newGoodsClear() {
            this.newGoods.gid = null,
                this.newGoods.goods_name = '',
                this.newGoods.type = 0,
                this.newGoods.o_price = 0,
                this.newGoods.s_price = 0,
                this.newGoods.total = 0,
                this.newGoods.status = true,
                this.newGoods.addVedio = false,
                this.newGoods.class_id = [],
                this.newGoods.tid=1,
                this.newGoods.file = [],
                this.newGoods.content = '',
                this.newGoods.upload_status = true
        },
        skuClear() {
            this.sku = {
                addShow: false,
                sku_attr: '',
                sku_value: '',
                newSku: [],
                showTable: false,
                tableLoading: false,
                tableColumn: [],
                tableData: [],
                select_index: 0,
                upload_status: false
            }
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

.el-tag {
    margin-left: 10px;
}

.button-new-tag {
    margin-left: 10px;
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
}

.input-new-tag {
    width: 90px;
    margin-left: 10px;
    vertical-align: bottom;
}

.tag-row {
    width: 100%;
}

.tag-group {
    margin-bottom: 10px;

    .tag-group__title {
        font-size: 16px;
        color: #909399;
        margin-right: 10px;
    }
}
</style>