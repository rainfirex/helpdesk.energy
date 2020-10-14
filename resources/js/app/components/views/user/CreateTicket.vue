<template>
    <div class="content">
        <h2 class="text-center">Создать заявку</h2>
        <hr>

        <div class="offset-md-1 col-md-10">
            <div class="mb-4">
                <button class="btn btn-secondary" @click="$router.go(-1)" title="Назад"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
            </div>

            <div class="form-group">
                <label for="department">Отдел</label>
                <input type="text" class="form-control" id="department" aria-describedby="departmentHelp"
                       v-model="department"
                       :class="{'error-input': $v.department.$error}"
                       @change="$v.department.$touch"
                >
                <small id="departmentHelp" class="form-text text-muted" :class="{'is-error': $v.department.$error}">Отдел
                    заявителя.
                    <span v-if="!$v.department.required" class="error-text"
                          :class="{'error-show': !$v.department.required}">Поле пустое</span>
                    <span v-if="!$v.department.minLength" class="error-text"
                          :class="{'error-show': !$v.department.minLength}">Минимум 6 символа</span>
                </small>
            </div>

            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" class="form-control" id="phone" aria-describedby="phoneHelp" v-model="phone"
                       :class="{'error-input': $v.phone.$error}"
                       @change="$v.phone.$touch"
                >
                <small id="phoneHelp" class="form-text text-muted" :class="{'is-error': $v.phone.$error}">Номер телефона
                    для обратной связи.
                    <span v-if="!$v.phone.required" class="error-text" :class="{'error-show': !$v.phone.required}">Поле пустое</span>
                    <span v-if="!$v.phone.minLength" class="error-text" :class="{'error-show': !$v.phone.minLength}">Минимум 6 символа</span>
                </small>
            </div>

            <div class="form-group">
                <label for="title">Название</label>

                <input type="text" class="form-control" id="title" aria-describedby="titleHelp" v-model="title"
                       :class="{'error-input': $v.title.$error}"
                       @change="$v.title.$touch()"
                >
                <small id="titleHelp" class="form-text text-muted" :class="{'is-error': $v.title.$error}">Название
                    заявки.
                    <span v-if="!$v.title.required" class="error-text" :class="{'error-show': !$v.title.required}">Поле пустое</span>
                    <span v-if="!$v.title.minLength" class="error-text" :class="{'error-show': !$v.title.minLength}">Минимум 4 символа</span>
                </small>
            </div>

            <div class="form-group">
                <label for="category">Категория</label>
                <select class="form-control" id="category" aria-describedby="categoryHelp" v-model="category"
                        :class="{'error-input': $v.category.$error}"
                        @change="$v.description.$touch()"
                >
                    <option :value="cat.title" v-for="cat in categories" :key="cat.id">{{cat.title}}</option>

                </select>
                <small id="categoryHelp" class="form-text text-muted" :class="{'is-error': $v.category.$error}">Категория заявки.
                    <span v-if="!$v.category.required" class="error-text"
                          :class="{'error-show': !$v.category.required}">Поле пустое</span>
                </small>
            </div>

            <div class="form-group">
                <label for="description">Описание заявки</label>
                <textarea class="form-control" id="description" rows="5" style="height: 300px" v-model="description"
                          :class="{'error-input': $v.description.$error}"
                          @change="$v.description.$touch()"
                ></textarea>
                <small id="descriptionHelp" class="form-text text-muted" :class="{'is-error': $v.description.$error}">Основной текст заявки.
                    <span v-if="!$v.description.required" class="error-text"
                          :class="{'error-show': !$v.description.required}">Поле пустое</span>
                    <span v-if="!$v.description.minLength" class="error-text"
                          :class="{'error-show': !$v.description.minLength}">Минимум 4 символа</span>
                </small>
            </div>

            <div class="form-group">
                <label for="description">Загрузка файлов</label>
                <div style="font-size: 0.8em; line-height: 30px">
                </div>
                <div class="custom-file d-none">
                    <input type="file" class="custom-file-input"
                           multiple @change="selectFiles"
                           ref="fileInput"
                           accept="image/jpeg, image/jpg, image/png" >
                    <label class="custom-file-label">Файл...</label>
                </div>
                <div class="drop-container mt-2 form-control" ref="dropContainer" tabindex="7"
                     @dragenter="dropEnter"
                     @drop="dropFiles"
                     @dragleave="dropLeave"
                     @dragover="dropOver"
                     @click="$refs.fileInput.click()">
                    <p class="mb-0">Нажмите или перетащите в эту область графические файлы формата: .png, .jpeg, .jpg<br>Вставить из буфера <b>(ctrl+v)</b></p>
                </div>
            </div>

            <div class="form-group" v-if="files.length > 0">
                <p>Загружаемые файлы</p>
                <ul class="list-unstyled d-flex flex-wrap justify-content-between">
                    <li class="item-prev p-2 mb-2"
                        v-for="(file, index) in files">
                        <div class="img-container">
                            <p class="title-img">{{ index +1 }} - {{ file.name }}&nbsp;</p>
                            <img  :alt="file.name" :ref="'image_'+parseInt( index )"/>
                            <i class="fa fa-trash-o ico-img-remove" @click="removeFile(index)" title="Убрать файл"></i>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="text-center">
                <button type="button" class="btn btn-outline-dark" @click="createTicket">Создать заявку</button>
            </div>
        </div>
    </div>
</template>

