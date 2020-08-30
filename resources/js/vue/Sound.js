export default {



    init() {
        store.commit('loadSoundMode');
    },

    play(sound){
        store.commit('playSound', sound);
    },

    setMode(mode) {
      store.commit('setSoundMode', mode);
    },

    clear() {
        localStorage.removeItem('is_sound_mute');
    }
}
