<template>
    <div class="content">
        <h2 class="text-center">Обработка заявок</h2>
        <hr>
        <div class="row">
            <div class="offset-1 col-10 text-center mb-3 container-count">
                <button class="btn btn-success" @click="setTypeTicket('all')">Все <span class="badge badge-light">{{typeCount.all}}</span>
                </button>
                <button class="btn btn-primary" @click="setTypeTicket('new')">Новые <span class="badge badge-light">{{typeCount.new}}</span>
                </button>
                <button class="btn btn-secondary" @click="setTypeTicket('completed')">Завершенные <span
                    class="badge badge-light">{{typeCount.completed}}</span></button>
                <button class="btn btn-secondary" @click="setTypeTicket('untouched')">Не начатые <span
                    class="badge badge-light">{{typeCount.untouched}}</span></button>
                <button class="btn btn-secondary" @click="setTypeTicket('rejected')">Отклоненные <span
                    class="badge badge-light">{{typeCount.rejected}}</span></button>
                <button class="btn btn-secondary" @click="setTypeTicket('performed')">Выполняются <span
                    class="badge badge-light">{{typeCount.performed}}</span></button>
                <button class="btn btn-warning" @click="setTypeTicket('performer')">Назначенные <span
                        class="badge badge-light">{{typeCount.performer}}</span></button>

            </div>
        </div>

        <div class="row mt-4 mb-4">
            <Pagination :countPage="countPage" :currentPage="currentPage" @getTickets="getTickets($event)"/>

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
                        <p v-if="ticket.is_new_handler_comment && ticket.is_new_handler_comment.length > 0">Новых комментариев: <span class="badge badge-primary">{{ticket.is_new_handler_comment.length}}</span></p>
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

        <div class="col-12 offset-md-1 col-md-10 mb-5 p-0"
             v-show="typeCount.completed || typeCount.untouched || typeCount.rejected || typeCount.performed"
        >
            <h3 class="text-center">Заявки</h3>
            <Piechart
                :size="{width:400, height:400}"
                :data_obj="[
                    {title:'Завершенные', value:typeCount.completed, color: '#28a745'},
                    {title:'Не начатые', value:typeCount.untouched, color: '#efc934'},
                    {title:'Отклоненные', value:typeCount.rejected, color: '#ff150e'},
                    {title: 'Выполняются', value:typeCount.performed, color: '#91f6ff'}

                ]"
                :colors="['#efc934', '#0069d9', '#28a745', '#ff150e']"
                :doughnutHoleSize="0.6"
            ></Piechart>
        </div>

        <indicatorAutoUpdate :is_enable="intervalUpdateTicket"/>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';
    import indicatorAutoUpdate from '../../IndicatorAutoUpdate';
    import Piechart from "../../Piechart";
    import Pagination from "../../Pagination";
    export default {
        name: "HandlerTickets",
        components: {
            indicatorAutoUpdate,
            Pagination,
            Piechart
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
                    performed: 0,
                    rejected: 0,
                    untouched: 0,
                    performer: 0
                },
                typeTicket: 'all'
            }
        },
        computed: mapGetters(['getAutoUpdater', 'getUser']),
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            // Получить заявки
            getTickets(numPage) {
                this.currentPage = numPage;
                this.setLoaderBar(true);
                this.stopIntervalUpdateAllTickets();
                axios.get(`/api/handler/tickets/page/${ numPage }/type/${ this.typeTicket }/get-tickets`)
                    .then(response => {
                        this.setLoaderBar(false);
                        if (response.data.success) {
                            this.tickets = response.data.tickets;
                            this.startIntervalUpdateAllTicket();
                        } else {
                            this.setMessenger({text: response.data.message, status: 'error'});
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                    });
            },
            // Получить количество заявок по типам
            getTypeTickets() {
                this.setLoaderBar(true);
                axios.get(`/api/handler/tickets/get-type-tickets`)
                    .then(response => {
                        this.setLoaderBar(false);
                        if (response.data.success) {
                            this.typeCount = response.data.typeCount;
                        } else {
                            this.setMessenger({text: response.data.message, status: 'error'});
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                    });
            },
            // Получить кол-во страниц определенного типа
            getCountPage() {
                this.setLoaderBar(true);
                axios.get(`/api/handler/tickets/type-ticket/${ this.typeTicket }/pages`)
                    .then(response => {
                        this.setLoaderBar(false);
                        if (response.data.success) {
                            this.countPage = response.data.count;
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);

                    });
            },
            // Поиск заявок
            findTickets() {
                if (this.findText.length < 5) {
                    this.setMessenger({text: 'Для поиска необходимо ввести 5 символов.', status: 'error'});
                    return false;
                }

                this.stopIntervalUpdateAllTickets();
                this.setLoaderBar(true);
                axios.get(`/api/handler/tickets/ticket-title/${this.findText}/find`)
                    .then(response => {
                        this.setLoaderBar(false);
                        if (response.data.success) {
                            this.tickets = response.data.tickets;
                            this.countPage = 0;
                            this.paginationGenerated();
                        } else {
                            this.setMessenger({text: response.data.message, status: 'error'});
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                    });
            },
            setTypeTicket(type) {
                this.typeTicket = type;
                this.getCountPage();
                this.getTickets(1);
            },
            openDetale(id) {
                this.$router.push({name: 'handler-detale-ticket', params: {id: id}});
            },
            startIntervalUpdateAllTicket() {
                if (this.intervalUpdateTicket === null) {
                    this.intervalUpdateTicket = setInterval(() => {
                        this.getTickets(this.currentPage);
                        this.getTypeTickets();
                    }, this.getAutoUpdater);
                }
            },
            stopIntervalUpdateAllTickets() {
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
                    this.getTickets(this.currentPage);
                }
            }
        },
        created() {
            this.getCountPage();
            this.getTickets(1);
            this.getTypeTickets();
        },
        beforeDestroy() {
            this.stopIntervalUpdateAllTickets();
        },
        beforeRouteEnter (to, from, next) {
            if (localStorage.getItem('is_handler') === 'true') {
                next();
            } else {
                next(false);
                window.location.href = "/";
            }
        }
    }
</script>

<style scoped>
    .container-count {
        line-height: 50px;
    }

    .container-table {
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

    .ticket-number {
        font-size: 14px;
        margin: 0 10px;
        color: #696869;
    }

    .ticket-title {
        font-style: italic;
        font-weight: 600;
        color: #696869;
    }

    .ticket-email {
        font-size: 14px;
    }

    .ticket-email > a {
        color: #0792eb;
    }

    .ticket-date-time {
        font-size: 14px;
        color: #696869;
    }

    .ticket-user {
        font-style: italic;
        font-size: 14px;
        color: #696869;
    }

    .ticket-department {
        font-size: 14px;
        color: #696869;
    }
</style>
