<template>
    <transition name="ani">
        <div class="messenger mt-1 ml-1"
             v-show="getMessenger.text"
             v-bind:class="{success: getMessenger.status === 'success', error: getMessenger.status === 'error'}"
             @click="close"
             @mouseenter="hover"
             @mouseleave="isHover=false"
        >
            <span class="m-2 ico" :class="{'ico-error': getMessenger.status === 'error', 'ico-success': getMessenger.status === 'success'}"></span>
            <div class="p-3 m-4 messenger-text">
                {{getMessenger.text}}
            </div>
        </div>
    </transition>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    export default {
        name: "Messenger",
        data() {
            return {
                isHover : false
            }
        },
        computed: {
            ...mapGetters(['getMessenger'])
        },
        methods: {
            ...mapActions(['setMessenger', 'clearTimeoutMessenger']),
            close() {
                this.isHover = false;
                this.setMessenger({text: ''});
            },
            hover() {
                this.isHover = true;
                this.clearTimeoutMessenger();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .messenger {
        box-shadow: 1px 2px 10px 0.2rem rgb(0 0 0 / 25%);
        background: linear-gradient(to bottom, #575252, #1b1919, #363636, #605858);
        color: white;
        line-height: 30px;
        position: fixed;
         z-index: 99;
        overflow: hidden;
        outline: solid 1px #f0f0f0;
        cursor: pointer;

        .error {
            &:hover {
            }
        }
        .success {
            &:hover {
            }
        }

        .ico {
            display: inline-block;
            &.ico-error {
                width: 10px;
                height: 10px;
                display: block;
                background: #bd2130;
            }
            &.ico-success {
                width: 10px;
                height: 10px;
                display: block;
                background: #28a745;
            }
        }
        .messenger-text {}
    }
</style>
