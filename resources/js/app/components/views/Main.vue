<template>
    <div class="content menu">
        <div class="item" v-for="nav in getTicketNavs" v-if="nav.auth === 'both' || nav.auth === getUser.login">
            <router-link class="nav-link" :to="nav.path" active-class="active">
                {{nav.title}}
            </router-link>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    export default {
        name: "Main",
        computed: mapGetters(['getTicketNavs', 'getUser'])
    }
</script>

<style scoped>
    .menu{
        display: flex;
        align-items: center;
        justify-content: space-evenly;
        flex-wrap: wrap;
    }

    .item{
        width: 210px;
        height: 100px;
        border-radius: 4px;
        background-color: #717171;

        user-select: none;

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

        transition:0.7s;
    }

    .item a:hover{
        background-color: #e9e9e9;
        color: #3f3939;
    }
</style>
