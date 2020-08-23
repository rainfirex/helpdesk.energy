import VueRouter from 'vue-router';
import Main from "./components/pages/Main";
import CreateTicket from "./components/pages/user/CreateTicket";
import MonitorTicket from "./components/pages/MonitorTicket";
import Auth from "./components/pages/Auth";
import UserTickets from "./components/pages/user/UserTickets";
import ShowDetaleTicket from "./components/pages/user/ShowDetaleTicket";
import HandlerTickets from "./components/pages/handler/HandlerTickets";
import ShowDetaleHandlerTickets from "./components/pages/handler/ShowDetaleHandlerTickets";
import UserTicketCompleted from "./components/pages/user/UserTicketCompleted";

export default new VueRouter({
    routes : [
        {
            path: '/', component: Main, name: 'main'
        },
        {
            path: '/create-ticket', component: CreateTicket, name: 'create-ticket', meta: {
                requestAuth: true
            }
        },
        {
            path: '/monitor-ticket', component: MonitorTicket, name: 'monitor-ticket'
        },
        {
            path:'/auth', component: Auth, name: 'auth', meta: {
                guest: true
            }
        },
        {
            path: '/tickets', component: UserTickets, name: 'user-tickets', meta: {
                requestAuth: true
            }
        },
        {
            path: '/detale-ticket/:id', component: ShowDetaleTicket, name: 'detale-ticket', meta: {
                requestAuth: true
            }
        },
        {
            path: '/handler-tickets', component: HandlerTickets, name: 'handler-tickets', meta: {
                requestAuth: true
            }
        },
        {
            path: '/handler-tickets/detale-ticket/:id', component: ShowDetaleHandlerTickets, name: 'handler-detale-ticket', meta: {
                requestAuth: true
            }
        },
        {
            path: '/completed-tickets', component: UserTicketCompleted, name: 'user-completed-tickets', meta: {
                requestAuth: true
            }
        }
    ], mode : 'history'
});
