<template>
    <header>
        <Navigator></Navigator>
    </header>
</template>

<script>
    import Navigator from "./Navigator";

    export default {

        name: "Header",

        data() {
            return {
            }
        },

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
        background-color: #363636;
        min-height: 26px;

        position: sticky;
        top: 0;
        z-index: 999;
    }
</style>
