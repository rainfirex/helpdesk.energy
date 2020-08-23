<template>
    <div class="monitor-ticket">
        <div class="content">
            <h2 class="text-center">Мониторинг заявки</h2>
            <hr>
            <div class="col-12 offset-md-2 col-md-8">
                <div class="form-group mb-4">
                    <label for="numberTicket">Номер заявки</label>
                    <input type="text" class="form-control" id="numberTicket" aria-describedby="numberTicketHelp" v-model="numberTicket">
                    <small id="numberTicketHelp" class="form-text text-muted">Введите номер заявки чтобы узнать статус.</small>
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
                <p><b>Последние комментарии..</b></p>
                <div class="comment-your p-2 m-2" v-for="comment in ticket.comments">
                    <p class="m-1 comment-created">{{formatDateTime(comment.created_at)}}</p>
                    <p class="m-0"><span class="nick">Вы: </span> {{comment.description}}</p>
                </div>
                <div class="mt-2 text-center" v-if="user.login === true">
                    <router-link class="btn btn-outline-info" :to="{name: 'detale-ticket', params: {id : ticket.id}}" tag="button">Подробно</router-link>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';

    export default {

        name: "MonitorTicket",

        data() {
            return {

                numberTicket: '',

                ticket: null,

                errors: null
            }
        },

        computed: {
            user(){
                return this.$store.state.Auth;
            }
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            getTicket(){

                this.ticket = null;

                const url = `/api/user/tickets/check-status/number/${this.numberTicket}`;

                this.changeLoaderBarMode(true);

                // axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        this.ticket = response.data.ticket;
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                });
            },

            formatDate(dateTime) {
                return new Date(dateTime).toLocaleDateString() + ' в ' + new Date(dateTime).toLocaleTimeString();
            },

            formatDateTime(dateTime) {
                return new Date(dateTime).toLocaleDateString() + ' в ' + new Date(dateTime).toLocaleTimeString();
            }
        }
    }
</script>

<style scoped>
    .monitor-ticket{
    }
    .comment-your{
        border-radius: 3px;
        background: #d8ecdd;
        min-width: 400px;
    }
</style>
