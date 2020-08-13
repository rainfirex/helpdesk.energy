<template>
    <div class="main">
        <div class="content menu">

            <div class="item mr-md-4 mr-sm-3" v-if="pageCreateTicket.auth === 'both' || pageCreateTicket.auth === auth.login">
                <router-link class="nav-link" :to="pageCreateTicket.path" active-class="active">{{pageCreateTicket.title}}</router-link>
            </div>
            <div class="item" v-if="pageMonitorTicket.auth === 'both' || pageMonitorTicket.auth === auth.login">
                <router-link class="nav-link" :to="pageMonitorTicket.path" active-class="active">{{pageMonitorTicket.title}}</router-link>
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
            },

            pageCreateTicket() {
                return this.navTicket[0];
            },

            pageMonitorTicket() {
                return this.navTicket[1];
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
        flex-wrap: wrap;
    }
    .item{
        width: 210px;
        height: 100px;
        border: solid 1px #7b7878;
        border-radius: 4px;
        background-color: #717171;

        /*margin-right: 50px;*/


        font-size: 20px;
        text-align: center;
        position: relative;
    }
    .item:last-child {
        /*margin-right:0;*/
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
