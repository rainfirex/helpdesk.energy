export default {
    state: {
        navMain: [
            {path: '/', title: 'Главная', auth: 'both', ico: 'fa-home'},
            {path: '/tickets', title: 'Заявки', auth:true, ico: 'fa-newspaper-o'}
        ],
        navAuth: [
            {path: '/logout', title: 'Выход', auth: true},
            {path: '/auth', title: 'Авторизация', auth: false, ico: 'fa-user'},
        ],
        navTicket: [
            {path: '/create-ticket', title: 'Создать заявку', auth: true},
            {path: '/monitor-ticket', title: 'Проверить заявку', auth: 'both'},
            {path: '/completed-tickets', title: 'Завершенные заявки', auth: true},
            {path: '/tickets', title: 'Активные заявки', auth: true}
        ],
        navHandler: [
            {path: '/handler-tickets', title: 'Обработка заявок', auth: true, is_handler: true, ico:'fa-sign-language'}
        ],
    },

    getters: {
        getMainNavs(state) {
            return state.navMain;
        },

        getAuthNavs(state) {
            return state.navAuth;
        },

        getTicketNavs(state) {
            return state.navTicket;
        },

        getHandlerNavs(state) {
            return state.navHandler;
        }
    },

    mutations: {
        setMainNav(state, payload) {
            state.nav.push(payload);
        }
    },

    actions: {
        setMainNav({ commit }, payload) {
            commit('setMainNav', payload)
        }
    }
}
