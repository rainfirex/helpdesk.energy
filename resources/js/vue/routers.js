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
            path: '/', component: Main
        },
        {
            path: '/create-ticket', component: CreateTicket
        },
        {
            path: '/monitor-ticket', component: MonitorTicket
        },
        {
            path:'/auth', component: Auth
        },
        {
            path: '/tickets', component: UserTickets, name: 'user-tickets'
        },
        {
            path: '/detale-ticket/:id', component: ShowDetaleTicket, name: 'detale-ticket'
        },
        {
            path: '/handler-tickets', component: HandlerTickets, name: 'handler-tickets'
        },
        {
            path: '/handler-tickets/detale-ticket/:id', component: ShowDetaleHandlerTickets, name: 'handler-detale-ticket'
        },
        {
            path: '/completed-tickets', component: UserTicketCompleted, name: 'user-completed-tickets'
        }
    ], mode : 'history'
});
