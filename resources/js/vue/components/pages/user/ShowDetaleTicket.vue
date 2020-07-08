<template>
    <div class="show-ticket">
        <div class="content">
            <h2 class="text-center">Заявка № {{ticket.number}}</h2>
            <hr>
            <div class="about-ticket">
                <div class="row">
                    <div class="col-md-5">
                        <p>Создана: {{formatDateTime(ticket.created_at)}}</p>
                    </div>
                    <div class="offset-md-3 col-md-4 " v-if="ticket.status_ticket">

                        <p>Статус: <span
                            :class="{'status-completed' : ticket.status_ticket.status === 'completed',
                             'status-untouched' : ticket.status_ticket.status === 'untouched',
                             'status-performed' : ticket.status_ticket.status === 'performed',
                             'status-rejected' : ticket.status_ticket.status === 'rejected'}">{{ticket.status_ticket.title}}</span></p>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <p>Категория: {{ticket.category}}</p>
                    </div>
                    <div class="offset-md-3 col-md-4">
                        <p><b>Исполнитель:</b></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <p>Название: {{ticket.title}}</p>
                    </div>
                    <div class="offset-md-3 col-md-4" v-if="ticket.performer_user">
                        <p>{{ticket.performer_user.name}}</p>
                        <p>{{ticket.performer_user.title}}</p>
                        <p><b>Контакты:</b></p>
                        <p>Телефон: {{ticket.performer_user.phone}}</p>
                        <p>Почта: <a :href="'mailto:'+ticket.performer_user.email">{{ticket.performer_user.email}}</a></p>
                    </div>
                    <div class="offset-md-3 col-md-4" v-else>
                        <p>Неизвестно</p>
                    </div>
                </div>

                <p><b>Содержание:</b></p>
                <div class="description p-3">
                   {{ticket.description}}
                </div>
            </div>

            <hr>

            <details open>
                <summary class="mb-2"><b>Комментарии:</b></summary>
                <div class="offset-md-2 comments col-md-8">
                    <div v-for="comment in comments">
                        <div class="text-left pr-4">
                            <div class="comment-your p-2 pl-4 pr-4 m-1" v-if="comment.user_id === +user.user_id">
                                <p class="m-1 comment-created">{{formatDateTime(comment.created_at)}}</p>
                                <p class="m-0"><span class="nick">Вы: </span> {{comment.description}}</p>
                            </div>
                        </div>

                        <div class="text-right pl-4">
                            <div class="comment-adm p-2 pl-4 pr-4 m-1">
                                <p class="m-1 comment-created">{{formatDateTime(comment.created_at)}}</p>
                                <p class="m-0"><span class="nick">Исполнитель: </span> {{comment.description}}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </details>

            <hr>

            <details>
                <summary class="mb-2"><b>Написать комментарий:</b></summary>
                <div class="create-comment ml-md-3 mr-md-3">
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
            </details>

            <hr>

            <details>
                <summary class="mb-2"><b>Завершить заявку</b></summary>
                <div class="completed-ticket ml-md-3 mr-md-3">
                    <p>Напишите причину.</p>
                    <textarea class="form-control" rows="5"
                              v-model="descriptionComplete"
                              :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                    ></textarea>
                    <div class="text-center m-2">
                        <button class="btn btn-outline-dark"
                                @click="completedTicket"
                                :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                        >Завершить</button>
                    </div>
                </div>
            </details>

        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';

    export default {
        name: "ShowDetaleTicket",

        data() {
          return {
              ticket: {},

              description: null,

              descriptionComplete: null,

              errors: null,

              comments: [],

              intervalUpdateComments: null
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

            getTicket() {

                const url = `/api/get-ticket/${this.ticket_id}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {
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

            createComment() {
                if (this.description === null || this.description === '') return;

                const url = `/api/create-comment`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.post(url, {ticket_id: this.ticket_id, description: this.description.trim()}).then(response => {
                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        this.description = '';

                        this.setTextMessenger({text: 'Комментарий отправлен.', status: 'success'});

                        this.getComment(response.data.comment_id);

                        this.scrollToTopComments();

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
                const url = `/api/get-comment/${comment_id}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.comments.unshift(response.data.comment);
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getComments() {
                const url = `/api/get-comments/${this.ticket_id}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.comments = response.data.comments;
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            completedTicket(){
                const url = `/api/completed-ticket/${this.ticket_id}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.put(url, {description : this.descriptionComplete}).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        this.descriptionComplete = '';

                        this.setTextMessenger({text: 'Заявка завершена.', status: 'success'});

                        this.$router.push({name: 'user-tickets'});

                   } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
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

            updateComments() {
                this.intervalUpdateComments = setInterval(() => {
                    this.getComments();
                }, 60000);
            },

            scrollToTopComments() {
                const container = this.$el.querySelector(".comments");
                container.scrollTop = 0; //container.scrollHeight
            }
        },

        created() {
            this.getTicket();
            this.getComments();

            this.updateComments();
        },

        beforeDestroy() {
            if (this.intervalUpdateComments) {
                clearInterval(this.intervalUpdateComments);
            }
        }
    }
</script>

<style scoped>

    summary:focus {
        outline: none;
    }

    .about-ticket{

    }

    .description{
        background: #eff2f5;
        line-height: 30px;
        min-height: 120px;
        /*overflow: auto;*/
        word-break: break-all;
    }

    .comments{
        max-height: 450px;
        overflow: auto;
    }

    .comment-your{
        border-radius: 3px;
        background: #d8ecdd;
        /*display: inline-block;*/
        min-width: 400px;
    }

    .comment-adm{
        border-radius: 3px;
        background: #d8e3ec;
        /*display: inline-block;*/
        text-align: left;
        min-width: 450px;
    }

    .nick{
        font-size: 0.9em;
        color: #7b7bd7;
    }

    .comment-created{
        color: #757575;
        font-size: 0.9em;
        border-bottom: solid 1px #838383;
        display: inline-block;
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

</style>
