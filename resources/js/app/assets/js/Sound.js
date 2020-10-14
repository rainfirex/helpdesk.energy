export default {
    init() {
        store.commit('setIsSoundMute', this.loadSoundMode());
    },

    playSound(sound) {
        if (!store.getters.getIsSoundMute)
            new Audio(sound).play();
    },

    setMode(mode) {
        store.commit('setIsSoundMute', mode);
        localStorage.setItem('is_sound_mute', mode);
    },

    loadSoundMode(){
        const value = localStorage.getItem('is_sound_mute');
        if (value != null) {
            return (value == 'true') ? true : false;
        } else
            return false;
    }
}
