<template>
        <div class="wrapper">
            <Header></Header>
            <Messenger @hoverMouse="alert('123')"></Messenger>
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
    import Sound from "./Sound";

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
            Sound.init();
        }
    };
</script>
