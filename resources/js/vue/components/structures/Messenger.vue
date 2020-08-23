<template>
    <div class="messenger mt-4 p-3 col-10 offset-1"
         v-if="messenger.text"
         v-bind:class="{success: messenger.status === 'success', error: messenger.status === 'error'}"
         @dblclick="close">
        <p>{{messenger.text}}</p>
        <hr>
        <p class="help m-0 p-0" @click="close">Нажми чтобы закрыть</p>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    export default {
        name: "Messenger",

        computed: {
            ...mapState(['messenger']),
        },

        methods: {
            close() {
                this.$store.commit('setTextMessenger', {text:''});
            }
        }
    }
</script>

<style scoped>
 .messenger{
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
</style>
