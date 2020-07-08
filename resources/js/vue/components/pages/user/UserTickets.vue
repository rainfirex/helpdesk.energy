<template>
    <div class="user-tickets">
        <div class="content">
            <h2 class="text-center">Список заявок</h2>
            <hr>
            <div>
                <details class="offset-md-1 col-md-10 mb-1">
                    <summary>Закрытые заявки</summary>
                    <ListTicket v-bind:tickets="ticketsCompleted"></ListTicket>
                </details>

                <details class="offset-md-1 col-md-10" open>
                    <summary>Открытые заявки</summary>
                    <ListTicket v-bind:tickets="tickets"></ListTicket>
                </details>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex';
    import ListTicket from "../../ListTicket";

    export default {
        name: "UserTickets",
        components: {ListTicket},
        data() {
            return {
                tickets: [],

                ticketsCompleted:[],

                errors: null,

                intervalUpdateListTicket: null
            }
        },

        computed: {

            user(){
                return this.$store.state.Auth;
            },
        },

        methods: {

            ...mapMutations(['setTextMessenger', 'changeLoaderBarMode']),

            getTickets(status) {
                const url = `/api/get-tickets/${status}`;

                this.changeLoaderBarMode(true);

                axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.user.api_token;
                axios.get(url).then(response => {

                    this.changeLoaderBarMode(false);

                    if (response.data.success) {
                        switch (status) {
                            case 1:
                                this.tickets = response.data.tickets;
                                break;
                            case 2:
                                this.ticketsCompleted = response.data.tickets;
                                break;
                        }
                    } else {
                        this.setTextMessenger({text: response.data.message, status: 'error'});
                    }

                }).catch(error => {
                    this.changeLoaderBarMode(false);
                    this.errors = error.response.data.message;
                    this.setTextMessenger({text: this.errors, status: 'error'});
                })
            },

            formatDate(datetime) {
                return  new Date(datetime).toLocaleDateString();
            },

            updateListTicket(){
                this.intervalUpdateListTicket = setInterval(() => {
                    this.getTickets(1);
                    this.getTickets(2);
                }, 60000);
            }
        },

        created() {
            this.getTickets(1);
            this.getTickets(2);

            this.updateListTicket();
        },

        beforeDestroy() {
            if (this.intervalUpdateListTicket)
                clearInterval(this.intervalUpdateListTicket);
        }
    }
</script>

<style scoped>
    details {
        border-radius: 2px;
        border: solid 1px #d9d9d9;
    }
    summary:focus {
        outline: none;
    }
</style>
