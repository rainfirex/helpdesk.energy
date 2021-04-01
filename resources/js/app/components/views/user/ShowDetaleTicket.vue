<template>
    <div class="content">
        <h2 class="text-center">Заявка № {{ticket.number}}</h2>
        <hr>

        <div class="mb-4">
            <button class="btn btn-secondary" @click="$router.go(-1)" title="Назад"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
        </div>

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
                             'status-rejected' : ticket.status_ticket.status === 'rejected'}">{{ticket.status_ticket.title}}</span>
                    </p>
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
                    <p>Не назначен или неизвестно</p>
                </div>
            </div>

            <p><b>Содержание:</b></p>
            <div class="description p-3">
                {{ticket.description}}
            </div>

            <Screenshots :screenshots="screenshots"></Screenshots>
        </div>

        <hr>

        <details open>
            <summary class="mb-4"><b>Комментарии:</b></summary>
            <div class="comments mb-2 pl-md-5 pr-md-5">

                <div v-for="(comment, index) in comments" :key="comment.id" >

                    <div class="comment comment-handler"
                         @mouseenter.self.stop="resetCommentNew(comment)"
                         v-if="comment.is_handler">
                        <p class="m-1 date-created">
                            <span>{{formatDateTime(comment.created_at)}}</span>
                            <span class="badge badge-primary ml-2" v-if="comment.is_new"> New</span>
                        </p>
                        <p class="mb-0 text-muted h6 pt-2 pb-2 comment-about">
                            <span>Обработчик:</span>
                            <span class="username">{{comment.user.name}}:</span>
                        </p>
                        <p>{{comment.description}}</p>
                    </div>

                    <div class="comment comment-user" v-else>
                        <p class="m-1 date-created">{{formatDateTime(comment.created_at)}}</p>
                        <p class="mb-0 text-muted h6 pt-2 pb-2 comment-about">Автор:</p>
                        <p>{{comment.description}}</p>
                    </div>

                </div>
            </div>
        </details>

        <hr>

        <details>
            <summary class="mb-4"><b>Написать комментарий:</b></summary>
            <div class="create-comment ml-md-3 mr-md-3">
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
        </details>

        <hr>

        <details>
            <summary class="mb-4"><b>Завершить заявку</b></summary>
            <div class="completed-ticket ml-md-3 mr-md-3">
                    <textarea class="form-control" rows="5"
                              v-model="descriptionComplete"
                              :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                              :class="{'error-input': $v.descriptionComplete.$error}"
                              @change="$v.descriptionComplete.$touch()"
                    ></textarea>
                <small id="descriptionCompleteHelp" class="form-text text-muted"
                       :class="{'is-error': $v.descriptionComplete.$error}">Причина закрытия.
                    <span v-if="!$v.descriptionComplete.required" class="error-text"
                          :class="{'error-show': !$v.descriptionComplete.required}">Поле пустое</span>
                    <span v-if="!$v.descriptionComplete.minLength" class="error-text"
                          :class="{'error-show': !$v.descriptionComplete.minLength}">Минимум 6 символов</span>
                </small>
                <div class="text-center m-2">
                    <button class="btn btn-outline-dark"
                            @click="completedTicket"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                    >Завершить
                    </button>
                </div>
            </div>
        </details>

        <IndicatorAutoUpdate class="mt-3" :is_enable="intervalUpdateComments"/>
    </div>
</template>

