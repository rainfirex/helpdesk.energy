export default {

    Login(state) {
        state.Auth.user_id = localStorage.getItem('user_id');
        state.Auth.api_token = localStorage.getItem('api_token');
        state.Auth.name = localStorage.getItem('name');
        state.Auth.email = localStorage.getItem('email');
        state.Auth.phone = localStorage.getItem('phone');
        state.Auth.mobile = localStorage.getItem('mobile');
        state.Auth.department = localStorage.getItem('department');
        state.Auth.title = localStorage.getItem('title');
        state.Auth.is_handler = (localStorage.getItem('is_handler') == 'true') ? true : false;
        state.Auth.login =
            state.Auth.user_id !== null &&
            state.Auth.api_token !== null &&
            state.Auth.name !== null;
    },

    setTextMessenger(state, val) {
        state.messenger.text = val.text;
        state.messenger.status = val.status;


        if (state.messenger.timerId) {
            clearTimeout(state.messenger.timerId);
        }

        state.messenger.timerId = setTimeout(() => {
            if (state.messenger.text.length > 1) {
                state.messenger.text = '';
            }

        }, state.messenger.timeout);
    },

    changeLoaderBarMode(state, val){

        if (val === true) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }

        state.loaderBar = val;
    },

    playSound(state, sound) {
        if (!state.isSoundMute) {
            new Audio(sound).play();
        }
    },

    setSoundMode(state, mode) {
        state.isSoundMute = mode;
        localStorage.setItem('is_sound_mute', mode);
    },

   loadSoundMode(state){
       const value = localStorage.getItem('is_sound_mute');
       if (value != null) {
           state.isSoundMute = (value == 'true') ? true : false;
       }
    }
}
