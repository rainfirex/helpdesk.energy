<template>
    <ul class="p-3 ">
        <li class="item-ticket p-1 mb-2"  v-for="ticket in tickets">
            <router-link class="d-block" :to="{name: 'detale-ticket', params: {id : ticket.id}}">
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
                        <p class="item-ticket-title mb-1">Название: {{ticket.title}}</p>

                        <p v-if="ticket.is_new_user_comment && ticket.is_new_user_comment.length > 0">Новых комментариев: <span class="badge badge-primary">{{ticket.is_new_user_comment.length}}</span></p>
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

<style scoped>
    ul{
        list-style: none;
        overflow: hidden;
    }
    .item-ticket{
        border-bottom: solid 1px #e2e2e2;
        background: #f7f7f7;
    }
    .item-ticket:last-child {
        border-bottom: none;
    }
    .item-ticket > a{
        text-decoration: none;
        color: #656767;
    }
    .item-ticket:hover{
        background-color: white;
        transition: .4s;
    }
    .item-ticket-title{
        font-size: 1.2em;
        color: #545454;
    }
</style>
