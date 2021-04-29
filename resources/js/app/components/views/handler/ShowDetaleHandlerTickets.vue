<template>
    <div class="content">
        <h2 class="text-center">Обработка заявки № {{ticket.number}}</h2>
        <hr>

        <div class="about-ticket">
            <div class="mb-4">
                <button class="btn btn-secondary" @click="$router.go(-1)" title="Назад"><i class="fa fa-arrow-left"
                                                                                           aria-hidden="true"></i>
                </button>
                <button class="btn btn-outline-secondary"
                        :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                        @click="isShowAssign = !isShowAssign">Назначить на исполнение</button>
                <button class="btn btn-outline-secondary"
                        :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                        @click="isShowChangeStatus = !isShowChangeStatus">Обработать заявку
                </button>
            </div>

            <AssignHandler v-if="isShowAssign" :ticket="ticket" @close="isShowAssign = false"/>

            <ChangeStatus v-if="isShowChangeStatus" :ticket="ticket" @updateTicket="updateTicket"
                          @close="isShowChangeStatus = false"/>

            <div class="row">
                <div class="col-12">
                    <p><b>Назначил исполнителя:</b>
                        <span v-if="ticket.master_user">{{ ticket.master_user.name }}</span>
                    </p>
                    <p><b>Исполнитель заявки:</b>
                        <span v-if="ticket.performer_user">{{ ticket.performer_user.name }}</span>
                    </p>
                </div>
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

            <div class="row mb-3">
                <div class="col-12">
                    <p><b>Содержание:</b></p>
                    <div class="description p-3">
                        {{ticket.description}}
                    </div>
                </div>
            </div>

            <Docs v-if="docs.length > 0" :docs="docs"/>

            <Screenshots v-if="screenshots.length > 0" :screenshots="screenshots" />

            <hr>

            <p><b>Написать комментарий:</b></p>
            <div class="create-comment">
                <textarea class="form-control" rows="5"
                          placeholder="Текст комментария."
                          v-model="description"
                          :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed' || ticket.status_ticket.status === 'rejected')"
                          :class="{'error-input': $v.description.$error}"
                          @change="$v.description.$touch()"
                ></textarea>
                <small id="descriptionHelp" class="form-text text-muted" :class="{'is-error': $v.description.$error}">
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

            <!--Комментарии-->
            <div v-if="comments.length > 0">
                <p><b>Комментарии:</b></p>
                <div class="comments mb-4 pl-md-5 pr-md-5" @scroll="scrolls">
                    <div v-for="(comment, index) in comments" :key="comment.ID">
                        <div class="comment-box">
                            <!--Обработчик-->
                            <div class="comment comment-handler" :id="comment.id" v-if="comment.is_handler" :ref="'comment_'+index">
                                <div class="mb-4">
                                    <p class="m-1 date-created">{{formatDateTime(comment.created_at)}}</p>
                                    <span class="mb-0 text-muted h6 pt-2 pb-2 comment-about ml-4">Обработчик: <span class="username">({{comment.user.name}})</span></span>
                                </div>
                                <p>{{comment.description}}</p>

                                <div class="p-0" v-if="comment.is_read">
                                    <p class="pl-1 pr-1 small m-0 alert-warning">Сообщение было прочитано автором заявки</p>
                                </div>

                                <!--Зрители-->
                                <div class="viewers-container mt-1" v-if="comment.comment_viewer.length > 0">
                                <span class="viewers p-1" :class="'v-'+randomStyleViewer()"
                                      :title="'Просмотренно: ' + v.user.name" v-for="v in comment.comment_viewer"
                                      :key="comment.comment_viewer.id">
                                    {{ v.user.name }}
                                </span>
                                </div>
                            </div>
                            <!--Автор-->
                            <div class="comment comment-user" :id="comment.id" v-else @mouseenter.self.stop="addCommentViewer(comment); resetCommentNew(comment)" :ref="'comment_'+index">
                                <div class="mb-4">
                                    <p class="m-1 date-created">{{formatDateTime(comment.created_at)}} <span class="badge badge-primary ml-2" v-if="comment.is_new">New</span></p>
                                    <span class="mb-0 text-muted h6 pt-2 pb-2 comment-about  ml-4">Автор:&nbsp;<span class="username">({{comment.user.name}})</span></span>
                                </div>
                                <p>{{comment.description}}</p>

                                <!--Зрители-->
                                <div class="viewers-container mt-1" v-if="comment.comment_viewer.length > 0">
                                <span class="viewers p-1" :class="'v-'+randomStyleViewer()"
                                      :title="'Просмотренно: ' + v.user.name" v-for="v in comment.comment_viewer"
                                      :key="comment.comment_viewer.id">
                                    {{ v.user.name }}
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <indicatorAutoUpdate :is_enable="intervalUpdateComments"/>
        </div>
    </div>
