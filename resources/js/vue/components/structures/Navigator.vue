<template>
    <div>
        <div class="row m-0 p-1 navigator">

            <div class="col-12 col-sm-8 col-md-7 d-none d-sm-none d-md-block">
                <ul class="p-md-0">
                    <li v-for="item in nav">
                        <router-link
                            class="nav-link"
                            :to="item.path"
                            active-class="active"
                            v-if="item.auth === 'both' || item.auth === auth.login"
                        >{{item.title}}
                        </router-link>
                    </li>

                    <li v-for="item in navHandler">
                        <router-link
                            class="nav-link"
                            :to="item.path"
                            active-class="active"
                            v-if="item.auth === auth.login && item.is_handler === auth.is_handler"
                        >{{item.title}}
                        </router-link>
                    </li>
                </ul>
            </div>

            <div class="col-12 text-md-right col-md-5 row">
                <div class="col-2 offset-1 offset-md-7 col-md-2">
                    <span class="user" @click="isShowUser = !isShowUser">{{letter}}</span>
                </div>
                <div class="col-2 offset-7 offset-md-1 col-md-2 align-self-center" v-if="auth.login">
                    <button class="btn btn-light btn-sm p-1" @click="logout()">Выход</button>
                </div>
            </div>

        </div>
        <div class="col-12 offset-md-7 col-md-3 offset-lg-9 user-panel p-0" v-if="isShowUser">
            <div v-if="isShowUser && username" class="p-1">
                <div>
                    <p class="mb-0 p-2">Вы авторизованы как "{{username}}"</p>
                    <p class="mb-0 p-2">{{department}}</p>
                </div>
                <div>
                    <p class="mb-0 p-2">Почта: "{{email}}"</p>
                </div>
            </div>

            <div class="mobile-menu d-md-none d-lg-none d-xl-none d-sm-block">
                <ul class="p-md-0">
                    <li v-for="item in nav" v-if="item.auth === 'both' || item.auth === auth.login" @click="isShowUser = false">
                        <router-link
                            class="d-block p-2"
                            :to="item.path"
                            active-class="active"
                        >{{item.title}}
                        </router-link>
                    </li>

                    <li v-for="item in navHandler" v-if="item.auth === auth.login && item.is_handler === auth.is_handler" @click="isShowUser = false">
                        <router-link
                            class="d-block p-2"
                            :to="item.path"
                            active-class="active"
                        >{{item.title}}
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import Auth from "../../Auth";

    export default {

        name: "Navigator",

        computed: {

            ...mapState(['nav', 'navHandler']),

            auth() {
                return this.$store.state.Auth;
            },

            username(){
                return this.$store.state.Auth.name;
            },

            letter() {
                if (this.auth.login)
                    return this.$store.state.Auth.name.slice(0, 1);
                else
                    return 'X';
            },

            email() {
                return this.$store.state.Auth.email;
            },

            department() {
                return this.$store.state.Auth.department;
            }
        },

        data() {

            return {

                errors : [],

                isShowUser: false
            }
        },

        methods:{

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

                    if(error.response.data.message === 'Unauthenticated.') {
                        Auth.logout();
                        Auth.init();
                        this.$router.push('/');
                    }
                });
            }

        },

        created() {
            document.body.addEventListener('keydown', e => {
                if(e.keyCode === 13 || e.keyCode === 27) {
                    this.isShowUser = false;
                }
            })
        }

    }
</script>

<style scoped>

    .navigator{
        user-select: none;
    }

    .user-panel{
        position: absolute;
        font-size: 10px;
        background-color: #2c7fd7;
        color: white;
    }

    .mobile-menu{
        background-color: #4c87c5;
        font-size: 14px;
    }

    .mobile-menu li {
        display: block;
        border-top: solid 1px #0067ce;
    }
    .mobile-menu li:first-child{
        border: none;
    }
    .mobile-menu a{
        text-decoration: none;
    }

    .user{
        display: inline-block;
        color: white;
        border-radius: 20px;
        border: solid 1px #868686;
        width: 34px;
        text-align: center;
        height: 34px;
        cursor: pointer;
        font-size: 18px;
        font-style: italic;
        vertical-align: sub;
        padding: 3px;
        margin-top: 2px;
        transition: 0.2s;
    }

    .user:hover{
        border: solid 1px white;
    }

    .user:active {
        color: #2c7fd7;
    }
    a {
        color: white;
        transition:0.4s;
    }
    a:hover {
        color: #c2c2c2;
    }
    ul{
        padding: 0;
        list-style: none;
        margin-bottom: 0;
    }
    li{
        display: inline-block;
    }
</style>
