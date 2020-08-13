<template>
    <div class="handler-tickets">
        <div class="content">
            <h2 class="text-center">Обработка заявок</h2>
            <hr>

            <div class="row">
                <div class="offset-1 col-10 text-center mb-3 container-count">
                    <button class="btn btn-success"   @click="setTypeTicket('all')">Все <span class="badge badge-light">{{typeCount.all}}</span></button>
                    <button class="btn btn-primary"   @click="setTypeTicket('new')">Новые <span class="badge badge-light">{{typeCount.new}}</span></button>
                    <button class="btn btn-secondary" @click="setTypeTicket('completed')">Завершенные <span class="badge badge-light">{{typeCount.completed}}</span></button>
                    <button class="btn btn-secondary" @click="setTypeTicket('untouched')">Не начатые <span class="badge badge-light">{{typeCount.untouched}}</span></button>
                    <button class="btn btn-secondary" @click="setTypeTicket('rejected')">Отклоненные <span class="badge badge-light">{{typeCount.rejected}}</span></button>
                    <button class="btn btn-secondary" @click="setTypeTicket('performed')">Выполняются <span class="badge badge-light">{{typeCount.performed}}</span></button>
                </div>
            </div>

            <div class="row mt-4 mb-4">
                <Pagination :countPage="countPage" :currentPage="currentPage" @getTickets="getAllTickets($event)"/>

                <div class="col-12 col-md-7 offset-lg-2 col-lg-5">
                    <div class="row">
                        <div class="col-8">
                            <input type="text" class="form-control" placeholder="Что ищем?"
                                   v-model="findText"
                                   @input="inputFindText"
                                   @keydown.enter="findTickets"
                                   @keydown.esc="findText=''; inputFindText()">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-outline-primary" @click="findTickets">Поиск</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-table table-responsive">
                <table class="table">
                    <tr>
                        <th>Заявка</th>
                        <th>Дата</th>
                        <th class="d-none d-md-block">Категория</th>
                        <th>Обратная связь</th>
                        <th>Автор</th>
                        <th class="d-none d-md-block">Исполнитель</th>
                        <th>Статус</th>
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
                        <td class="d-none d-md-block">{{ticket.category}}</td>
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
                        <td v-if="ticket.performer_user" class="d-none d-md-block">
                            <span class="ticket-user">{{ticket.performer_user.name}}</span>
                            <p class="ticket-email mb-0">
                                <a :href="'mailto:'+ticket.performer_user.email">{{ticket.performer_user.email}}</a>
                            </p>
                        </td>
                        <td v-else></td>
                        <td>
                            <p class="mb-1">{{ticket.status_ticket.title}}</p>
                            <h4 class="mb-0"><span class="badge badge-primary" v-if="ticket.is_new">New</span></h4>

                        </td>
                    </tr>
                </table>
            </div>

            <indicatorAutoUpdate :is_enable="intervalUpdateTicket"/>

        </div>
    </div>
</template>

<script>
    import Pagination from "../../Pagination";
    import {mapMutations, mapState} from 'vuex';

    import indicatorAutoUpdate from '../../IndicatorAutoUpdate';

    export default {
        name: "HandlerTickets",

        components: {
            indicatorAutoUpdate,
            Pagination
        },

        data() {

          return {
              findText: '',

              countPage: 0,

              currentPage: 0,

              tickets: [],

              errors: null,

              intervalUpdateTicket: null,

              typeCount: {
                  all: 0,
                  new: 0,
                  completed: 0,
                  performed: 8,
                  rejected: 3,
                  untouched : 0
              },

              typeTicket: 'all'
          }

        },

        computed: {

            ...mapState(['autoUpdateDataOnPage']),

            user() {
                return this.$store.state.Auth;
            }

        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            setTypeTicket(status){
                this.typeTicket = status;
                this.getCountPage();
                this.getAllTickets(1);
            },

            getAllTickets(numPage) {

                this.currentPage = numPage;

                const url = `/api/handler/tickets/page/${numPage}/type/${this.typeTicket}/get`;

                this.changeLoaderBarMode(true);

                this.stopIntervalUpdateAllTickets();

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;

                        this.startIntervalUpdateAllTicket();
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getCountPage() {
                const url = `/api/handler/tickets/type/${this.typeTicket}/pages`;

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

            startIntervalUpdateAllTicket() {
                if (this.intervalUpdateTicket === null) {
                    this.intervalUpdateTicket = setInterval(() => {
                        this.getAllTickets(this.currentPage);
                        this.countType();
                    }, this.autoUpdateDataOnPage);
                }
            },

            stopIntervalUpdateAllTickets(){
                if (this.intervalUpdateTicket) {
                    clearInterval(this.intervalUpdateTicket);
                    this.intervalUpdateTicket = null;
                }
            },

            formatDate(date) {
                return new Date(date).toLocaleDateString();
            },

            formatTime(time) {
                return new Date(time).toLocaleTimeString();
            },

            inputFindText() {
                if (this.findText === '') {
                    this.startIntervalUpdateAllTicket();
                    this.getCountPage();
                    this.getAllTickets(this.currentPage);
                }
            },

            findTickets() {
                if (this.findText.length < 5) {
                    this.setTextMessenger({text: 'Для поиска необходимо ввести 5 символов.', status: 'error'});
                    return false;
                }

                this.stopIntervalUpdateAllTickets();

                const url = `/api/handler/tickets/find/${this.findText}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.tickets = response.data.tickets;
                        this.countPage = 0;
                        this.paginationGenerated();
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            countType() {

                const url = `/api/handler/tickets/count-type`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {
                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        this.typeCount = response.data.typeCount;

                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
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
            this.getAllTickets(1);
            this.countType();

            // this.startIntervalUpdateAllTicket();
        },

        beforeDestroy() {
            this.stopIntervalUpdateAllTickets();
        }
    }
</script>

<style scoped>

    .container-count{
        line-height: 50px;
        /*display: flex;*/
        /*flex-wrap: wrap;*/
        /*justify-content: space-between;*/
        /*max-width: 70%;*/
        /*margin: 0 auto;*/
    }

    .container-table{
        min-height: 600px;
    }

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
        font-size: 14px;
    }
    .ticket-email > a{
        color: #0792eb;
    }
    .ticket-date-time{
        font-size: 14px;
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
