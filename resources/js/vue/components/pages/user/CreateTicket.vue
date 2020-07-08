<template>
    <div class="create-ticket">
        <div class="content">
            <h2 class="text-center">Создать заявку</h2>
            <hr>
            <div class="offset-1 col-md-10">
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
                    <label for="department">Ваш отдел</label>
                    <input type="text" class="form-control" id="department" aria-describedby="emailHelp" required v-model="user.department">
                    <small id="departmentHelp" class="form-text text-muted">Отдел заявителя.</small>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="tel" class="form-control" id="phone" aria-describedby="phoneHelp" required v-model="user.phone">
                    <small id="phoneHelp" class="form-text text-muted">Номер телефона для обратной связи.</small>
                </div>

                <div class="form-group">
                    <label for="title">Название</label>
                    <input type="text" class="form-control" id="title" aria-describedby="titleHelp" required v-model="title">
                    <small id="titleHelp" class="form-text text-muted">Название заявки.</small>
                </div>

                <div class="form-group">
                    <label for="category">Категория</label>
                    <select class="form-control" id="category" aria-describedby="categoryHelp" required v-model="category">
                        <option>Сеть</option>
                        <option>Компьютер</option>
                        <option>Телефония</option>
                        <option>Другое</option>
                    </select>
                    <small id="categoryHelp" class="form-text text-muted">Обезательное поле.</small>
                </div>

                <div class="form-group">
                    <label for="description">Описание заявки</label>
                    <textarea class="form-control" id="description" rows="5" style="height: 300px" required v-model="description"></textarea>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-outline-dark" @click="createTicket">Создать заявку</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';

    export default {
        name: "CreateTicket",

        data() {
            return {
                errors: null,

                category: '',
                title: '',
                description: ''
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

                const frmData = new FormData();
                // frmData.append('user_id', this.user.user_id);
                // frmData.append('name',  this.user.name);
                // frmData.append('email', this.user.email);
                frmData.append('phone', this.user.phone);
                frmData.append('department', this.user.department);
                frmData.append('category', this.category);
                frmData.append('title', this.title);
                frmData.append('description', this.description);

                const url = `/api/create-ticket`;

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
            }
        }
    }
</script>

<style scoped>
    .create-ticket{
        width: 80%;
        margin: 0 auto;
    }
</style>
