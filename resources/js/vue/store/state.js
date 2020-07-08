export default {

    test: 'Hello test!',

    loaderBar: false,

    messenger: {
        text: '',
        status: '',
        timeout: 8500
    },

    nav: [
        {path: '/', title: 'Главная', auth: 'both'},
        {path: '/auth', title: 'Авторизация', auth: false},
        {path: '/tickets', title: 'Заявки', auth:true},
        {path: '/handler-tickets', title: 'Обработка заявок', auth: true}
    ],

    navAuth: [
        {path: '/logout', title: 'Выход', auth: true}
    ],

    navTicket: [
        {path: '/create-ticket', title: 'Создать заявку', auth: true},
        {path: '/monitor-ticket', title: 'Проверить заявку', auth: 'both'}
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
        title: null
    }
};
