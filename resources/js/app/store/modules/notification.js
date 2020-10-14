export default {
    state: {
        permission: null,

        permMessage: ''
    },

    getters: {
        getPermMessage(state) {
            return state.permMessage;
        },
        getPermission(state) {
            return state.permission;
        }
    },

    mutations: {
        initPermission(state, payload) {
            state.permission = payload.permission;
            state.permMessage = payload.message;
        }
    },

    actions: {
        initPermission({commit}, payload) {
            commit('initPermission', payload);
        }
    }
}
