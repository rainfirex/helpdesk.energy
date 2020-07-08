<template>
        <div class="wrapper">
            <Header></Header>
            <Messenger></Messenger>
            <Body></Body>
            <Footer></Footer>
            <LoaderBar></LoaderBar>
        </div>
</template>

<script>
    import Header from "./components/structures/Header";
    import Messenger from "./components/structures/Messenger";
    import Body from "./components/structures/Body";
    import Footer from "./components/structures/Footer";
    import LoaderBar from "./components/structures/LoaderBar";

    // import {mapState, mapGetters} from 'vuex';

    import Auth from "./Auth";

    export default {
        name:"Application",
        props: {
        },
        components: {
            Header,
            Messenger,
            Body,
            Footer,
            LoaderBar
        },
        computed: {

            // ...mapState(['test']),
            // ...mapGetters(['test']),
            // auth() {
            //     return this.$store.state.Auth;
            // },
        },
        created() {
            Auth.init();
        },
        methods: {}
    };
</script>

<style>
    html, body {height: 100%;}

    .wrapper{
        background-color: #d1d5d9;
        width: 100%;
        min-height: 100%;
        position: relative;
    }
    .content{
        width: 90%;
        max-width: 1480px;
        min-height: 500px;
        margin: 15px auto;
        background: #fafafa;
        padding: 25px;
        border-radius: 2px;
        overflow: hidden;
    }
</style>
