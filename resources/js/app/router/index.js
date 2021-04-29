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

function isAuth(to, from, next) {
    if(localStorage.getItem('api_token')) {
        next();
    } else {
        next(false);
        window.location.href = '/auth';
    }
    // axios.get(`/api/is-auth`)
    //     .then(response => {
    //         // if (response.data.result)next();
    //     })
    //     .catch(error => {
    //         if (error.response && error.response.status === 401){
    //             next(false);
    //             // window.location.href = '/';
    //         }
    //     });
}

export default new VueRouter({
    routes : [
        {
            path: '/', component: Main, name: 'main'
        },
        {
            path: '/create-ticket', component: CreateTicket, name: 'create-ticket', meta: {
                requestAuth: true
            }, beforeEnter: (to, from, next) => { isAuth(to, from, next) }
        },
        {
            path: '/monitor-ticket', component: MonitorTicket, name: 'monitor-ticket'
        },
        {
            path:'/auth', component: Auth, name: 'auth', meta: {
                guest: true
            }, beforeEnter: (to, from, next) => { next() }
        },
        {
            path: '/tickets', component: UserTickets, name: 'user-tickets', meta: {
                requestAuth: true
            }, beforeEnter: (to, from, next) => { isAuth(to, from, next) }
        },
        {
            path: '/detale-ticket/:id', component: ShowDetaleTicket, name: 'detale-ticket', meta: {
                requestAuth: true
            }, beforeEnter: (to, from, next) => { isAuth(to, from, next) }
        },
        {
            path: '/handler-tickets', component: HandlerTickets, name: 'handler-tickets', meta: {
                requestAuth: true
            }, beforeEnter: (to, from, next) => { isAuth(to, from, next) }
        },
        {
            path: '/handler-tickets/detale-ticket/:id', component: ShowDetaleHandlerTickets, name: 'handler-detale-ticket', meta: {
                requestAuth: true
            }, beforeEnter: (to, from, next) => { isAuth(to, from, next)}
        },
        {
            path: '/completed-tickets', component: UserTicketCompleted, name: 'user-completed-tickets', meta: {
                requestAuth: true
            }, beforeEnter: (to, from, next) => { isAuth(to, from, next) }
        },
        {
            path: '*', component: NotFound, name: 'not-found', meta: {
                requestAuth: false
            }
        }
    ], mode : 'history'
});