<template>
    <div class="content">
        <h2 class="text-center">Обработка заявки № {{ticket.number}}</h2>
        <hr>

        <div class="about-ticket">

            <div class="mb-4">
                <button class="btn btn-secondary" @click="$router.go(-1)" title="Назад"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p>Создана: {{formatDateTime(ticket.created_at)}}</p>
                </div>
                <div class="offset-md-3 col-md-3 text-md-right" v-if="ticket.status_ticket">
                    <p>Статус: <span
                        :class="{'status-completed' : ticket.status_ticket.status === 'completed',
                             'status-untouched' : ticket.status_ticket.status === 'untouched',
                             'status-performed' : ticket.status_ticket.status === 'performed',
                             'status-rejected' : ticket.status_ticket.status === 'rejected'}">{{ticket.status_ticket.title}}</span>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p>Категория: {{ticket.category}}</p>
                </div>
                <div class="offset-md-1 col-md-5 text-md-right">
                    <p v-if="ticket.user">Автор: {{ticket.user.name}}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p>Название: {{ticket.title}}</p>
                </div>
                <div class="offset-md-1 col-md-5 text-md-right">
                    <p>{{ticket.department}}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p><b>Контакты: </b></p>
                    <p v-if="ticket.user">Телефон: {{ticket.user.phone}}</p>
                    <p v-if="ticket.user">Почта: <a :href="'mailto:'+ticket.user.email">{{ticket.user.email}}</a></p>
                </div>
                <div class="offset-md-1 col-md-5 text-md-right">
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

            <p><b>Изменить статус:</b></p>
            <div class="mt-2 mb-5 text-center">
                <div class="mb-2 offset-1 col-10 offset-md-3 col-md-6 offset-lg-4 col-lg-4">
                    <select
                        class="form-control"
                        v-model="status"
                        @change="selectDescriptionStatus()"
                        :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')">
                            <option v-for="status in statusTicket" :value="status.id">{{status.title}}</option>
                    </select>
                </div>

                <div class="p-2 pl-4 pr-4 mt-3 mb-3 text-left description-status">
                    <p class="mb-2 mb-md-3">{{statusDescription}}</p>
                    <p class="description-status-warning"><small><i><b>Перед изменением статуса</b> (Завершено, Отклонено) убедитесь, что комментарии с автором были завершены.</i></small></p>
                </div>

                <button class="btn btn-outline-dark"
                        :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                        @click="handlerTicket()">Обработать заявку
                </button>
            </div>

            <hr>

            <p><b>Комментарии:</b></p>
            <div class="comments mb-2 pl-md-5 pr-md-5">
                <div v-for="comment in comments" :key="comment.ID">

                    <div class="comment comment-handler" v-if="comment.is_handler">
                        <p class="m-1 date-created">{{formatDateTime(comment.created_at)}}</p>
                        <p class="mb-0 text-muted h6 pt-2 pb-2 comment-about">Обработчик: <span
                            class="username">({{comment.user.name}})</span></p>
                        <p>{{comment.description}}</p>
                    </div>

                    <div class="comment comment-user" v-else @mouseenter.self.stop="resetCommentNew(comment)">
                        <p class="m-1 date-created">{{formatDateTime(comment.created_at)}} <span
                            class="badge badge-primary ml-2" v-if="comment.is_new">New</span></p>
                        <p class="mb-0 text-muted h6 pt-2 pb-2 comment-about">Автор: <span
                            class="username">({{comment.user.name}})</span></p>
                        <p>{{comment.description}}</p>
                    </div>

                </div>
            </div>

            <p><b>Написать комментарий:</b></p>
            <div class="create-comment">
                <textarea class="form-control" rows="5"
                          v-model="description"
                          :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                          :class="{'error-input': $v.description.$error}"
                          @change="$v.description.$touch()"
                ></textarea>
                <small id="descriptionHelp" class="form-text text-muted" :class="{'is-error': $v.description.$error}">Текст
                    комментария.
                    <span v-if="!$v.description.required" class="error-text"
                          :class="{'error-show': !$v.description.required}">Поле пустое</span>
                    <span v-if="!$v.description.minLength" class="error-text"
                          :class="{'error-show': !$v.description.minLength}">Минимум 6 символов</span>
                </small>
                <div class="text-center m-2">
                    <button class="btn btn-outline-dark"
                            @click="createComment"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                    >Написать
                    </button>
                </div>
            </div>

            <indicatorAutoUpdate :is_enable="intervalUpdateComments"/>
        </div>
    </div>
</template>

