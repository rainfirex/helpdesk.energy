<template>
    <div class="user-tickets">
        <div class="content">
            <h2 class="text-center">Список завершенных заявок</h2>
            <hr>

            <div class="navigator">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><router-link class="p-2" :to="pageCreateTicket.path" v-if="pageCreateTicket.auth === 'both' || pageCreateTicket.auth === user.login">{{pageCreateTicket.title}}</router-link></li>
                    <li class="breadcrumb-item"><router-link class="p-2" :to="pageTickets.path" v-if="pageTickets.auth === 'both' || pageTickets.auth === user.login">Активные заявки</router-link></li>
                </ul>
            </div>

            <Pagination :countPage="countPage" :currentPage="currentPage" @getTickets="getTickets($event)"/>

            <div class="container-list">
                <ListTicket v-bind:tickets="tickets"></ListTicket>
            </div>

        </div>
    </div>
</template>

<script>
    import {mapMutations, mapState} from 'vuex';
    import ListTicket from "../../ListTicket";
    import Pagination from "../../Pagination";

    export default {

        name: "UserTicketCompleted",

        components: {ListTicket, Pagination},

        data() {
            return {
                tickets: [],

                countPage: 0,

                currentPage: 0
            }
        },

        computed: {

            ...mapState(['nav', 'navTicket', 'autoUpdateDataOnPage']),

            user(){
                return this.$store.state.Auth;
            },

            pageCreateTicket() {
                return this.navTicket[0];
            },

            pageTickets() {
                return this.nav[2];
            }

        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            getTickets(numPage) {

                this.currentPage = numPage;

                const url = `/api/user/tickets/completed/page/${numPage}/get`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                })
            },

            getCountPage() {

                const url = `/api/user/tickets/completed/pages`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.countPage = response.data.count;
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            }

        },

        created() {
            this.getCountPage();
            this.getTickets(1);
        }
    }
</script>

<style scoped>

    .container-list{
        min-height: 500px;
    }

    ul{
        list-style: none;
    }

    li {
        display: inline-block;
    }

</style>
