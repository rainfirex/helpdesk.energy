<template>
    <div class="auth">
        <div class="content">
            <div class="offset-lg-1 col-md-10">
                <h2 class="text-center">Авторизация</h2>
                <hr>
                <div class="form-group">
                    <label for="user">Пользователь</label>
                    <input type="text" class="form-control" id="user" aria-describedby="userHelp" v-model="user">
                    <small id="userHelp" class="form-text text-muted">Введите логин.</small>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" aria-describedby="passwordHelp" v-model="password">
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
              user: 'Poplavskiy_AA',
              password:'Qwerty12',

              errors: []
          }
        },

        computed:{

        },

        methods: {

            ...mapMutations(['setTextMessenger']),

            auth() {
                const url = `/api/user-auth/user/${this.user}/password/${this.password}`;
                axios.get(url).then(response => {
                    if (response.data.success) {
                        Auth.login(response.data);
                        Auth.init();
                        this.$router.push('/');
                        this.setTextMessenger({text: 'Вы вошли в систему', status: 'success'});
                    } else {
                        // this.$store.commit('setTextMessenger', {text: response.data.message, status: 'error'});
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
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