<script>
    import {mapMutations, mapState} from 'vuex';
    import IndicatorAutoUpdate from "../../IndicatorAutoUpdate";
    import Screenshots from "../../Screenshots";
    import {required, minLength} from 'vuelidate/lib/validators';

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

                statusDescription: '',

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
            user() {
                return this.$store.state.Auth;
            }
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode', 'playSound']),

            getStatusTicket() {
                const url = `/api/handler/tickets/status/gets`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.statusTicket = response.data.statusTicket;
                        this.status = response.data.statusTicket[0].id;
                        this.selectDescriptionStatus();
                    } else {
                        this.playSound('/sounds/_alert.mp3');
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
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
                        this.playSound('/sounds/_alert.mp3');
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getComments() {

                const url = `/api/handler/comments/ticket/${this.ticket_id}/get`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.comments = response.data.comments;

                        if (response.data.comments.some(item => item.is_new && item.is_handler === 0)) {
                            this.playSound('/sounds/_adjutant.mp3');
                        }

                    } else {
                        this.playSound('/sounds/_alert.mp3');
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            getTicketState() {
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
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                })
            },

            handlerTicket() {

                const newStatus  = this.status;
                const currStatus = this.ticket.status_id;

                if (newStatus === currStatus) {
                    this.playSound('/sounds/_alert.mp3');
                    this.setTextMessenger({text: 'Заявка в текущем статусе', status: 'error'});
                    return;
                }

                if (!confirm(`Вы собираетесь изменить статус заявки. Выполнить это действие?`))
                    return;

                const url = `/api/handler/tickets/change-status`;

                this.changeLoaderBarMode(true);

                axios.put(url, {ticket_id: this.ticket_id, status: newStatus}).then(response => {
                    this.changeLoaderBarMode(false);
                    if (response.data.success) {
                        this.playSound('/sounds/_upgrade_complete.mp3');
                        this.ticket = response.data.ticket;
                        this.getComments();
                        this.setTextMessenger({text: 'Статус заявки изменен.', status: 'success'});
                    } else {
                        this.playSound('/sounds/_alert.mp3');
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
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
                        this.playSound('/sounds/_alert.mp3');
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            createComment() {

                this.$v.$touch();

                if (this.$v.$invalid) {
                    this.playSound('/sounds/_alert.mp3');
                    this.setTextMessenger({text: 'Заполните поле "комментарий"', status: 'error'});
                    return false;
                }

                const url = `/api/handler/comments/create`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.post(url, {ticket_id: this.ticket_id, description: this.description.trim()}).then(response => {
                    this.changeLoaderBarMode(false);

                    if (response.data.success) {

                        this.playSound('/sounds/_notify.mp3');

                        this.description = '';

                        this.setTextMessenger({text: 'Комментарий отправлен.', status: 'success'});

                        this.getComment(response.data.comment_id);

                        this.scrollToTopComments();

                        this.$v.$reset();
                    } else {
                        this.playSound('/sounds/_alert.mp3');
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
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

            startUpdateComments() {
                if (this.intervalUpdateComments === null) {
                    this.intervalUpdateComments = setInterval(() => {
                        this.getComments();
                        this.getTicketState();
                    }, this.autoUpdateDataOnPage);
                }
            },

            stopUpdateComments() {
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

                    if (response.data.success) {

                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
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
                    this.playSound('/sounds/_alert.mp3');
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            resetCommentNew(comment) {

                if (comment.is_new !== 1)
                    return;

                const url = `/api/handler/comments/id/${comment.id}/reset-new`;

                // this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.put(url).then(response => {

                    // this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        comment.is_new = 0;
                    }

                }).catch(error => {
                    this.playSound('/sounds/_alert.mp3');
                    // this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            selectDescriptionStatus() {
                this.statusDescription = this.statusTicket[this.status -1].description;
            }
        },

        created() {
            this.getStatusTicket();
            this.getTicket();
            this.getComments();
            this.getScreenshots();

            this.startUpdateComments();
        },

        beforeDestroy() {
            this.stopUpdateComments();
        }
    }
</script>

<style scoped>

    .description {
        background: #eff2f5;
        line-height: 30px;
        min-height: 120px;
        word-break: break-all;
    }

    .description-status{
        border: solid 1px #e48e8e;
        border-radius: 2px;
        line-height: 25px;
        text-indent: 1.5em; /* Отступ первой строки */
        text-align: justify; /* Выравнивание по ширине */
    }

    .description-status-warning{

        margin: 5px 0;
        background: #eaeaea;
    }

    p {

    }
</style>
