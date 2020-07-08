<template>
    <transition name="fade">
        <div class="loader-bar" v-if="loaderBar">
            <div>
                <img src="/img/Loading.gif" alt="loading">
                Loading...
            </div>
        </div>
    </transition>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "LoaderBar",

        computed: {
             ...mapState(['loaderBar'])
        }
    }
</script>

<style scoped>
    .loader-bar{
        color: white;
        background: #151616db;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 999;

        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loader-bar > div{
        font-size: 1.4em;
        padding: 20px 30px;
        background: #0062cc;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    img {
        width: 40px;
    }
</style>
