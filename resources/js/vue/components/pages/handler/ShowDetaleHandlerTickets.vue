<template>
    <div class="show-ticket">
        <div class="content">
            <h2 class="text-center">Заявка № {{ticket.number}}</h2>
            <hr>
            <div class="about-ticket">
                <div class="row">
                    <div class="col-md-6">
                        <p>Создана: {{formatDateTime(ticket.created_at)}}</p>
                    </div>
                    <div class="offset-md-4 col-md-2 text-right" v-if="ticket.status_ticket">
                        <p>Статус: <span
                            :class="{'status-completed' : ticket.status_ticket.status === 'completed',
                             'status-untouched' : ticket.status_ticket.status === 'untouched',
                             'status-performed' : ticket.status_ticket.status === 'performed',
                             'status-rejected' : ticket.status_ticket.status === 'rejected'}">{{ticket.status_ticket.title}}</span></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p>Категория: {{ticket.category}}</p>
                    </div>
                    <div class="offset-md-1 col-md-5 text-right">
                        <p v-if="ticket.user">Автор: {{ticket.user.name}}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p>Название: {{ticket.title}}</p>
                    </div>
                    <div class="offset-md-1 col-md-5 text-right">
                        <p>{{ticket.department}}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p><b>Контакты: </b></p>
                        <p v-if="ticket.user">Телефон: {{ticket.user.phone}}</p>
                        <p v-if="ticket.user">Почта: <a :href="'mailto:'+ticket.user.email">{{ticket.user.email}}</a></p>
                    </div>
                    <div class="offset-md-1 col-md-5 text-right">
                        <p v-if="ticket.user">{{ticket.user.title}}</p>
                    </div>
                </div>

                <div>
                    <p><b>Содержание:</b></p>
                    <div class="description p-3">
                        {{ticket.description}}
                    </div>
                </div>

                <div class="mt-2 text-center">
                    <div class="col-md-2 ml-auto mr-auto mb-2">
                        <select class="form-control"
                                v-model="status"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                        >
                            <option v-for="status in statusTicket" :value="status.id">{{status.title}}</option>
                        </select>
                    </div>
                    <button class="btn btn-outline-dark"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                            @click="handlerTicket()">Обработать заявку</button>
                </div>

                <hr>

                <div class="comments mb-2">
                    <p><b>Комментарий:</b></p>
                    <div v-for="comment in ticket.comments">
                        <div class="text-left">

                            <div class="comment-adm p-2 pl-4 pr-4 m-1 mr-4" v-if="comment.user_id === +user.user_id">
                                <p class="m-1 comment-created">{{formatDateTime(comment.created_at)}}</p>
                                <p class="m-0"><span class="nick">Вы: </span> {{comment.description}}</p>
                            </div>

                            <div class="comment-user p-2 pl-4 pl-4 m-1 ml-4" v-else>
                                <p class="m-1 comment-created">{{formatDateTime(comment.created_at)}}</p>
                                <p class="m-0"><span class="nick">Пользователь: </span> {{comment.description}}</p>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="">
                    <p><b>Написать комментарий:</b></p>
                    <textarea class="form-control" rows="5"
                              v-model="description"
                              :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                    ></textarea>
                    <div class="text-center m-2">
                        <button class="btn btn-outline-dark"
                                @click="createComment"
                                :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                        >Написать</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';

    export default {

        name: "ShowDetaleHandlerTickets",

        data() {
          return {
              status: null,

              description: null,

              ticket: {},

              errors: null,

              statusTicket: []
          }
        },

        computed: {
            ticket_id() {
                return this.$route.params.id;
            },
            user(){
                return this.$store.state.Auth;
            }
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            getStatusTicket() {
                const url = `/api/handler-tickets/get-status-ticket`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.statusTicket = response.data.statusTicket;
                        this.status = response.data.statusTicket[0].id;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getTicket() {
                const url = `/api/handler-tickets/get-ticket/${this.ticket_id}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.status = response.data.ticket.status_ticket.id;
                        this.ticket = response.data.ticket;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            handlerTicket() {
                if (!confirm(`Вы собираетесь изменить статус заявки. Выполнить это действие?`))
                    return;

                const url = `/api/handler-tickets/change-status-ticket`;

                this.changeLoaderBarMode(true);

                axios.put(url, {ticket_id: this.ticket_id, status: this.status}).then(response => {
                    this.changeLoaderBarMode(false);
                    if (response.data.success) {
                        this.ticket = response.data.ticket;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getComment(comment_id) {
                const url = `/api/handler-tickets/comments/get/${comment_id}`;

                this.changeLoaderBarMode(true);

                axios.get(url).then(response => {
                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.ticket.comments.unshift(response.data.comment);
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            createComment() {
                if (this.description === null || this.description === '') return;

                const url = `/api/handler-tickets/comments/create`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.post(url, {ticket_id: this.ticket_id, description: this.description.trim()}).then(response => {
                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        this.description = '';

                        this.setTextMessenger({text: 'Комментарий отправлен.', status: 'success'});

                        this.getComment(response.data.comment_id);

                        this.scrollToTopComments();
                    }
                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            formatDateTime(dateTime) {
                return new Date(dateTime).toLocaleDateString() + ' в ' + new Date(dateTime).toLocaleTimeString();
            },

            scrollToTopComments() {
                const container = this.$el.querySelector(".comments");
                container.scrollTop = 0; //container.scrollHeight
            },
        },

        created() {
            this.getStatusTicket();
            this.getTicket();
        }
    }
</script>

<style scoped>

    .description{
        background: #eff2f5;
        line-height: 30px;
        min-height: 120px;
        /*overflow: auto;*/
        word-break: break-all;
    }

    .status-performed{
        color: #62ac6c;
        font-size: 19px;
        font-style: italic;
    }

    .status-completed{
        color: #d68b46;
        font-size: 19px;
        font-style: italic;
    }

    .status-untouched{
        color: #5e8fe7;
        font-style: italic;
        font-size: 19px;
    }

    .status-rejected{
        color: #ff150e;
        font-style: italic;
        font-size: 19px;
    }

    .form {
        width: 90%;
        margin: 15px auto;
        background: #fafafa;
        padding: 25px;
        border-radius: 2px;
    }

    .comments{
        max-height: 450px;
        overflow: auto;
    }

    .comment-user{
        border: solid 1px #eeeded;
        background: #e3f3fa;
    }

    .comment-adm{
        border: solid 1px #e9f3f6;
        background: #fefefe;
    }

</style>
