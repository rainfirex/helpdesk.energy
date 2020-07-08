<template>
    <ul>
        <li v-for="item in nav">
            <router-link
                class="nav-link"
                :to="item.path"
                active-class="active"
                v-if="item.auth === 'both' || item.auth === auth.login"
            >{{item.title}}</router-link>
        </li>
    </ul>

</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "Navigator",
        computed: {
            ...mapState(['nav']),

            auth() {
                return this.$store.state.Auth;
            },
        },
        data() {
            return { }
        }
    }
</script>

<style scoped>
    a {
        color: white;
        transition:0.4s;
    }
    a:hover {
        color: #c2c2c2;
    }
    ul{
        list-style: none;
        margin-bottom: 0;
    }
    li{
        display: inline-block;
    }
</style>
