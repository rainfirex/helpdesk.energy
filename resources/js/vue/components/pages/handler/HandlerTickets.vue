<template>
    <div class="handler-tickets">
        <div class="content">
            <h2 class="text-center">Обработка заявок</h2>
            <hr>

            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item"
                        v-for="numPage in countPage"
                        :class="{'active': numPage === currentPage}">
                        <a class="page-link"
                           @click="getAllTickets(numPage)"
                        >{{numPage}}</a>
                    </li>
                </ul>
            </nav>

            <div>

                <table class="table">
                    <tr>
                        <th>Заявка</th>
                        <th>Создана</th>
                        <th>Категория</th>
                        <th>Обратная связь</th>
                        <th>Пользователь</th>
                        <th>Исполнитель</th>
                        <th>Стату выполнения</th>
                    </tr>

                    <tr v-for="ticket in tickets" @dblclick="openDetale(ticket.id)">
                        <td>
                            <span class="ticket-number"><b>№</b> {{ticket.number}}</span>
                            <p class="ticket-title mb-0">{{ticket.title}}</p>
                        </td>
                        <td>
                            <p class="mb-1 ticket-date-time">{{formatDate(ticket.created_at)}}</p>
                            <p class="mb-0 ticket-date-time">{{formatTime(ticket.created_at)}}</p>
                        </td>
                        <td>{{ticket.category}}</td>
                        <td>
                            <span>{{ticket.phone}}</span>
                            <p class="ticket-email mb-0">
                                <a :href="'mailto:'+ticket.user.email">{{ticket.user.email}}</a>
                            </p>
                        </td>
                        <td>
                            <span class="ticket-user">{{ticket.user.name}}</span>
                            <p class="ticket-department mb-0"><b>Отдел:</b> {{ticket.department}}</p>
                        </td>
                        <td v-if="ticket.performer_user">
                            <span class="ticket-user">{{ticket.performer_user.name}}</span>
                            <p class="ticket-email mb-0">
                                <a :href="'mailto:'+ticket.performer_user.email">{{ticket.performer_user.email}}</a>
                            </p>
                        </td>
                        <td v-else></td>
                        <td>{{ticket.status_ticket.title}}</td>
                    </tr>
                </table>

            </div>

        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';

    export default {
        name: "HandlerTickets",

        data() {
          return {

              countPage: 0,

              currentPage: 0,

              tickets: [],

              errors: null,

              intervalUpdateTicket: null
          }
        },

        computed: {

            user() {
                return this.$store.state.Auth;
            }

        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            getAllTickets(numPage) {

                this.currentPage = numPage;
                const url = `/api/handler-tickets/page/${numPage}/get-all-tickets`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getCountPage() {
                const url = `/api/handler-tickets/page/count`;

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
            },

            openDetale(id) {
                this.$router.push({ name: 'handler-detale-ticket', params: { id: id } });
            },

            intervalUpdateAllTicket() {
                this.intervalUpdateTicket = setInterval(() => {
                    this.getAllTickets(this.currentPage);
                }, 60000);
            },

            formatDate(date) {
                return new Date(date).toLocaleDateString();
            },

            formatTime(time) {
                return new Date(time).toLocaleTimeString();
            }
        },

        created() {
            this.getCountPage();
            this.getAllTickets(1);

            this.intervalUpdateAllTicket();
        },

        beforeDestroy() {
            if (this.intervalUpdateTicket)
                clearInterval(this.intervalUpdateTicket);
        }
    }
</script>

<style scoped>

    .content {
        width: 100%;
        max-width: 1910px;
    }

    .table {
        color: #595757;
    }

    .table > tr:hover {
        background-color: #e9eeed;
        cursor: pointer;
        transition: .5s;
    }

    .table th {
        color: #595757;
        text-transform: uppercase;
        font-size: 14px;
        font-family: serif;
    }

    .ticket-number{
        font-size: 14px;
        margin: 0 10px;
        color: #696869;
    }
    .ticket-title{
        font-style: italic;
        font-weight: 600;
        color: #696869;
    }
    .ticket-email {
        font-weight: 600;
        font-size: 14px;
    }
    .ticket-email > a{
        color: #0792eb;
    }
    .ticket-date-time{
        font-size: 14px;
        font-weight: 600;
        color: #696869;
    }
    .ticket-user{
        font-style: italic;
        font-size: 14px;
        color: #696869;
    }
    .ticket-department{
        font-size: 14px;
        color: #696869;
    }
</style>