<script>
    import { required, minLength } from 'vuelidate/lib/validators'
    import { mapActions, mapGetters } from 'vuex';
    import Sound from "../../../assets/js/Sound";
    import IndicatorAutoUpdate from "../../IndicatorAutoUpdate";
    import Screenshots from "../../Screenshots";
    import Notification from "../../../assets/js/Notification";
    export default {
        name: "ShowDetaleTicket",
        components: {
            IndicatorAutoUpdate,
            Screenshots
        },

        data() {
          return {
              ticket: {},

              screenshots: [],

              description: null,

              descriptionComplete: null,

              errors: null,

              comments: [],

              intervalUpdateComments: null
          }
        },

        validations: {

            description: {
                required,
                minLength: minLength(6)
            },

            descriptionComplete: {
                required,
                minLength: minLength(6)
            }
        },

        computed: {
            ...mapGetters(['getAutoUpdater', 'getUser']),
            ticket_id() {
                return this.$route.params.id;
            }
        },

        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),

            getTicket() {
                const url = `/api/user/tickets/id/${this.ticket_id}/get`;

                this.setLoaderBar(true);

                axios.get(url).then(response => {
                    this.setLoaderBar(false);

                    if (response.data.success) {
                        this.ticket = response.data.ticket;
                    } else {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.setMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            createComment() {
                this.$v.description.$touch();

                if (this.$v.description.$invalid) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Заполните поле "комментарий"', status: 'error'});
                    return false;
                }

                const url = `/api/user/comments/create`;

                this.setLoaderBar(true);

                axios.post(url, {ticket_id: this.ticket_id, description: this.description.trim()}).then(response => {
                    this.setLoaderBar(false);

                    if (response.data.success) {

                        Sound.playSound('/sounds/_notify.mp3');

                        this.description = '';

                        this.setMessenger({text: 'Комментарий отправлен.', status: 'success'});

                        this.getComment(response.data.comment_id);

                        this.scrollToTopComments();

                        this.$v.description.$reset();

                    } else {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.setMessenger({text: response.data.message, status: 'error'});
                    }
                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            getComment(comment_id) {
                const url = `/api/user/comments/${comment_id}/get`;

                this.setLoaderBar(true);

                axios.get(url).then(response => {

                    this.setLoaderBar(false);

                    if (response.data.success) {
                        this.comments.unshift(response.data.comment);
                    } else {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.setMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            getComments() {
                const url = `/api/user/comments/ticket/${this.ticket_id}/get`;

                this.setLoaderBar(true);

                axios.get(url).then(response => {

                    this.setLoaderBar(false);

                    if (response.data.success) {
                        this.comments = response.data.comments;

                        if (response.data.comments.some(item => item.is_new && item.is_handler === 1)) {
                            Sound.playSound('/sounds/_adjutant.mp3');
                            Notification.play('Заявка', 'У вас непрочитанный комментарий.');
                        }


                    } else {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.setMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            // Состояние заявки
            getTicketState() {
                const url = `/api/user/tickets/id/${this.ticket_id}/state`;

                this.setLoaderBar(true);

                axios.get(url).then(response => {

                    this.setLoaderBar(false);

                    if (response.data.success) {

                        const {status_ticket, performer_user} = response.data.ticket;

                        this.ticket.status_ticket = status_ticket;
                        this.ticket.performer_user = performer_user;
                    } else {
                        this.setMessenger({text: 'Не получилось получить состояние заявки', status: 'error'});
                        Sound.playSound('/sounds/_alert.mp3');
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                })
            },

            completedTicket(){
                this.$v.descriptionComplete.$touch();

                if (this.$v.descriptionComplete.$invalid) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Укажите причину завершения заявки', status: 'error'});
                    return false;
                }

                const url = `/api/user/tickets/completed/id/${this.ticket_id}/set`;

                this.setLoaderBar(true);

                axios.put(url, {description : this.descriptionComplete}).then(response => {

                    this.setLoaderBar(false);

                    if (response.data.success) {

                        Sound.playSound('/sounds/_notify.mp3');

                        this.descriptionComplete = '';

                        this.setMessenger({text: 'Заявка завершена.', status: 'success'});

                        this.$v.descriptionComplete.$reset();

                        this.$router.push({name: 'user-tickets'});

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

            formatDateTime(dateTime) {
                return new Date(dateTime).toLocaleDateString() + ' в ' + new Date(dateTime).toLocaleTimeString();
            },

            startUpdateComments() {
                if (this.intervalUpdateComments === null) {
                    this.intervalUpdateComments = setInterval(() => {
                        this.getComments();
                        this.getTicketState();
                    }, this.getAutoUpdater);
                }
            },

            stopUpdateComments(){
                if (this.intervalUpdateComments) {
                    clearInterval(this.intervalUpdateComments);
                    this.intervalUpdateComments = null;
                }
            },

            scrollToTopComments() {
                const container = this.$el.querySelector(".comments");
                container.scrollTop = 0; //container.scrollHeight
            },

            getScreenshots() {
                const url = `/api/screenshots/id/${this.ticket_id}/get`;

                axios.get(url).then(response => {

                    if (response.data.success) {
                        this.screenshots = response.data.screens;
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },

            resetCommentNew(comment) {
                if (comment.is_new !== 1)
                    return;

                const url = `/api/user/comments/id/${comment.id}/reset-new`;

                // this.setLoaderBar(true);

                axios.put(url).then(response => {

                    // this.setLoaderBar(false);

                    if (response.data.success) {
                        comment.is_new = 0;
                    }

                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    // this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            }
        },

        created() {
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

    summary:focus {
        outline: none;
    }

    .description{
        background: #eff2f5;
        line-height: 30px;
        min-height: 120px;
        word-break: break-all;
    }

</style>
