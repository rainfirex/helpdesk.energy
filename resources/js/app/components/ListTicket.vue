<template>
    <ul class="p-3 ">
        <li class="item-ticket mb-2"  v-for="ticket in tickets">
            <router-link class="d-block p-4" :to="{name: 'detale-ticket', params: {id : ticket.id}}">
                <div class="row mb-1">
                    <div class="offset-md-1 col-md-7">
                        <p class="m-0">№ {{ticket.number}}</p>
                    </div>
                    <div class="offset-md-1 col-md-3">
                        <span>{{formatDate(ticket.created_at)}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-md-1 col-md-7">
                        <p class="ticket-title mb-1">Название: {{ticket.title}}</p>

                        <p v-if="ticket.is_new_user_comment && ticket.is_new_user_comment.length > 0"><i>Новых комментариев:</i> <span class="badge badge-primary">{{ticket.is_new_user_comment.length}}</span></p>
                    </div>
                    <div class="offset-md-1 col-md-3">
                        <p>Статус:
                            <span :class="{'status-completed' : ticket.status_ticket.status === 'completed',
                             'status-untouched' : ticket.status_ticket.status === 'untouched',
                             'status-performed' : ticket.status_ticket.status === 'performed',
                             'status-rejected' : ticket.status_ticket.status === 'rejected'}"> {{ticket.status_ticket.title}}</span>
                        </p>
                    </div>
                </div>
            </router-link>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "ListTicket",

        props:['tickets'],

        methods: {
            formatDate(datetime) {
                return  new Date(datetime).toLocaleDateString();
            }
        }
    }
</script>

<style lang="scss" scoped>

    $colorLink: #656767;
    $colorTitle: #6d8abf;
    $ticketBackgroundColor: #e6e6e6;

    .item-ticket{
        border: solid 1px #e2e2e2;
        background: $ticketBackgroundColor;
        transition: 0.8s;

        p {
            margin-bottom: 0;
        }

        &:last-child {
            /*border-bottom: none;*/
        }

        a{
            text-decoration: none;
            color: $colorLink;
        }

        &:hover{
            background-color: white;
            border: solid 1px $colorTitle;
        }

        .ticket-title{
            font-weight: 600;
            color: $colorTitle;
        }
    }
</style>
