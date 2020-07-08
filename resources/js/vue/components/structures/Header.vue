<template>
    <header>
        <Navigator></Navigator>
        <div class="user-panel col-md-12" v-if="username">
            <div class="row p-1">
                <div class="col-md-5">
                    <p class="mb-0 p-1 pl-4">Вы авторизованы как "{{username}}"</p>
                </div>
                <div class="offset-md-2 col-md-5 text-right">
                    <button class="btn btn-light btn-sm p-1" @click="logout()">Выход</button>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
    import Navigator from "./Navigator";
    import Auth from "../../Auth";

    export default {
        name: "Header",
        data() {
            return {
                errors : []
            }
        },
        components: {
            Navigator
        },
        computed: {
            username(){
                return this.$store.state.Auth.name;
            },
            auth() {
                return this.$store.state.Auth;
            }
        },
        methods:{
            logout() {
                const url = `/api/user-auth/logout`;
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
        }
    }
</script>

<style scoped>
    header{
        background-color: #363636;
        min-height: 26px;

        position: sticky;
        top: 0;
        z-index: 999;
    }
    .user-panel{
        background-color: #007bff;
        color: white;
    }
</style>
