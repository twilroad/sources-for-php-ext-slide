<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/slide/category/list`).then(response => {
                const data = response.data.data;
                next(vm => {
                    vm.list = data.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    vm.page.total = data.total;
                    vm.page.current_page = data.current_page;
                    vm.page.per_page = data.per_page;
                    vm.parent = to.query.parent;
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            });
        },
        data() {
            const self = this;
            return {
                addCategoryModal: false,
                addRules: {
                    category_name: [
                        {
                            message: '分类名称不能为空 ',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                categoryAdd: {
                    category_id: '',
                    category_name: '',
                },
                categoryDelete: {
                    category_id: '',
                },
                categoryDeleteId: '',
                categoryEdit: {
                    category_id: '',
                    category_name: '',
                },
                columns: [
                    {
                        key: 'name',
                        title: '分类名称',
                        width: 300,
                    },
                    {
                        key: 'alias',
                        title: '分类ID',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.$router.push({
                                                path: '/slide/group',
                                                query: {
                                                    id: data.row.alias,
                                                    name: data.row.name,
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.categoryEdit.category_id
                                                    = data.row.alias;
                                            self.categoryEdit.category_name
                                                    = data.row.name;
                                            self.editCategoryModal = true;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.deleteCategoryModal = true;
                                            self.categoryDeleteId = data.row.alias;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 300,
                    },
                ],
                deleteCategoryModal: false,
                deleteRules: {
                    category_id: [
                        {
                            message: '分类ID不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                editCategoryModal: false,
                editRules: {
                    category_name: [
                        {
                            message: '分类名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                list: [],
                loading: false,
                page: {
                    current_page: 1,
                    per_page: 0,
                    total: 0,
                },
                parent: 0,
                self: this,
            };
        },
        created() {
            this.page.total = 1;
        },
        methods: {
            addCategory() {
                this.addCategoryModal = true;
            },
            changePage(page) {
                const self = this;
                self.$loading.start();
                self.$notice.open({
                    title: '正在搜索数据...',
                });
                self.$http.post(`${window.api}/slide/category/list?page=${page}`).then(res => {
                    const data = res.data.data;
                    self.list = data.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    self.page.total = data.total;
                    self.page.current_page = data.current_page;
                    self.page.per_page = data.per_page;
                    self.$loading.finish();
                    self.$notice.open({
                        title: '搜索数据完成！',
                    });
                });
            },
            refreshData() {
                const self = this;
                self.$loading.start();
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$http.post(`${window.api}/slide/category/list`).then(response => {
                    const dataList = response.data.data;
                    self.list = dataList.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    self.page.total = dataList.total;
                    self.page.current_page = dataList.current_page;
                    self.page.per_page = dataList.per_page;
                    self.$loading.finish();
                    self.$notice.open({
                        title: '刷新数据完成！',
                    });
                });
            },
            submitAddCategory() {
                const self = this;
                let count = 0;
                const reg = /^\d*$/;
                const value = self.categoryAdd.category_id;
                if (!reg.test(value) && value !== '') {
                    count = -1;
                }
                if (count === -1) {
                    self.$notice.error({
                        title: '分类id请填写数字！',
                    });
                } else {
                    injection.loading.start();
                    self.loading = true;
                    self.$refs.categoryAdd.validate(valid => {
                        if (valid) {
                            self.$http.post(`${window.api}/slide/category/set`, self.categoryAdd).then(response => {
                                if (response.data.code === 200) {
                                    self.$notice.open({
                                        title: '新增分类信息成功！',
                                    });
                                    self.addCategoryModal = false;
                                    self.categoryAdd.category_name = '';
                                    self.categoryAdd.category_id = '';
                                    self.refreshData();
                                }
                            }).catch(() => {}).finally(() => {
                                self.loading = false;
                            });
                        } else {
                            self.loading = false;
                            self.$notice.error({
                                title: '请正确填写设置信息！',
                            });
                        }
                    });
                }
            },
            submitDeleteCategory() {
                const self = this;
                self.loading = true;
                injection.loading.start();
                self.$refs.categoryDelete.validate(valid => {
                    console.log(valid);
                    if (valid && self.categoryDeleteId === self.categoryDelete.category_id) {
                        self.$http.post(`${window.api}/slide/category/delete`, self.categoryDelete).then(response => {
                            if (response.data.code === 200) {
                                self.$notice.open({
                                    title: '删除分类信息成功！',
                                });
                                self.deleteCategoryModal = false;
                                self.self.categoryDelete.category_id = '';
                                self.refreshData();
                            }
                        }).catch(() => {}).finally(() => {
                            self.loading = false;
                        });
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            submitEditCategory() {
                const self = this;
                self.loading = true;
                injection.loading.start();
                self.$refs.categoryEdit.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/slide/category/update`, self.categoryEdit).then(response => {
                            if (response.data.code === 200) {
                                self.$notice.open({
                                    title: '编辑分类信息成功！',
                                });
                                self.editCategoryModal = false;
                                self.refreshData();
                            }
                        }).catch(() => {}).finally(() => {
                            self.loading = false;
                        });
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="setting-wrap">
        <div class="slide-wrap">
            <tabs value="name1">
                <tab-pane label="轮播图插件" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>关于</p>
                            <p>删除分类前，请先删除分类下的所有组图</p>
                        </div>
                        <div class="slide-list">
                            <i-button type="ghost" @click.native="addCategory">+新增分类</i-button>
                            <i-button type="text" icon="android-sync" class="refresh"
                            @click.native="refreshData">刷新</i-button>
                            <i-table class="slide-table"
                                     :columns="columns"
                                     :data="list"
                                     ref="slideList"
                                     highlight-row>
                            </i-table>
                            <div class="page">
                                <page :current="page.current_page"
                                      @on-change="changePage"
                                      :page-size="page.per_page"
                                      :total="page.total"
                                      v-if="page.total > page.per_page"
                                      show-elevator></page>
                            </div>
                        </div>
                        <modal
                                v-model="editCategoryModal"
                                title="编辑分类信息" class="upload-picture-modal">
                            <div class="slide-category-modal">
                                <i-form ref="categoryEdit" :model="categoryEdit" :rules="editRules" :label-width="100">
                                    <row>
                                        <i-col span="14">
                                            <form-item label="分类名称" prop="category_name">
                                                <i-input v-model="categoryEdit.category_name"></i-input>
                                                <p class="tip">商城前台不显示，名称仅用于后台标记分类</p>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="14">
                                            <form-item label="分类ID">
                                                {{ categoryEdit.category_id }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="14">
                                            <form-item>
                                                <i-button :loading="loading" type="primary"
                                                          @click.native="submitEditCategory">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                        <modal
                                v-model="addCategoryModal"
                                title="新增分类" class="upload-picture-modal">
                            <div class="slide-category-modal">
                                <i-form ref="categoryAdd" :model="categoryAdd" :rules="addRules" :label-width="100">
                                    <row>
                                        <i-col span="14">
                                            <form-item label="分类名称" prop="category_name">
                                                <i-input v-model="categoryAdd.category_name"></i-input>
                                                <p class="tip">商城前台不显示，名称仅用于后台标记分类</p>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="14">
                                            <form-item label="分类ID" prop="category_id">
                                                <i-input v-model="categoryAdd.category_id"></i-input>
                                                <p class="tip">可留空，系统会自动分配一个ID</p>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="14">
                                            <form-item>
                                                <i-button :loading="loading" type="primary"
                                                          @click.native="submitAddCategory">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                        <modal
                                v-model="deleteCategoryModal"
                                title="删除" class="upload-picture-modal">
                            <div class="slide-category-delete-modal">
                                <i-form ref="categoryDelete" :model="categoryDelete" :rules="deleteRules">
                                    <row>
                                        <i-col>
                                            <p>删除后不可恢复，请输入分类ID：{{ categoryDeleteId }} 以确认删除</p>
                                            <form-item prop="category_id">
                                                <i-input v-model="categoryDelete.category_id" style="width: 124px"></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col>
                                            <i-button :loading="loading" type="primary"
                                                      @click.native="submitDeleteCategory">
                                                <span v-if="!loading">删除</span>
                                                <span v-else>正在删除…</span>
                                            </i-button>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>