<script>
    import { required, minLength } from 'vuelidate/lib/validators'
    import { mapGetters, mapActions } from 'vuex';
    import Sound from "../../../assets/js/Sound";
    export default {
        name: "CreateTicket",

        data() {
            return {
                errors: null,

                department: '',
                phone: '',
                category: '',
                title: '',
                description: '',

                categories: [],

                files: []
            }
        },

        validations: {
            department : {
                required,
                minLength: minLength(6)
            },

            phone: {
                required,
                minLength: minLength(6)
            },

            category : {
                required
            },

            title: {
                required,
                minLength: minLength(4)
            },

             description: {
                required,
                minLength: minLength(4)
            }
        },

        computed: mapGetters(['getUser']),

        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),

            createTicket() {
                this.$v.$touch();

                if (this.$v.$invalid) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Заполните все поля!', status: 'error'});
                    return false;
                }

                const frmData = new FormData();
                frmData.append('phone', this.phone);
                frmData.append('department', this.department);
                frmData.append('category', this.category);
                frmData.append('title', this.title);
                frmData.append('description', this.description);

                for (let i = 0; i < this.files.length; i++) {
                    frmData.append('file_'+i, this.files[i])
                }

                const url = `/api/user/tickets/create`;
                this.setLoaderBar(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.getUser.api_token;
                axios.post(url, frmData).then(response => {

                    this.setLoaderBar(false);

                    if (response.data.success){
                        this.$router.push('/');

                        this.setMessenger({text: `Ваша заявка создана под номером: ${response.data.ticket_number}`, status: 'success'});
                        Sound.playSound('/sounds/_notify.mp3');
                    } else {
                        this.setMessenger({text: response.data.message, status: 'error'});
                        Sound.playSound('/sounds/_alert.mp3');
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            selectFiles(e) {
                e.preventDefault();

                if (e.target.files.length <= 0) return;

                let isErrors = false;
                let ignorFiles = [];

                for (const file of e.target.files) {

                    let extFile = file.name.substr(file.name.lastIndexOf(".")+1, file.name.length).toLowerCase();

                    if (extFile === 'jpg' || extFile === 'png' || extFile === 'jpeg') {
                        this.files.push(file);
                    } else {
                        isErrors = true;
                        ignorFiles.push(file.name)
                    }
                }
                if (isErrors) {
                    this.setMessenger({text: `Некоторые файлы были проигнорированы: ${ignorFiles.map(item => `"${item}"`).join("\n\r")}`, status: 'error'});
                }
            },

            dropEnter(e) {
                e.preventDefault();
                e.stopPropagation();
                this.$refs.dropContainer.classList.add('drop-container-enter');
            },

            dropFiles(e) {
                e.preventDefault();
                e.stopPropagation();

                for (const file of e.dataTransfer.files) {
                    this.files.push(file);
                }

                this.$refs.dropContainer.classList.remove('drop-container-enter');
            },

            dropLeave(e) {
                e.preventDefault();
                e.stopPropagation();
                this.$refs.dropContainer.classList.remove('drop-container-enter');
            },

            dropOver(e) {
                e.preventDefault();
                e.stopPropagation();
            },

            getCategories() {
                const url = `/api/user/categories`;
                this.setLoaderBar(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.getUser.api_token;
                axios.get(url).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        this.categories = response.data.categories;
                    }
                }).catch(error => {
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            pstImage(event) {
                let items = (event.clipboardData || event.originalEvent.clipboardData).items;
                // will give you the mime types
                for (const index in items) {
                    let item = items[index];

                    if (item.kind === 'file') {
                        const file = item.getAsFile();
                        this.files.push(file);
                    }
                }
            },

            removeFile(index){
                this.files.splice(index, 1);
            },

            renderImage(){
                for (let index in this.files) {

                    let reader = new FileReader();

                    reader.addEventListener('load', () => {
                        this.$refs['image_'+parseInt(index)][0].src = reader.result;
                    }, false);

                    reader.readAsDataURL(this.files[index]);
                }
            },

            listenerKeyDown(e) {
                if (e.code === 'Enter' && e.key === 'Enter') {
                    this.createTicket();
                }
            }
        },

        watch: {
          files() {
              this.renderImage();
          }
        },

        created() {
            this.department = this.getUser.department;
            this.phone = this.getUser.phone;

            this.getCategories();
        },

        mounted() {
            document.body.addEventListener('paste', this.pstImage);
            document.body.addEventListener('keydown', this.listenerKeyDown);
        },

        beforeDestroy() {
            document.body.removeEventListener('paste', this.pstImage);
            document.body.removeEventListener('keydown', this.listenerKeyDown);
        }
    }
</script>

<style lang="scss" scoped>
    .custom-file-label::after{content:"Обзор" !important;}

    .drop-container {
        background-color: #f0f1f2;
        height: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #809a9e;
        font-size: 11px;
        cursor: pointer;
        transition: 1s;
        line-height: 35px;

        &:hover{
            background-color: #fdfeff;
        }
    }

    .drop-container-enter{
        border: solid 1px #7698c2;
        box-shadow: 0 0 0 0.2rem rgb(0 135 255 / 25%);
    }

    .item-prev{
        background-color: white;
        font-size: small;
        font-family: monospace;
    }

    .img-container{
        position: relative;
        width: 250px;

        .title-img{
            position: absolute;
            top: 0;
            left: 0;
            background: #80777894;
            color: white;
            padding: 5px;
            width: 100%;
            overflow: hidden;
            text-align: center;
            white-space: nowrap;
        }

        .ico-img-remove {
            cursor: pointer;
            position: absolute;
            bottom: 0;
            right: 0;
            background: #80777894;
            padding: 10px;
            color: #c1c1c1;
            font-size: 1.3em;
        }

        .ico-img-remove:hover {
            color: #e4e2e2;
        }

        img {
            width: 100%;
        }
    }
</style>
