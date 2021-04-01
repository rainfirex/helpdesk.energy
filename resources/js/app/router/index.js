import VueRouter from 'vue-router';
import Main from "../components/views/Main";
import CreateTicket from "../components/views/user/CreateTicket";
import MonitorTicket from "../components/views/MonitorTicket";
import Auth from "../components/views/Auth";
import UserTickets from "../components/views/user/UserTickets";
import ShowDetaleTicket from "../components/views/user/ShowDetaleTicket";
import HandlerTickets from "../components/views/handler/HandlerTickets";
import ShowDetaleHandlerTickets from "../components/views/handler/ShowDetaleHandlerTickets";
import UserTicketCompleted from "../components/views/user/UserTicketCompleted";
import NotFound from '../components/views/NotFound';
import RequestInfoResource from "../components/views/RequestInfoResource";
import ShowInfoResource from "../components/views/handler/ShowInfoResource";

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
            path: '/request-info-resource', component: RequestInfoResource, name: 'request.info.resource'
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
        },
        {
            path: '*', component: NotFound, name: 'not-found', meta: {
                requestAuth: false
            }
        },
        {
            path: '/resource-access', component: ShowInfoResource, name: 'info-resourse', meta:{
                requestAuth: true
            }
        }
    ], mode : 'history'
});
