<template>
    <header>
        <Navigator></Navigator>
    </header>
</template>

<script>
    import Navigator from "./Navigator";

    export default {

        name: "Header",

        components: {
            Navigator
        },

        computed: {

            username(){
                return this.$store.state.Auth.name;
            },

            auth() {
                return this.$store.state.Auth;
            }
        },

        methods: {}
    }
</script>

<style scoped>
    header{
        background: linear-gradient(to top, #7b7676, #1b1919, #363636, #776f6f);
        /*background-color: #363636;*/
        min-height: 26px;

        position: sticky;
        top: 0;
        z-index: 999;
    }
</style>
