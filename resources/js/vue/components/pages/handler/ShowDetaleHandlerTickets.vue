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

                <Screenshots :screenshots="screenshots"></Screenshots>

                <div class="mt-2 text-center">
                    <div class="col-md-2 ml-auto mr-auto mb-2">
                        <select class="form-control"
                                v-model="status"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                        >
                            <option v-for="status in statusTicket" :value="status.id">{{status.title}}</option>
                        </select>
<!--                        <select class="form-control"-->
<!--                                v-model="status"-->
<!--                                :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"-->
<!--                        >-->
<!--                            <option v-for="status in statusTicket" :value="status.id">{{status.title}}</option>-->
<!--                        </select>-->
                    </div>
                    <button class="btn btn-outline-dark"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                            @click="handlerTicket()">Обработать заявку</button>
<!--                    <button class="btn btn-outline-dark"-->
<!--                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"-->
<!--                            @click="handlerTicket()">Обработать заявку</button>-->
                </div>

                <hr>

                <div class="comments mb-2">
                    <p><b>Комментарий:</b></p>
                    <div v-for="comment in comments">
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
                              :class="{'error-input': $v.description.$error}"
                              @change="$v.description.$touch()"
                    ></textarea>
                    <small id="descriptionHelp" class="form-text text-muted" :class="{'is-error': $v.description.$error}">Текст комментария.
                        <span v-if="!$v.description.required"  class="error-text" :class="{'error-show': !$v.description.required}">Поле пустое</span>
                        <span v-if="!$v.description.minLength" class="error-text" :class="{'error-show': !$v.description.minLength}">Минимум 6 символов</span>
                    </small>
                    <div class="text-center m-2">
                        <button class="btn btn-outline-dark"
                                @click="createComment"
                                :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                        >Написать</button>
                    </div>
                </div>

                <indicatorAutoUpdate :is_enable="intervalUpdateComments"/>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapMutations, mapState} from 'vuex';
    import IndicatorAutoUpdate from "../../IndicatorAutoUpdate";
    import Screenshots from "../../Screenshots";
    import { required, minLength } from 'vuelidate/lib/validators'

    export default {

        name: "ShowDetaleHandlerTickets",

        components: {
            IndicatorAutoUpdate,
            Screenshots
        },

        data() {
          return {
              status: null,

              description: null,

              ticket: {},

              comments: [],

              screenshots: [],

              errors: null,

              statusTicket: [],

              intervalUpdateComments: null
          }
        },

        validations: {
            description: {
                required,
                minLength: minLength(6)
            }
        },

        computed: {

            ...mapState(['autoUpdateDataOnPage']),

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
                const url = `/api/handler/tickets/status/gets`;

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

                const url = `/api/handler/tickets/id/${this.ticket_id}/get`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.status = response.data.ticket.status_ticket.id;
                        this.ticket = response.data.ticket;

                        this.resetNew(this.ticket);

                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getComments(){

                const url = `/api/handler/comments/ticket/${this.ticket_id}/get`;

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

            getTicketState(){
                const url = `/api/handler/tickets/id/${this.ticket_id}/state`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        const {status_ticket} = response.data.ticket;

                        this.ticket.status_ticket = status_ticket;
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                })
            },

            handlerTicket() {
                if (!confirm(`Вы собираетесь изменить статус заявки. Выполнить это действие?`))
                    return;

                const url = `/api/handler/tickets/change-status`;

                this.changeLoaderBarMode(true);

                axios.put(url, {ticket_id: this.ticket_id, status: this.status}).then(response => {
                    this.changeLoaderBarMode(false);
                    if (response.data.success) {
                        this.ticket = response.data.ticket;
                        this.getComments();
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

                const url = `/api/handler/comments/${comment_id}/get`;

                this.changeLoaderBarMode(true);

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

            createComment() {

                this.$v.$touch();

                if (this.$v.$invalid) {
                    this.setTextMessenger({text: 'Заполните поле "комментарий"', status: 'error'});
                    return false;
                }

                const url = `/api/handler/comments/create`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.post(url, {ticket_id: this.ticket_id, description: this.description.trim()}).then(response => {
                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        this.description = '';

                        this.setTextMessenger({text: 'Комментарий отправлен.', status: 'success'});

                        this.getComment(response.data.comment_id);

                        this.scrollToTopComments();

                        this.$v.$reset();
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

            startUpdateComments(){
                if (this.intervalUpdateComments === null) {
                    this.intervalUpdateComments = setInterval(() => {
                        this.getComments();
                        this.getTicketState();
                    }, this.autoUpdateDataOnPage);
                }
            },

            stopUpdateComments(){
                if (this.intervalUpdateComments) {
                    clearInterval(this.intervalUpdateComments);
                    this.intervalUpdateComments = null;
                }
            },

            resetNew(ticket) {

                if (ticket.is_new !== 1)
                    return;

                const url = `/api/handler/tickets/id/${ticket.id}/reset-new`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.put(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success){

                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getScreenshots() {

                const url = `/api/screenshots/id/${this.ticket_id}/get`;

                axios.get(url).then(response => {

                    if (response.data.success) {
                        this.screenshots = response.data.screens;
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            }
        },

        created() {
            this.getStatusTicket();
            this.getTicket();
            this.getComments();
            this.getScreenshots();

            this.startUpdateComments()
        },

        beforeDestroy() {
            this.stopUpdateComments();
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
