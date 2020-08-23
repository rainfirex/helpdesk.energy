<template>
    <div class="content">
        <h2 class="text-center">Создать заявку</h2>
        <hr>

        <div class="offset-md-1 col-md-10">

            <div class="mb-4">
                <button class="btn btn-secondary" @click="$router.go(-1)">Назад</button>
            </div>

            <!--                <div class="form-group">-->
            <!--                    <label for="user">Пользователь</label>-->
            <!--                    <input type="text" class="form-control" id="user" aria-describedby="userHelp" v-model="user.name">-->
            <!--                    <small id="userHelp" class="form-text text-muted">Фамилия имя отчество заявителя.</small>-->
            <!--                </div>-->

            <!--                <div class="form-group">-->
            <!--                    <label for="email">Email адрес</label>-->
            <!--                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" v-model="user.email">-->
            <!--                    <small id="emailHelp" class="form-text text-muted">Электронная почта для обратной связи.</small>-->
            <!--                </div>-->

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
                <small id="categoryHelp" class="form-text text-muted" :class="{'is-error': $v.category.$error}">Категория
                    заявки.
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
                <small id="descriptionHelp" class="form-text text-muted" :class="{'is-error': $v.description.$error}">Основной
                    текст заявки.
                    <span v-if="!$v.description.required" class="error-text"
                          :class="{'error-show': !$v.description.required}">Поле пустое</span>
                    <span v-if="!$v.description.minLength" class="error-text"
                          :class="{'error-show': !$v.description.minLength}">Минимум 4 символа</span>
                </small>
            </div>

            <div class="form-group">
                <label for="description">Загрузка файлов</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" multiple @change="selectFiles"
                           accept=".jpg, .jpeg, .png">
                    <label class="custom-file-label">Файл...</label>
                </div>
                <div class="drop-container mt-2" ref="dropContainer"
                     @dragenter="dropEnter"
                     @drop="dropFiles"
                     @dragleave="dropLeave"
                     @dragover="dropOver">
                    Перетащите в эту область графические файлы формата: .png, .jpeg, .jpg
                </div>
            </div>

            <div class="form-group">
                <ul>
                    <li v-for="(file, index) in files">{{ index +1 }} - {{ file.name }}</li>
                </ul>
            </div>

            <div class="text-center">
                <button type="button" class="btn btn-outline-dark" @click="createTicket">Создать заявку</button>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';
    import { required, minLength } from 'vuelidate/lib/validators'
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

        computed: {

            user(){
                return this.$store.state.Auth;
            },
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            createTicket() {

                this.$v.$touch();

                if (this.$v.$invalid) {
                    this.setTextMessenger({text: 'Заполните все поля!', status: 'error'});
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

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.post(url, frmData).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success){
                        this.$router.push('/');
                        this.setTextMessenger({text: `Ваша заявка создана под номером: ${response.data.ticket_number}`, status: 'success'});
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            selectFiles(e) {

                e.preventDefault();

                if (e.target.files.length <= 0) return;

                this.files = e.target.files;

            },

            dropEnter(e) {
                e.preventDefault();
                e.stopPropagation();
                this.$refs.dropContainer.classList.add('drop-container-enter');
            },

            dropFiles(e) {
                e.preventDefault();
                e.stopPropagation();

                this.files = e.dataTransfer.files;

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

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {
                    this.changeLoaderBarMode(false);
                    if (response.data.success) {
                        this.categories = response.data.categories;
                    }
                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            }
        },

        created() {
            this.department = this.user.department;
            this.phone = this.user.phone;

            this.getCategories();
        }
    }
</script>

<style lang="scss" scoped>
    .custom-file-label::after{content:"Выбрать" !important;}

    .drop-container {
        background-color: #f0f1f2;
        height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #7984ca;
        font-size: 11px;
    }

    .drop-container-enter{
        border: solid 1px #7698c2;
        box-shadow: 0 0 0 0.2rem rgb(0 135 255 / 25%);
    }
</style>