</template>

<script>
    import {required, minLength} from 'vuelidate/lib/validators';
    import {mapActions, mapGetters} from 'vuex';
    import Sound from "../../../assets/js/Sound";
    import IndicatorAutoUpdate from "../../IndicatorAutoUpdate";
    import Screenshots from "../../Screenshots";
    import AssignHandler from "../../AssignHandler";
    import ChangeStatus from "../../ChangeStatus";
    import Docs from "../../Docs";
    export default {
        name: "ShowDetaleHandlerTickets",
        components: {
            IndicatorAutoUpdate,
            Screenshots,
            AssignHandler,
            ChangeStatus,
            Docs
        },
        data() {
            return {
                description: null,
                ticket: {},
                comments: [],
                screenshots: [],
                docs: [],
                errors: null,
                intervalUpdateComments: null,
                isShowAssign: false,
                isShowChangeStatus: false,
                testId: 0
            }
        },
        validations: {
            description: {
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
            updateTicket(ticket) {
                this.ticket = ticket;
                this.getComments();
            },
            getTicket() {
                this.setLoaderBar(true);
                axios.get(`/api/handler/tickets/id/${this.ticket_id}/get-ticket`).then(response => {
                    this.setLoaderBar(false);

                    if (response.data.success) {
                        this.status = response.data.ticket.status_ticket.id;
                        this.ticket = response.data.ticket;
                        this.resetTicketNew(this.ticket);
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
                this.setLoaderBar(true);
                axios.get(`/api/handler/comments/ticket-id/${this.ticket_id}/get-comments`).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        this.comments = response.data.comments;
                        if (response.data.comments.some(item => item.is_new && item.is_handler === 0)) {
                            Sound.playSound('/sounds/_adjutant.mp3');
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
            getComment(comment_id) {
                this.setLoaderBar(true);
                axios.get(`/api/handler/comments/comment-id/${ comment_id }/get-comment`).then(response => {
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
            createComment() {
                this.$v.$touch();

                if (this.$v.$invalid) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Заполните поле "комментарий"', status: 'error'});
                    return false;
                }

                this.setLoaderBar(true);
                axios.post(`/api/handler/comments/create-comment`, {ticket_id: this.ticket_id, description: this.description.trim()}).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        Sound.playSound('/sounds/_notify.mp3');
                        this.description = '';
                        this.setMessenger({text: 'Комментарий отправлен.', status: 'success'});
                        this.getComment(response.data.comment_id);
                        this.scrollToTopComments();
                        this.$v.$reset();
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
            // Сбросить флаг новой заявки
            resetTicketNew(ticket) {
                if (ticket.is_new !== 1)
                    return;

                this.setLoaderBar(true);
                axios.put(`/api/handler/tickets/id/${ ticket.id }/reset-ticket-new`).then(() => {
                    this.setLoaderBar(false);
                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },
            // Проверка статуса заявки
            getTicketStatus() {
                this.setLoaderBar(true);
                axios.get(`/api/handler/tickets/id/${this.ticket_id}/get-ticket-status`).then(response => {
                    this.setLoaderBar(false);
                    if (response.data.success) {
                        const {status_ticket} = response.data.ticket;
                        this.ticket.status_ticket = status_ticket;
                    }
                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setLoaderBar(false);
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                })
            },
            // Получить скриншеты
            getScreenshots() {
                const url = `/api/screenshots/ticket-id/${ this.ticket_id }/get-screenshots`;
                this.setLoaderBar(true);
                axios.get(url)
                    .then(response => {
                        this.screenshots = response.data.screenshots;
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                    });
            },
            // Получить документы
            getDocs() {
                const url = `/api/docs/ticket-id/${ this.ticket_id }/get-docs`;
                this.setLoaderBar(true);
                axios.get(url)
                    .then(response => {
                        this.docs = response.data.docs;
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                    });
            },
            // Сбросить флаг нового комментария
            resetCommentNew(comment) {
                if (comment.is_new !== 1)
                    return;
                axios.put(`/api/handler/comments/id/${comment.id}/reset-new`).then(response => {
                    if (response.data.success) {
                        comment.is_new = 0;
                    }
                }).catch(error => {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.errors = error.response.data.message;
                    this.setMessenger({text: this.errors, status: 'error'});
                });
            },
            addCommentViewer(comment) {
                // Просмотренный комментарий не обновляем
                for (let i = 0; i < comment.comment_viewer.length; i++) {
                    const user = comment.comment_viewer[i].user;
                    if ((user.id === +this.getUser.user_id) && (user.name === this.getUser.name)) {
                        return;
                    }
                }

                axios.post(`/api/handler/comments/comment-id/${ comment.id }/create-comment-viewer`)
                    .then(response => {
                        if(response.data.result) {
                            comment.comment_viewer.push(response.data.commentViewer);
                        }
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
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
                        this.getTicketStatus();
                    }, this.getAutoUpdater);
                }
            },
            stopUpdateComments() {
                if (this.intervalUpdateComments) {
                    clearInterval(this.intervalUpdateComments);
                    this.intervalUpdateComments = null;
                }
            },
            randomStyleViewer() {
                return Math.round(1 - 0.5 + Math.random() * (10 - 1 + 1));
            },
            scrolls() {
                let visibleElement;
                for (const name in this.$refs){
                    const el = this.$refs[name][0];

                        const result = this.isVisibleEl(el);
                        if (result) {
                            visibleElement = el;

                            if (this.testId === visibleElement.id) return;
                        }
                }
                if (visibleElement === undefined) return;
                else this.testId = visibleElement.id;

                if (visibleElement) {
                    const htmlId = visibleElement.getAttribute('id');
                    const comment = this.comments.filter(item => item.id === +htmlId)[0];
                    this.addCommentViewer(comment);
                    this.resetCommentNew(comment);
                }
            },
            //полностью ли элемент виден в текущем окне просмотра
            isVisibleEl(el){
                const rect = el.getBoundingClientRect();
                // Ширина окна
                const widthWindow = window.innerWidth;
                // Ширина документа
                const widthDoc = document.documentElement.clientWidth;
                // Высота окна
                const heightWindow = window.innerHeight;
                // Высота документа
                const heightDoc = document.documentElement.clientHeight;

                const width = widthWindow || widthDoc;
                const height = heightWindow || heightDoc;

                return  (rect.top > 0 && rect.left > 0 && rect.right <= width && rect.bottom <= height)
            }
        },
        created() {
            this.getTicket();
            this.getComments();
            this.getScreenshots();
            this.getDocs();
            this.startUpdateComments();

            document.addEventListener('scroll', this.scrolls);
        },
        beforeDestroy() {
            this.stopUpdateComments();
            document.removeEventListener('scroll', this.scrolls)
        }
    }
</script>

<style lang="scss" scoped>
    .description {
        background: #eff2f5;
        line-height: 30px;
        min-height: 120px;
        word-break: break-all;
    }
</style>
