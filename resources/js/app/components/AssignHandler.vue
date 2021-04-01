<template>
    <transition name="fade">
        <div class="assign-handler" @click="close">
            <div class="mt-2 mb-5 text-center assign-handler-form" @click.stop="">
                <p><b>Назначить исполнителя заявки:</b></p>
                <!-- Список -->
                <div class="mb-2 offset-1 col-10 offset-md-3 col-md-6 offset-lg-4 col-lg-4">
                    <select
                            class="form-control"
                            v-model="handlerId"
                            :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                    >
                        <option v-for="handler in handlers" :value="handler.id">{{ handler.name}}</option>
                    </select>
                </div>
                <!-- Кнопка -->
                <button class="btn btn-outline-dark"
                        :disabled="ticket.status_ticket && (ticket.status_ticket.status === 'completed')"
                        @click="assignHandler">Назначить</button>

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
        name: "AssignHandler",
        data(){
            return{
                handlerId: '',
                handlers: []
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
            getHandlers(){
                this.setLoaderBar(true);
                axios.get('/api/handler/handlers')
                    .then(response => {
                        this.handlers = response.data.handlers;
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
            assignHandler(){
                this.setLoaderBar(true);
                const ticketId = this.ticket.id;
                axios.put(`/api/handler/handlers/assign/handler-${ this.handlerId }/ticket-${ ticketId }`)
                    .then(response => {
                        if (response.data.success){
                            this.ticket.performer_user = response.data.performer_user;
                            this.ticket.master_user = response.data.master_user;
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
            close() {
                this.$emit('close');
            }
        },
        created() {
            this.getHandlers();
        }
    }
</script>

<style lang="scss" scoped>
    .assign-handler{
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
        .assign-handler-form{
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
        }
    }
</style>