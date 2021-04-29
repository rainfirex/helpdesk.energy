<template>
    <div class="content">
        <h2 class="text-center">Список завершенных заявок</h2>
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
            ...mapGetters(['getTicketNavs', 'getUser']),
            //Навигация
            breadcrumbNav() {
                return this.getTicketNavs.filter((item, index) => {
                    if (index === 0 || index === 3)
                        return item;
                });
            }
        },
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            getPages() {
                this.setLoaderBar(true);
                axios.get(`/api/user/tickets/completed/pages`).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        this.countPage = response.data.count;
                    }
                }).catch(error => {
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },
            getTickets(numPage) {
                this.currentPage = numPage;
                this.setLoaderBar(true);
                axios.get(`/api/user/tickets/completed/page/${numPage}/get-tickets`).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                    } else {
                        this.setMessenger({text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                })
            }
        },
        created() {
            this.getPages();
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
    .breadcrumb {
        background-color: #f2f5f9;
    }
</style>
