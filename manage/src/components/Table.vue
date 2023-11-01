<template>
    <div>
        <el-collapse-transition>
            <div style="width: 100%;min-width: 400px;" v-if="show_muti_search && head.selects">
                <el-form @submit.prevent="" @keyup.enter=";" :inline="true" :model="select_form" size="mini">
                    <template v-for="(item, index) in head.selects">
                        <el-form-item v-if="item.type == 'text'" :key="index" :label="item.label">
                            <el-input v-model="select_form[item.prop]" placeholder="" size="mini"></el-input>
                        </el-form-item>
                        <el-form-item v-if="item.type == 'select'" :key="index" :label="item.label">
                            <el-select v-model="select_form[item.prop]" placeholder="" size="mini">
                                <el-option v-for="(option, idx) in item.option" :key="idx" :label="option.label"
                                    :value="option.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item v-if="item.type == 'date'" :key="index" :label="item.label">
                            <el-date-picker v-model="select_form[item.start]" type="daterange" align="right" unlink-panels
                                range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"
                                :picker-options="pickerOptions">
                            </el-date-picker>
                        </el-form-item>
                    </template>
                    <el-form-item>
                        <el-button @click="handleSearch" type="primary" size="mini">搜索</el-button>
                        <el-button @click="searchReset" size="mini">重置</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </el-collapse-transition>
        <div class="head" style="">
            <div class="search" style="width: 200px;min-width: 150px;">
                <el-input v-model="search_key" class="input-with-select" size="mini" :placeholder="head.placeholder">
                    <el-button slot="prepend" size="mini" @click="showSearch" icon="el-icon-menu">
                    </el-button>
                </el-input>
            </div>
            <div class="add">
                <el-button v-if="head.add" size="mini" type="primary" @click="handleAdd()" icon="el-icon-circle-plus"
                    :disabled="!authority.add">{{ head.addName? head.addName:'创建'}}</el-button>
            </div>
            <div class="add">
                <el-button v-if="head.delete" size="mini" type="danger" @click="handleMultiDelete()" icon="el-icon-remove"
                    :disabled="!authority.delete">{{ head.deleteName ? head.deleteName : '删除' }}</el-button>
            </div>

        </div>
        <el-table :data="showData" :row-key="attribute['key']!=undefined? tableColumn[attribute['key']].prop :tableColumn[0].prop" 
            style="width: 100%;min-width: 400px;" empty-text :lazy="attribute['lazy']?attribute['lazy']:false" :load="onLoad"
            v-loading="dataLoading" @selection-change="handleSelectionChange" :default-expand-all="false" fit 
            :tree-props="{ hasChildren: 'hasChildren', children: 'children' }" border>
            <af-table-column type="selection" align="center"></af-table-column>
            <template v-for="(item, index) in tableColumn" v-if="!item.tableHide">
                <af-table-column :key="index" v-if="item.type == 'image'" :label="item.label" align="center" 
                    :width="item.width ? (item.width + 'px') : ''">
                    <template slot-scope="scope">
                        <el-image :preview-src-list="[scope.row[item.prop]]" shape="square"
                            style="width: 40px; height: 40px" fit="cover" :src="scope.row[item.prop]"></el-image>
                    </template>
                </af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'text'" :sortable="item.sortable" :label="item.label"
                    :prop="item.prop" :align="item.align" :width="item.width ? (item.width + 'px') : ''"></af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'number'" :sortable="item.sortable" :label="item.label"
                    :prop="item.prop" :align="item.align" :width="item.width ? (item.width + 'px') : ''"></af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'select'" :label="item.label" :align="item.align" 
                    :width="item.width ? (item.width + 'px') : ''">
                    <template slot-scope="scope">
                        {{ getSelect(item.option, scope.row[item.prop]) }}
                    </template>
                </af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'selectTag'" :label="item.label" :align="item.align"
                    :width="item.width ? (item.width + 'px') : ''">
                    <template slot-scope="scope">
                        <template v-if="item.multiple">
                            <el-tag v-for="(tag, id) in scope.row[item.prop]" :key="id"
                                :type="getTag(item.option, tag[item.childProp])">
                                {{ item.childProp ? getSelect(item.option, tag[item.childProp]) : getSelect(item.option, tag)}}
                            </el-tag>
                        </template>
                        <el-tag v-else :type="getTag(item.option, scope.row[item.prop])">
                            {{ getSelect(item.option, scope.row[item.prop]) }}
                        </el-tag>
                    </template>
                </af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'switch'" :label="item.label" :align="item.align"
                    :width="item.width ? (item.width + 'px') : ''">
                    <template slot-scope="scope">
                        <el-tag :type="getTag(item.option, scope.row[item.prop])">
                            {{ getSelect(item.option, scope.row[item.prop]) }}
                        </el-tag>
                    </template>
                </af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'input'" :label="item.label" :align="item.align"
                    :width="item.width ? (item.width + 'px') : ''">
                    <template slot-scope="scope">
                        <el-input v-model="scope.row[item.prop]" placeholder=""></el-input>
                    </template>
                </af-table-column>
                <af-table-column :key="index" v-else-if="item.type == 'date'" :sortable="item.sortable" :label="item.label"
                    :align="item.align" :width="item.width ? (item.width + 'px') : ''">
                    <template slot-scope="scope">
                        {{ scope.row[item.prop] }}
                    </template>
                </af-table-column>
            </template>
            <template>
                <el-table-column fixed="right" label="操作" :width="`${button_column_width}`" align="center">
                    <template slot-scope="scope">
                        <template v-for="button in tableButton" v-if="!button.hide">
                            <el-tooltip v-if="button.isIcon" :content="button.content" placement="top">
                                <el-button @click="handleButton(scope.row, button.handle)" :type="button.type"
                                    :disabled="!authority[button.handle]" :icon="button.icon" size="small"
                                    style="padding: 5px;font-size: 16px;"></el-button>
                            </el-tooltip>
                            <el-button v-else @click="handleButton(scope.row, button.handle)" :type="button.type"
                                :disabled="!authority[button.handle]" size="small" style="padding: 6px;font-size: 12px;">{{
                                    button.content }}</el-button>
                        </template>
                    </template>
                </el-table-column>
            </template>
        </el-table>
        <el-pagination style="margin-top: 12px;text-align: right;" background @size-change="sizeChange"
            @current-change="currentChange" :current-page="page.currentPage" :page-sizes="page.pageSizes"
            :page-size="page.pageSize" layout="total, sizes, prev, pager, next, jumper" :total="page.pageTotal">
        </el-pagination>
    </div>
