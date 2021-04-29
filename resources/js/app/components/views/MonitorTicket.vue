<template>
    <div class="monitor-ticket">
        <div class="content">
            <h2 class="text-center">Мониторинг заявки</h2>
            <hr>
            <div class="col-12 offset-md-2 col-md-8">
                <div class="form-group mb-4">
                    <label for="numberTicket">Номер заявки</label>
                    <input type="text" class="form-control" id="numberTicket" aria-describedby="numberTicketHelp"
                           placeholder="например: 000017"
                           v-model="numberTicket"
                           :class="{'error-input': $v.numberTicket.$error}"
                           @change="$v.numberTicket.$touch()"
                    >
                    <small id="numberTicketHelp" class="form-text text-muted"
                           :class="{'is-error': $v.numberTicket.$error}">Введите номер заявки чтобы узнать статус.
                        <span v-if="!$v.numberTicket.required" class="error-text"
                              :class="{'error-show': !$v.numberTicket.required}">Поле пустое</span>
                        <span v-if="!$v.numberTicket.minLength" class="error-text"
                              :class="{'error-show': !$v.numberTicket.minLength}">Меньше 6ти символов</span>
                        <span v-if="!$v.numberTicket.alpha" class="error-text"
                              :class="{'error-show': !$v.numberTicket.alpha}">Неверный формат заявки</span>
                    </small>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-outline-dark" @click="getTicket">Запросить</button>
                </div>
            </div>

            <div v-if="ticket" class="offset-1 col-md-10 mt-4">
                <p><b>Название заявки: </b>"{{ticket.title}}"</p>
                <p>Состояние заявки: {{ticket.status_ticket.title}}</p>
                <p v-if="ticket.performer_user"><b>Исполнитель:</b> {{ticket.performer_user.name}}</p>
                <p v-if="ticket.performer_user">Контактный номер: {{ticket.performer_user.phone}}</p>
                <p>Отдел заявителя: {{ticket.department}}</p>
                <p><b>Последние комментарии:</b></p>
                <div class="comment-your p-2 m-2" v-for="comment in ticket.comments">
                    <p class="m-1 comment-created">{{formatDateTime(comment.created_at)}}</p>
                    <p class="m-0"><span class="nick">Вы: </span> {{comment.description}}</p>
                </div>
                <div class="mt-2 text-center" v-if="getUser.login === true">
                    <router-link class="btn btn-outline-info" :to="{name: 'detale-ticket', params: {id : ticket.id}}"
                                 tag="button">Подробно
                    </router-link>
                </div>
            </div>
            <div v-if="errors" class="offset-1 col-md-10 mt-4">
                <p class="text-center"><b>{{ errors }}</b></p>
            </div>
        </div>
    </div>
</template>

<script>
    import Sound from "../../assets/js/Sound";
    import {mapActions, mapGetters} from 'vuex';
    import {required, minLength, helpers} from 'vuelidate/lib/validators'
    // const alpha = helpers.regex('alpha', /^(ticket.)[a-z0-9.]+/i);
    const alpha = helpers.regex('alpha', /^[0-9.]+/i);
    export default {
        name: "MonitorTicket",
        data() {
            return {
                numberTicket: '',
                ticket: null,
                errors: null
            }
        },
        validations: {
            numberTicket: {
                required,
                minLength: minLength(6),
                alpha
            }
        },
        computed: {
            ...mapGetters(['getUser'])
        },
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            getTicket() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Ошибка ввода номера заявки', status: 'error'});
                    return false;
                }

                this.ticket = null;
                this.setLoaderBar(true);
                axios.get(`api/monitor/number-ticket/${this.numberTicket}`)
                    .then(response => {
                        this.ticket = response.data.ticket;
                        if (!this.ticket) {
                            this.errors = 'Заявка не найдена.';
                        }
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
            formatDate(dateTime) {
                return new Date(dateTime).toLocaleDateString() + ' в ' + new Date(dateTime).toLocaleTimeString();
            },
            formatDateTime(dateTime) {
                return new Date(dateTime).toLocaleDateString() + ' в ' + new Date(dateTime).toLocaleTimeString();
            },
            listenerKeyDown(e) {
                if (e.code === 'Enter' && e.key === 'Enter') {
                    this.getTicket();
                }
                if (e.code === 'Escape' && e.key === 'Escape') {
                    this.numberTicket = '';
                }
            }
        },
        mounted() {
            document.body.addEventListener('keydown', this.listenerKeyDown);
        },
        beforeDestroy() {
            document.body.removeEventListener('keydown', this.listenerKeyDown);
        }
    }
</script>

<style lang="scss" scoped>
    .comment-your {
        border-radius: 3px;
        background: #d8ecdd;
        min-width: 400px;
    }
</style>
