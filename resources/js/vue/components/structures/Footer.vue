<template>
    <footer>
        <p>Все права защищены © 2019 г.</p>
        <p>ОП "Энергосбыт" ПАО "Сахалинэнерго"</p>
    </footer>
</template>

<script>
    export default {
        name: "Footer"
    }
</script>

<style scoped>
    footer{
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
</style>
