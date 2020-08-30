<template>
    <div class="content">
        <div class="offset-1 col-10 offset-md-2 col-md-8">
            <h2 class="text-center">Авторизация</h2>
            <hr>
            <div class="form-group">
                <label for="user">Пользователь</label>
                <input type="text" class="form-control" id="user" aria-describedby="userHelp" v-model="login"
                       :class="{'error-input': $v.login.$error}">
                <small id="userHelp" class="form-text text-muted" :class="{'is-error': $v.login.$error}">Введите логин.
                    <span v-if="!$v.login.required" class="error-text" :class="{'error-show': !$v.login.required}">Поле пустое</span>
                </small>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" id="password" aria-describedby="passwordHelp"
                       v-model="password" :class="{'error-input': $v.password.$error}">
                <small id="passwordHelp" class="form-text text-muted" :class="{'is-error': $v.password.$error}">Введите
                    пароль.
                    <span v-if="!$v.password.required" class="error-text"
                          :class="{'error-show': !$v.password.required}">Поле пустое</span>
                    <span v-if="!$v.password.minLength" class="error-text"
                          :class="{'error-show': !$v.password.minLength}">Минимум 8 символа</span></small>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-outline-dark" @click="auth">Войти</button>
            </div>
        </div>
    </div>
</template>

<script>
    import Auth from "../../Auth";
    import {mapMutations} from 'vuex';
    import { required, minLength } from 'vuelidate/lib/validators'

    export default {
        name: "Auth",

        data() {
          return{

              login: '',

              password:'',

              errors: []
          }
        },

        computed: {
            user(){
                return this.$store.state.Auth;
            },
        },

        validations: {
            login: {
                required
            },

            password: {
                required,
                minLength: minLength(8)
            }
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'playSound']),

            auth() {

                this.$v.$touch();

                if (this.$v.$invalid) {
                    this.playSound('/sounds/_alert.mp3');
                    this.setTextMessenger({text: 'Заполните все поля!', status: 'error'});
                    return false;
                }

                const url = `/api/auth/login`;
                axios.post(url, {login: this.login, password: this.password}).then(response => {
                    if (response.data.success) {

                        Auth.login(response.data);
                        Auth.init();

                        this.setTextMessenger({text: 'Вы вошли в систему', status: 'success'});
                        this.playSound('/sounds/_mission.mp3');

                        this.$router.push('/');

                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                        this.playSound('/sounds/_alert.mp3');
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            listenerKeyDown(e) {
                if(e.code === 'Enter' && e.key === 'Enter') {
                    this.auth();
                }

                if (e.code === 'Escape' && e.key === 'Escape') {
                    if (this.login.length > 0 || this.password.length > 0) {
                        if(confirm('Сбросить поля ввода?')) {
                            this.login = '';
                            this.password = '';
                        }
                    }
                }
            }

            // logout() {
            //     const url = `/api/auth/logout`;
            //     axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.auth.api_token;
            //     axios.post(url).then(response => {
            //         if (response.data.success) {
            //             PlayAudio('/sounds/_ping.mp3');
            //             Auth.logout();
            //             Auth.init();
            //             this.$store.commit('setTextMessenger', {text: 'Вы вышли из системы', status: 'success'});
            //             // if(this.$router.history.current.path !== '/') {
            //                 this.$router.push('/');
            //             // }
            //         } else {
            //             this.$store.commit('setTextMessenger', {text: response.data.message, status: 'error'});
            //         }
            //     }).catch(error => {
            //         this.errors = error.response.data.errors;
            //         this.$store.commit('setTextMessenger', {text: this.errors, status: 'error'});
            //     });
            // }
        },

        mounted() {
            document.body.addEventListener('keydown', this.listenerKeyDown);
        },

        beforeDestroy() {
            document.body.removeEventListener('keydown', this.listenerKeyDown);
        }

        // beforeRouteEnter(to, from, next) {
        //     console.log(this.$store);
        // }
    }
</script>
