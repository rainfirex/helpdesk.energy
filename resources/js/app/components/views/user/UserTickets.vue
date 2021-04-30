<template>
    <div class="content">
        <h2 class="text-center">Список активных заявок</h2>
        <hr>
        <div class="navigator">
            <ul class="breadcrumb">
                <li class="breadcrumb-item" v-for="(nav, index) in breadcrumbNav" :key="index">
                    <router-link class="p-2" :to="nav.path" v-if="nav.auth === 'both' || nav.auth === getUser.login">
                        {{ nav.title }}
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
    import { mapGetters, mapActions } from 'vuex';
    import ListTicket from "../../ListTicket";
    import Pagination from "../../Pagination";
    import Sound from "../../../assets/js/Sound";
    export default {
        name: "UserTickets",
        components: { ListTicket, Pagination },
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
            ...mapGetters(['getTicketNavs', 'getAutoUpdater', 'getUser']),
            //Навигация
            breadcrumbNav() {
               return this.getTicketNavs.filter((item, index) => {
                    if (index === 0 || index === 2)
                        return item;
                });
            }
        },
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            getPages(){
                this.setLoaderBar(true);
                axios.get(`/api/user/tickets/pages`).then(response => {
                    this.setLoaderBar(false);

                    if (response.data.success) {
                        this.countPage = response.data.count;
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },
            getTickets(numPage) {
                this.currentPage = numPage;
                this.setLoaderBar(true);

                axios.get(`/api/user/tickets/page/${ numPage }/get-tickets`).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                    } else {
                        this.setMessenger({text: response.data.message, status: 'error'});
                        Sound.playSound('/sounds/_alert.mp3');
                    }
                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                })
            },
            findTickets() {
                if (this.findText.length < 5) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Для поиска необходимо ввести 5 символов.', status: 'error'});
                    return false;
                }
                this.stopUpdateListTicket();
                this.setLoaderBar(true);
                axios.get(`/api/user/tickets/search/${ this.findText }`).then(response => {

                    this.setLoaderBar(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                        this.countPage = 0;
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
            formatDate(datetime) {
                return  new Date(datetime).toLocaleDateString();
            },
            startUpdateListTicket(){
                this.intervalUpdateListTicket = setInterval(() => {
                    this.getTickets(this.currentPage);
                }, this.getAutoUpdater);
            },
            stopUpdateListTicket(){
                if (this.intervalUpdateListTicket) {
                    clearInterval(this.intervalUpdateListTicket);
                    this.intervalUpdateListTicket = null;
                }
            },
            inputFindText(){
                if (this.findText === '') {
                    this.getPages();
                    this.getTickets(this.currentPage);
                    this.startUpdateListTicket();
                }
            }
        },
        created() {
            this.getPages();
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
    .breadcrumb {
        background-color: #f2f5f9;
    }
</style>
