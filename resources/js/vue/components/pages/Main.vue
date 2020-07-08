<template>
    <div class="main">
        <div class="content menu">

            <div class="item" v-for="item in navTicket" v-if="item.auth === 'both' || item.auth === auth.login">
                <router-link class="nav-link" :to="item.path" active-class="active">{{item.title}}</router-link>
            </div>

        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "Main",
        computed: {
            ...mapState(['navTicket']),

            auth() {
                return this.$store.state.Auth;
            }
        }
    }
</script>

<style scoped>
    .main{
        width: 80%;
        margin: 0 auto;
    }
    .menu{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .item{
        width: 210px;
        height: 100px;
        border: solid 1px #7b7878;
        border-radius: 4px;
        background-color: #717171;

        margin-right: 50px;


        font-size: 20px;
        text-align: center;
        position: relative;
    }
    .item a {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;

        color: white;
        text-decoration: none;

        transition:0.4s;
    }
    .item a:hover{
        background-color: #e9e9e9;
        color: #3f3939;
    }
</style>
