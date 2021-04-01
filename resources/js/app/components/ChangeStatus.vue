<template>
    <transition name="fade">
        <div class="change-status" @click="close">
            <div class="mt-2 mb-5 text-center change-status-form" @click.stop="">
                <p><b>Изменить статус:</b></p>
                <!-- Список -->
                <div class="mb-2 offset-1 col-10 offset-md-3 col-md-6 offset-lg-4 col-lg-4">
                    <select
                            class="form-control"
                            v-model="newStatus"
                            @change="setDescription()"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')">
                        <option v-for="st in statusTicket" :value="st.id">{{st.title}}</option>
                    </select>
                </div>
                <!-- Описание -->
                <div class="p-2 pl-4 pr-4 mt-3 mb-3 text-left description-status">
                    <p class="mb-2 mb-md-3">{{ description }}</p>
                    <p class="description-status-warning"><small><i><b>Перед изменением статуса</b> (Завершено, Отклонено) убедитесь, что комментарии с автором были завершены.</i></small></p>
                </div>
                <!-- Кнопка -->
                <button class="btn btn-outline-dark"
                        :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                        @click="handlerTicket()">Обработать заявку
                </button>

                <div class="button-close">
                    <button class="btn btn-danger" @click="close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import Sound from "../assets/js/Sound";
    export default {
        name: "ChangeStatus",
        data(){
            return{
                newStatus: 0,
                statusTicket: [],
                description: '',
            }
        },
        props:{
            ticket:{
                type: Object,
                required: true
            }
        },
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            getStatusTicket() {
                const url = `/api/handler/tickets/status/gets`;

                this.setLoaderBar(true);

                axios.get(url)
                    .then(response => {
                        if (response.data.success) {
                            this.statusTicket = response.data.statusTicket;
                            this.newStatus = this.ticket.status_id;//Выбрать тек. статус
                            this.setDescription();
                        } else {
                            Sound.playSound('/sounds/_alert.mp3');
                            this.setMessenger({text: response.data.message, status: 'error'});
                        }
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.setLoaderBar(false);
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                    });
            },
            setDescription() {
                this.description = this.statusTicket[this.newStatus -1].description;
            },
            handlerTicket() {
                const currStatus = this.ticket.status_id;

                if (this.newStatus === currStatus) {
                    Sound.playSound('/sounds/_alert.mp3');
                    this.setMessenger({text: 'Заявка в текущем статусе', status: 'error'});
                    return;
                }

                if (!confirm(`Вы собираетесь изменить статус заявки. Выполнить это действие?`))
                    return;

                const url = `/api/handler/tickets/change-status`;

                this.setLoaderBar(true);

                axios.put(url, {ticket_id: this.ticket.id, status: this.newStatus})
                    .then(response => {
                        if (response.data.success) {
                            Sound.playSound('/sounds/_upgrade_complete.mp3');
                            this.$emit('updateTicket', response.data.ticket);
                            this.setMessenger({text: 'Статус заявки изменен.', status: 'success'});
                        } else {
                            Sound.playSound('/sounds/_alert.mp3');
                            this.setMessenger({text: response.data.message, status: 'error'});
                        }
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.setLoaderBar(false);
                        this.close();
                    });
            },
            close(){
                this.$emit('close');
            }
        },
        created() {
            this.getStatusTicket();
        }
    }
</script>

<style lang="scss" scoped>
    .change-status{
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 999;

        color: white;
        background: #151616db;

        display: flex;
        justify-content: center;
        align-items: center;

        user-select: none;

        .change-status-form{
            width: 80%;
            max-width: 1000px;
            margin: 0 auto;
            background: #eaeaea;
            padding: 24px;
            color: black;
            border-radius: 2px;
            position: relative;

            .button-close{
                position: absolute;
                top: 10px;
                right: 10px;
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
        }
    }
</style>