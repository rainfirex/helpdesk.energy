export default {
    state: {
        loaderBar: false,

        isSoundMute: true,

        autoUpdater: 80000
    },

    getters: {
        getLoaderBar(state) {
            return state.loaderBar;
        },

        getIsSoundMute(state) {
            return state.isSoundMute;
        },

        getAutoUpdater(state) {
            return state.autoUpdater;
        }
    },

    mutations: {
        setLoaderBar(state, payload) {
            if (payload === true) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }

            state.loaderBar = payload
        },

        setIsSoundMute(state, payload) {
            state.isSoundMute = payload;
        },

        setAutoUpdater(state, payload) {
            state.autoUpdater = payload;
        }
    },

    actions: {
        setLoaderBar({ commit }, payload) {
            commit('setLoaderBar', payload)
        },

        setIsSoundMute({ commit }, payload) {
            commit('setIsSoundMute', payload)
        },

        setAutoUpdater({ commit }, payload) {
            commit('setAutoUpdater', payload);
        }
    }
}
