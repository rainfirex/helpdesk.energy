export default {

    loaderBar: false,

    isSoundMute: true,

    messenger: {
        text: '',
        status: '',
        timerId: null,
        timeout: 8500
    },

    nav: [
        {path: '/', title: 'Главная', auth: 'both', ico: 'fa-home'},
        {path: '/auth', title: 'Авторизация', auth: false, ico: 'fa-user'},
        {path: '/tickets', title: 'Заявки', auth:true, ico: 'fa-newspaper-o'}
    ],

    navAuth: [
        {path: '/logout', title: 'Выход', auth: true}
    ],

    navTicket: [
        {path: '/create-ticket', title: 'Создать заявку', auth: true},
        {path: '/monitor-ticket', title: 'Проверить заявку', auth: 'both'},
        {path: '/completed-tickets', title: 'Завершенные заявки', auth: true}
    ],

    navHandler: [
        {path: '/handler-tickets', title: 'Обработка заявок', auth: true, is_handler: true, ico:'fa-sign-language'}
    ],

    Auth: {
        login: false,
        user_id: null,
        api_token: null,
        name: null,
        email: null,
        phone: null,
        mobile: null,
        department: null,
        title: null,
        is_handler: null
    },

    autoUpdateDataOnPage: 80000
};
