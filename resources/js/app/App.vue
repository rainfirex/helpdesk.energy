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

    import Sound from "./assets/js/Sound";
    import User from "./assets/js/User";
    import Notification from "./assets/js/Notification";
    export default {
        name:"App",
        components: {
            Header,
            Messenger,
            Body,
            Footer,
            LoaderBar
        },
        created() {
            Sound.init();
            User.init();
            Notification.init();
        }
    };
</script>
