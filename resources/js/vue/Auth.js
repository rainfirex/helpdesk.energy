export default {
    init() {
        store.commit('Login');
    },

    login(data){
        localStorage.setItem('user_id', data.user_id);
        localStorage.setItem('api_token', data.api_token);
        localStorage.setItem('name', data.name);
        localStorage.setItem('email', data.email);
        localStorage.setItem('phone', data.phone);
        localStorage.setItem('mobile', data.mobile);
        localStorage.setItem('department', data.department);
        localStorage.setItem('title', data.title);
    },

    logout() {
        localStorage.removeItem('user_id');
        localStorage.removeItem('api_token');
        localStorage.removeItem('name');
        localStorage.removeItem('email');
        localStorage.removeItem('phone');
        localStorage.removeItem('mobile');
        localStorage.removeItem('department');
        localStorage.removeItem('title');
    }
}
