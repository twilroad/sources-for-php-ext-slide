<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/slide/picture/list`, {
                group_id: to.query.id,
            }).then(response => {
                const data = response.data.data;
                next(vm => {
                    if (data.length > 0) {
                        vm.pictureDetail = data[0];
                        if (data[0] === undefined) {
                            vm.form.path1 = '';
                        } else if (data[1] === undefined) {
                            vm.form.path2 = '';
                        } else if (data[2] === undefined) {
                            vm.form.path3 = '';
                        } else if (data[3] === undefined) {
                            vm.form.path4 = '';
                        }
                        if (data[0] !== undefined) {
                            vm.form.path1 = data[0].path;
                        }
                        if (data[1] !== undefined) {
                            vm.form.path2 = data[1].path;
                        }
                        if (data[2] !== undefined) {
                            vm.form.path3 = data[2].path;
                        }
                        if (data[3] !== undefined) {
                            vm.form.path4 = data[3].path;
                        }
                    }
                    vm.parent.id = to.query.id;
                    vm.parent.name = to.query.name;
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            });
        },
        data() {
            return {
                action: `${window.api}/slide/picture/upload?group_id=${this.$route.query.id}`,
                colorPicker: false,
                form: {
                    path1: '',
                    path2: '',
                    path3: '',
                    path4: '',
                },
                loading: false,
                paramCount: '',
                parent: {
                    id: '',
                    name: '',
                },
                pictureDetail: {
                    background: '',
                    link: '',
                    title: '',
                },
                pictureList: [
                    {
                        path1: '',
                    },
                ],
                rules: {
                    path1: [
                        {
                            message: '上传图片不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            colorShow() {
                this.colorPicker = true;
            },
            getDetailMessage(param) {
                const self = this;
                self.paramCount = param;
                let count = '';
                if (param === 1) {
                    count = self.form.path1;
                } else if (param === 2) {
                    count = self.form.path2;
                } else if (param === 3) {
                    count = self.form.path3;
                } else if (param === 4) {
                    count = self.form.path4;
                }
                injection.loading.start();
                self.$http.post(`${window.api}/slide/picture/get`, {
                    path: count,
                }).then(response => {
                    self.pictureDetail = response.data.data;
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            removeSlide(param) {
                const self = this;
                let count = '';
                if (param === 1) {
                    count = self.form.path1;
                } else if (param === 2) {
                    count = self.form.path2;
                } else if (param === 3) {
                    count = self.form.path3;
                } else if (param === 4) {
                    count = self.form.path4;
                }
                self.$http.post(`${window.api}/slide/picture/delete`, {
                    path: count,
                }).then(() => {
                    self.$notice.open({
                        title: '删除图片信息成功！',
                    });
                    self.$loading.start();
                    self.$notice.open({
                        title: '正在刷新数据...',
                    });
                    if (param === 1) {
                        self.form.path1 = '';
                    } else if (param === 2) {
                        self.form.path2 = '';
                    } else if (param === 3) {
                        self.form.path3 = '';
                    } else if (param === 4) {
                        self.form.path4 = '';
                    }
                }).catch(() => {
                    self.$notice.error({
                        title: '删除图片信息错误！',
                    });
                });
            },
            removeSlide2() {
                this.form.picture2 = '';
            },
            removeSlide3() {
                this.form.picture3 = '';
            },
            removeSlide4() {
                this.form.picture4 = '';
            },
            submit() {
                const self = this;
                let count = '';
                self.loading = true;
                injection.loading.start();
                if (self.paramCount === 1) {
                    count = self.form.path1;
                }
                if (self.paramCount === 2) {
                    count = self.form.path2;
                }
                if (self.paramCount === 3) {
                    count = self.form.path3;
                }
                if (self.paramCount === 4) {
                    count = self.form.path4;
                }
                const params = {
                    path: count,
                    title: self.pictureDetail.title,
                    link: self.pictureDetail.link,
                    background: '',
                };
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/slide/picture/set`, params).then(response => {
                            if (response.data.code === 200) {
                                self.$notice.open({
                                    title: '设置图片信息成功！',
                                });
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
            updateValue() {
                this.colorPicker = false;
            },
            uploadBefore() {
                injection.loading.start();
            },
            uploadFormatError(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadSuccess1(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message[0],
                });
                self.form.path1 = data.data.path;
            },
            uploadSuccess2(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message[0],
                });
                self.form.path2 = data.data.path;
            },
            uploadSuccess3(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message[0],
                });
                self.form.path3 = data.data.path;
            },
            uploadSuccess4(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message[0],
                });
                self.form.path4 = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="setting-wrap">
        <div class="slide-group-edit">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>设置 "{{ parent.name }}" 组图</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <row>
                        <i-col span="14">
                            <form-item label="上传图片" prop="path1">
                                <div class="upload-picture-box">
                                    <div class="image-preview" v-if="form.path1" @click="getDetailMessage(1)">
                                        <img :src="form.path1">
                                        <icon type="ios-trash-outline" @click.native="removeSlide(1)"></icon>
                                    </div>
                                    <upload class="local-upload"
                                            :action="action"
                                            :before-upload="uploadBefore"
                                            :format="['jpg','jpeg','png']"
                                            :headers="{
                                                Authorization: `Bearer ${$store.state.token.access_token}`
                                            }"
                                            :max-size="2048"
                                            :on-error="uploadError"
                                            :on-format-error="uploadFormatError"
                                            :on-success="uploadSuccess1"
                                            ref="upload"
                                            :show-upload-list="false">
                                        <div class="clearfix upload-picture">
                                            <span>本地上传</span>
                                        </div>
                                    </upload>
                                    <div class="picture-gallery-upload ivu-upload">
                                        <div class="clearfix upload-picture">
                                            <span>图片库上传</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="upload-picture-box">
                                    <div class="image-preview" v-if="form.path2" @click="getDetailMessage(2)">
                                        <img :src="form.path2">
                                        <icon type="ios-trash-outline" @click.native="removeSlide(2)"></icon>
                                    </div>
                                    <upload class="local-upload"
                                            :action="action"
                                            :before-upload="uploadBefore"
                                            :format="['jpg','jpeg','png']"
                                            :headers="{
                                                Authorization: `Bearer ${$store.state.token.access_token}`
                                            }"
                                            :max-size="2048"
                                            :on-error="uploadError"
                                            :on-format-error="uploadFormatError"
                                            :on-success="uploadSuccess2"
                                            ref="upload"
                                            :show-upload-list="false">
                                        <div class="clearfix upload-picture">
                                            <span>本地上传</span>
                                        </div>
                                    </upload>
                                    <div class="picture-gallery-upload ivu-upload">
                                        <div class="clearfix upload-picture">
                                            <span>图片库上传</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="upload-picture-box">
                                    <div class="image-preview" v-if="form.path3" @click="getDetailMessage(3)">
                                        <img :src="form.path3">
                                        <icon type="ios-trash-outline" @click.native="removeSlide(3)"></icon>
                                    </div>
                                    <upload class="local-upload"
                                            :action="action"
                                            :before-upload="uploadBefore"
                                            :format="['jpg','jpeg','png']"
                                            :headers="{
                                                Authorization: `Bearer ${$store.state.token.access_token}`
                                            }"
                                            :max-size="2048"
                                            :on-error="uploadError"
                                            :on-format-error="uploadFormatError"
                                            :on-success="uploadSuccess3"
                                            ref="upload"
                                            :show-upload-list="false">
                                        <div class="clearfix upload-picture">
                                            <span>本地上传</span>
                                        </div>
                                    </upload>
                                    <div class="picture-gallery-upload ivu-upload">
                                        <div class="clearfix upload-picture">
                                            <span>图片库上传</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="upload-picture-box">
                                    <div class="image-preview" v-if="form.path4" @click="getDetailMessage(4)">
                                        <img :src="form.path4">
                                        <icon type="ios-trash-outline" @click.native="removeSlide(4)"></icon>
                                    </div>
                                    <upload class="local-upload"
                                            :action="action"
                                            :before-upload="uploadBefore"
                                            :format="['jpg','jpeg','png']"
                                            :headers="{
                                                Authorization: `Bearer ${$store.state.token.access_token}`
                                            }"
                                            :max-size="2048"
                                            :on-error="uploadError"
                                            :on-format-error="uploadFormatError"
                                            :on-success="uploadSuccess4"
                                            ref="upload"
                                            :show-upload-list="false">
                                        <div class="clearfix upload-picture">
                                            <span>本地上传</span>
                                        </div>
                                    </upload>
                                    <div class="picture-gallery-upload ivu-upload">
                                        <div class="clearfix upload-picture">
                                            <span>图片库上传</span>
                                        </div>
                                    </div>
                                </div>
                            </form-item>
                        </i-col>
                    </row>
                    <h5>
                        图片详情设置
                        <span>提示：每编辑完成一组详情后需要点击保存</span>
                    </h5>
                    <row>
                        <i-col span="14">
                            <form-item label="图片标题" prop="title">
                                <i-input v-model="pictureDetail.title"></i-input>
                                <p class="tip">图片标题文字将作为图片Alt形式显示</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="图片跳转链接" prop="link">
                                <i-input v-model="pictureDetail.link"></i-input>
                                <p class="tip">输入图片要跳转的URL地址，正确格式应以http://开头，点击后将以"_blank"形式另打开页面</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item>
                                <i-button :loading="loading" type="primary"
                                          @click.native="submit">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                                <!--<i-button type="ghost">更新板块内容</i-button>-->
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
    </div>
</template>