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

    import Auth from "./Auth";

    export default {
        name:"Application",

        props: {},

        components: {
            Header,
            Messenger,
            Body,
            Footer,
            LoaderBar
        },

        methods: {},

        created() {
            Auth.init();
        }
    };
</script>

<style lang="scss">

    .wrapper{
        background-color: #d1d5d9;
        width: 100%;
        min-height: 100%;
        position: relative;
    }

</style>
