<template>
    <footer>
        <div>
            <p><a href="/storage/helpdesk.sakh.dvec.docx" class="manual">Инструкция к системе</a></p>
            <p class="mt-2">Все права защищены © 2020 г. ОП "Энергосбыт" ПАО "Сахалинэнерго"</p>
            <p>{{ getPermMessage }}</p>
        </div>
    </footer>
</template>

<script>
    import { mapGetters } from 'vuex';
    export default {
        name: "Footer",
        computed: mapGetters(['getPermMessage'])
    }
</script>

<style scoped>
    footer{
        padding: 20px;
        min-height: 80px;
        background-color: #414141;
        color: #bababa;
        font-size: 12px;
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
    }
    p{
        margin-bottom: 5px;
        padding: 10px 0 0 45px;
    }
    .manual{
        font-size: 15px;
    }
</style>
