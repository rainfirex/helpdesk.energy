<template>
    <div class="auth">
        <div class="content">
            <div class="offset-lg-1 col-md-10">
                <h2 class="text-center">Авторизация</h2>
                <hr>
                <div class="form-group">
                    <label for="user">Пользователь</label>
                    <input type="text" class="form-control" id="user" aria-describedby="userHelp" v-model="user.login">
                    <small id="userHelp" class="form-text text-muted">Введите логин.</small>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" aria-describedby="passwordHelp" v-model="user.password">
                    <small id="passwordHelp" class="form-text text-muted">Введите пароль.</small>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-outline-dark" @click="auth">Войти</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Auth from "../../Auth";
    import {mapMutations} from 'vuex';

    export default {
        name: "Auth",

        data() {
          return{

              user: {
                  login: 'Poplavskiy_AA',
                  password:'Qwerty12'
              },

              errors: []
          }
        },

        methods: {

            ...mapMutations(['setTextMessenger']),

            auth() {
                const url = `/api/auth/login`;
                axios.post(url, {login: this.user.login, password: this.user.password}).then(response => {
                    if (response.data.success) {
                        Auth.login(response.data);
                        Auth.init();
                        this.$router.push('/');
                        this.setTextMessenger({text: 'Вы вошли в систему', status: 'success'});
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            logout() {
                const url = `/api/auth/logout`;
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.auth.api_token;
                axios.post(url).then(response => {
                    if (response.data.success) {
                        Auth.logout();
                        Auth.init();
                        this.$store.commit('setTextMessenger', {text: 'Вы вышли из системы', status: 'success'});
                        if(this.$router.history.current.path !== '/') {
                            this.$router.push('/');
                        }
                    } else {
                        this.$store.commit('setTextMessenger', {text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    this.errors = error.response.data.errors;
                    this.$store.commit('setTextMessenger', {text: this.errors, status: 'error'});
                });
            }
        },
    }
</script>

<style scoped>
    .auth{
        width: 80%;
        margin: 0 auto;
    }

</style>
