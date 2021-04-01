<template>
    <div>
        <div class="row m-0 p-1 navigator">
            <div class="col-12 col-sm-8 col-md-7 d-none d-sm-none d-md-block">
                <ul class="p-md-0">
                    <li v-for="item in getMainNavs">
                        <router-link
                            class="nav-link"
                            :to="item.path"
                            active-class="active"
                            v-if="item.auth === 'both' || item.auth === getUser.login"
                        ><i class="fa" :class="item.ico" aria-hidden="true">&nbsp;&nbsp;{{item.title}}</i>
                        </router-link>
                    </li>

                    <li v-for="item in getAuthNavs" v-if="item.auth === false && item.auth === getUser.login"
                        @click="isShowUser = false">
                        <router-link
                            class="d-block p-2"
                            :to="item.path"
                            active-class="active"
                        ><i class="fa" :class="item.ico" aria-hidden="true">&nbsp;{{item.title}}</i>
                        </router-link>
                    </li>

                    <li v-for="item in getHandlerNavs">
                        <router-link
                            class="nav-link"
                            :to="item.path"
                            active-class="active"
                            v-if="item.auth === getUser.login && item.is_handler === getUser.is_handler"
                        ><i class="fa" :class="item.ico" aria-hidden="true">&nbsp;&nbsp;{{item.title}}</i>
                        </router-link>
                    </li>
                </ul>
            </div>

            <div class="col-12 text-md-right col-md-5 row">
                <div class="col-2 offset-1 offset-md-7 col-md-2" @click.stop="">
                    <span class="user" @click="openUserInfo" v-if="letter" :title="getUser.name">{{letter}}</span>
                    <span class="user" @click="openUserInfo" v-else title="Пользователь не идентифицирован"><i
                        class="fa fa-male"></i></span>
                </div>
                <div class="col-2 offset-7 offset-md-1 col-md-2 align-self-center" v-if="getUser.login">
                    <button class="btn btn-light btn-sm p-1" @click="logout()">Выход</button>
                </div>
            </div>
        </div>

        <div class="col-12 offset-md-9 col-md-3 offset-lg-9 user-panel p-0" v-if="isShowUser" @click.stop="">
            <div v-if="isShowUser && getUser.name" class="p-1">
                <div>
                    <p class="mb-0 p-2">Вы авторизованы как "{{ getUser.name }}"</p>
                    <p class="mb-0 p-2">{{ getUser.department }}</p>
                </div>
                <div>
                    <p class="mb-0 p-2">Почта: "{{ getUser.email }}"</p>
                </div>
            </div>

            <div class="p-1">
                <p class="mb-0 p-2">
                    <label class="p-0 m-0">Без звука <input type="checkbox" class="ml-5" :checked="getIsSoundMute" @change="changeSoundMode"></label>
                </p>
            </div>

            <div class="mobile-menu d-md-none d-lg-none d-xl-none d-sm-block">
                <ul class="p-md-0">
                    <li v-for="item in getMainNavs" v-if="item.auth === 'both' || item.auth === getUser.login"
                        @click="isShowUser = false">
                        <router-link
                            class="d-block p-2"
                            :to="item.path"
                            active-class="active"
                        ><i class="fa" :class="item.ico" aria-hidden="true">&nbsp;{{item.title}}</i>
                        </router-link>
                    </li>

                    <li v-for="item in getAuthNavs" v-if="item.auth === false && item.auth === getUser.login"
                        @click="isShowUser = false">
                        <router-link
                            class="d-block p-2"
                            :to="item.path"
                            active-class="active"
                        ><i class="fa" :class="item.ico" aria-hidden="true">&nbsp;{{item.title}}</i>
                        </router-link>
                    </li>

                    <li v-for="item in getHandlerNavs"
                        v-if="item.auth === getUser.login && item.is_handler === getUser.is_handler"
                        @click="isShowUser = false">
                        <router-link
                            class="d-block p-2"
                            :to="item.path"
                            active-class="active"
                        ><i class="fa mr-1" :class="item.ico" aria-hidden="true">&nbsp;{{item.title}}</i>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';
    import Sound from '../../assets/js/Sound';
    import User from '../../assets/js/User';
    export default {

        name: "Navigator",

        computed: {
            ...mapGetters(['getMainNavs', 'getAuthNavs', 'getHandlerNavs', 'getIsSoundMute', 'getUser']),

            letter() {
                if (this.getUser.login)
                    return this.getUser.name.slice(0, 1);
                else
                    return 'X';
            }
        },
        data() {
            return {
                errors: [],
                isShowUser: false
            }
        },

        methods: {
            ...mapActions(['setMessenger', 'setIsSoundMute']),

            logout() {
                const url = `/api/auth/logout`;
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.getUser.api_token;
                axios.post(url).then(response => {
                    if (response.data.success) {
                        Sound.playSound('/sounds/_beep.mp3');
                        this.setMessenger({text: 'Вы вышли из системы', status: 'success'});

                        if (this.$router.history.current.path !== '/') {
                            this.$router.push('/');
                        }
                    } else {
                        this.setMessenger({text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.errors = error.response.data.errors;
                    this.setMessenger({text: this.errors, status: 'error'});

                    if (error.response.data.message === 'Unauthenticated.') {
                        this.$router.push('/');
                    }

                }).finally(() => {
                    User.logout();
                    User.init();
                });
            },

            openUserInfo() {
                this.isShowUser = !this.isShowUser;
                if (this.isShowUser)
                    Sound.playSound('/sounds/_bloop.mp3');
            },

            changeSoundMode() {
                Sound.setMode(!this.getIsSoundMute);
            }
        },

        watch: {
            $route() {
                Sound.playSound('/sounds/_bloop.mp3');
            }
        },

        created() {
            document.body.addEventListener('keydown', e => {
                if (e.key === 'Escape' || e.code === 'Escape') {
                    this.isShowUser = false;
                }
            });

            document.body.addEventListener('click', () => {
                this.isShowUser = false;
            });
        }
    }
</script>

<style lang="scss" scoped>

    .navigator {

        user-select: none;

        /*text-transform: uppercase;*/

        .user {
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

            &:hover {
                border: solid 1px white;
            }

            &:active {
                color: #2c7fd7;
            }
        }
    }

    .user-panel {
        position: absolute;
        font-size: 12px;
        background-color: #5888bb;
        color: white;

        .mobile-menu {
            background-color: #4c87c5;
            font-size: x-large;

            li {
                display: block;
                border-top: solid 1px #0067ce;
            }

            li:first-child {
                border-top: none;
            }

            a {
                text-decoration: none;
            }
        }
    }

    ul {
        padding: 0;
        list-style: none;
        margin-bottom: 0;

        li {
            display: inline-block;

            a {
                color: white;
                transition: 0.4s;

                &:hover {
                    color: #c2c2c2;
                }
            }
        }
    }
</style>
