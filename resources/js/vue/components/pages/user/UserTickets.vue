<template>
    <div class="content">
        <h2 class="text-center">Список активных заявок</h2>
        <hr>

        <div class="navigator">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link class="p-2" :to="pageCreateTicket.path"
                                 v-if="pageCreateTicket.auth === 'both' || pageCreateTicket.auth === user.login">
                        {{pageCreateTicket.title}}
                    </router-link>
                </li>
                <li class="breadcrumb-item">
                    <router-link class="p-2" :to="pageCompletedTicket.path"
                                 v-if="pageCompletedTicket.auth === 'both' || pageCompletedTicket.auth === user.login">
                        {{pageCompletedTicket.title}}
                    </router-link>
                </li>
            </ul>
        </div>

        <div class="search mt-4 mb-4">
            <div class="col-12 offset-md-2 col-md-10 offset-lg-4 col-lg-8">
                <div class="row">
                    <div class="col-10 offset-md-2 col-md-8 offset-lg-4 col-lg-6">
                        <input type="text" class="form-control" placeholder="номер, название или текст содержимого"
                               v-model="findText"
                               @input="inputFindText"
                               @keydown.enter="findTickets"
                               @keydown.esc="findText='';inputFindText()"
                        >
                        <p class="mt-1 mb-0 p-1 search-label"><i>Поиск производится по всем статусам</i></p>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-outline-primary" @click="findTickets">Поиск</button>
                    </div>
                </div>
            </div>
        </div>

        <Pagination :countPage="countPage" :currentPage="currentPage" @getTickets="getTickets($event)"/>

        <div class="container-list">
            <ListTicket v-bind:tickets="tickets"></ListTicket>
        </div>

    </div>
</template>

<script>
    import {mapMutations, mapState} from 'vuex';
    import ListTicket from "../../ListTicket";
    import Pagination from "../../Pagination";

    export default {
        name: "UserTickets",

        components: {ListTicket, Pagination},

        data() {
            return {

                tickets: [],

                errors: null,

                intervalUpdateListTicket: null,

                countPage: 0,

                currentPage: 0,

                findText: ''
            }
        },

        computed: {

            ...mapState(['nav', 'navTicket', 'autoUpdateDataOnPage']),

            user(){
                return this.$store.state.Auth;
            },

            pageCreateTicket(){
                return this.navTicket[0];
            },
            pageCompletedTicket() {
                return this.navTicket[2];
            }
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode', 'playSound']),

            getTickets(numPage) {

                this.currentPage = numPage;

                const url = `/api/user/tickets/page/${numPage}/get`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                        this.playSound('/sounds/_alert.mp3');
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                })
            },

            formatDate(datetime) {
                return  new Date(datetime).toLocaleDateString();
            },

            startUpdateListTicket(){
                this.intervalUpdateListTicket = setInterval(() => {
                    this.getTickets(this.currentPage);
                }, this.autoUpdateDataOnPage);
            },

            stopUpdateListTicket(){
                if (this.intervalUpdateListTicket) {
                    clearInterval(this.intervalUpdateListTicket);
                    this.intervalUpdateListTicket = null;
                }
            },

            getCountPage(){

                const url = `/api/user/tickets/pages`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.countPage = response.data.count;
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            inputFindText(){
                if (this.findText === '') {
                    this.getCountPage();
                    this.getTickets(this.currentPage);
                    this.startUpdateListTicket();
                }
            },

            findTickets() {
                if (this.findText.length < 5) {
                    this.playSound('/sounds/_alert.mp3');
                    this.setTextMessenger({text: 'Для поиска необходимо ввести 5 символов.', status: 'error'});
                    return false;
                }

                this.stopUpdateListTicket();

                const url = `/api/user/tickets/find/${this.findText}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                        this.countPage = 0;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                        this.playSound('/sounds/_alert.mp3');
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            }
        },

        created() {
            this.getCountPage();

            this.getTickets(1);

            this.startUpdateListTicket();
        },

        beforeDestroy() {
            this.stopUpdateListTicket();
        }
    }
</script>

<style lang="scss" scoped>
    .search-label{
        font-size: small;
    }
</style>