</template>
<script>
export default {
    name: 'Table',
    props: {
        tableColumn: {
            type: Array,
            default() {
                return []
            }
        },
        tableButton: {
            type: Array,
            default() {
                return []
            }
        },
        attribute: {
            type: Object,
            default() {
                return {
                }
            }
        },
        head: {
            type: Object,
            default() {
                return {
                    placeholder: '输入关键字搜索',
                    add: true,
                    delete: true,
                    selects: []
                }
            }
        },
        page: {
            type: Object,
            default() {
                return {
                    currentPage: 1,
                    pageSizes: [10, 20, 30, 50],
                    pageSize: 10,
                    pageTotal: 0
                }
            }
        },
        authority: {
            type: Object,
            default() {
                return {
                    get: false,
                    add: false,
                    edit: false,
                    delete: false,
                }
            }
        }
    },
    data() {
        var get_select_form = () => {
            let obj = {};
            if (this.head.selects === undefined) {
                return
            }
            for (let i = 0; i < this.head.selects.length; i++) {
                if (this.head.selects[i].type == 'date' && this.head.selects[i].start && this.head.selects[i].end) {
                    this.$set(obj, this.head.selects[i].start, '');
                    this.$set(obj, this.head.selects[i].end, '');
                }
                else if (this.head.selects[i].prop && this.head.selects[i].default) {
                    this.$set(obj, this.head.selects[i].prop, this.head.selects[i].default);
                }
                else if (this.head.selects[i].prop) {
                    this.$set(obj, this.head.selects[i].prop, '');
                }
            }
            return obj;
        }
        return {
            show_muti_search: false,
            empty_select: get_select_form(),
            select_form: get_select_form(),
            condition: null,
            dataLoading: false,
            showData: [],
            search_key: '',
            select_key: '',
            multipleSelection: [],
            button_column_width: 0,
            pickerOptions: {
                shortcuts: [{
                    text: '最近一周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近一个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近三个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
            }
        }
    },
    mounted() {
        this.select_key = this.head.defaultSearch
        if (this.tableButton && this.tableButton.length > 0) {
            this.button_column_width = this.tableButton.length * 60
        }
    },
    watch: {
        search_key: {
            handler(val, old) {
                if (val != '') {
                    let form = {}
                    this.$set(form, this.select_key, val);
                    this.$emit('handleSearch', this.page.pageSize, 1, form)
                }
                else {
                    this.$emit('handleSearch', this.page.pageSize, this.page.currentPage)
                }
            },
            immediate: false,
            deep: true
        }
    },
    methods: {
        getData(data) {
            this.showData = data
        },
        handleSelectionChange(val) {
            this.multipleSelection = val;
        },
        sizeChange(val) {
            this.page.pageSize = val;
            this.pageData = this.queryPage();
        },
        changeTotal(val) {
            this.page.pageTotal = val
        },
        currentChange(val) {
            this.page.currentPage = val;
            this.pageData = this.queryPage();
        },
        queryPage() {
            this.$emit('queryPage', this.page.pageSize, this.page.currentPage, this.condition);
        },
        handleAdd() {
            this.$emit('handleAdd');
        },
        handleMultiDelete() {
            this.$emit('handleMultiDelete', this.multipleSelection)
        },
        handleButton(row, type) {
            if (type == 'edit') {
                this.$emit('handleEdit', row)
            }
            else if (type == 'delete') {
                this.$emit('handleDelete', row)
            }
            else if (type != undefined) {
                this.$emit(`handle${type}`, row)
            }
        },
        showSearch() {
            this.show_muti_search = !this.show_muti_search
        },
        handleSearch() {
            this.page.currentPage = 1;
            let form = {}
            for (let key in this.head.selects) {
                let type = this.head.selects[key].type
                let prop = this.head.selects[key].prop
                if (type == 'date') {
                    if (this.select_form[this.head.selects[key].start]) {
                        let exp = new RegExp('/', 'g')
                        form[this.head.selects[key].start] = this.select_form[this.head.selects[key].start][0].toLocaleDateString().replace(exp, '-')
                        form[this.head.selects[key].end] = this.select_form[this.head.selects[key].start][1].toLocaleDateString().replace(exp, '-')
                    }
                }
                else if (prop != undefined && this.select_form[prop] !== '') {
                    form[prop] = this.select_form[prop]
                }
            }
            this.condition = form
            this.$emit('handleSearch', this.page.pageSize, this.page.currentPage, this.condition)
        },
        searchReset() {
            this.select_form = JSON.parse(JSON.stringify(this.empty_select))
            this.condition = null
            this.$emit('handleSearch', this.page.pageSize, this.page.currentPage)
        },
        onLoad(tree, treeNode, resolve){
            this.$emit('load', tree, treeNode, resolve)
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
        getTag(option, value) {
            if (!option || option.length == 0) {
                return 'warning'
            }
            for (let i in option) {
                if (option[i].value != undefined && option[i].type != undefined) {
                    if (option[i].value == value) {
                        return option[i].type
                    }
                }
                else {
                    return 'warning'
                }
            }
            return 'warning'
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
</style>