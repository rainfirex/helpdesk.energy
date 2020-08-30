<template>
    <div class="messenger mt-4 p-3 col-10 offset-1"
         :class="{'hover': isHover}"
         v-if="messenger.text"
         v-bind:class="{success: messenger.status === 'success', error: messenger.status === 'error'}"
         @dblclick="close"
         @mouseenter="hover"
         @mouseleave="isHover=false">
        <p>{{messenger.text}}</p>
        <hr>
        <p class="help m-0 p-0" @click="close">Нажми чтобы закрыть</p>
    </div>
</template>

<script>
    export default {
        name: "Messenger",

        data() {
            return {
                isHover : false
            }
        },
        computed: {
            ...mapState(['messenger']),
        },

        methods: {
            close() {
                this.isHover=false;
                this.$store.commit('setTextMessenger', {text:''});
            },

            hover() {
                this.isHover = true;
                if (this.messenger.timerId)
                    clearInterval(this.messenger.timerId);
            }
        },

        watch: {
        }
    }
    import {mapState} from 'vuex'
</script>

<style scoped>
 .messenger{
     box-shadow: 1px 2px 10px 0.2rem rgba(0, 0, 0, 0.25);
     background-color: #6281a0f7;
     color: white;
     line-height: 40px;
     position: fixed;
     z-index: 99;
 }
 .error{
     background-color: #965c64e6;
 }
 .success{
     background-color: #5c9669e6;
 }
 .help{
     font-size: 11px;
     cursor: pointer;
 }
    .hover{
        outline: solid 2px #f0f0f0;
    }
</style>
