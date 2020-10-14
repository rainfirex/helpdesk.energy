<template>
    <div class="indicator-auto-update">
        <p class="indicator-auto-update-enable" v-if="is_enable">автоматическое обновление</p>
        <p class="indicator-auto-update-disable" v-else>обновление отключено</p>
    </div>
</template>

<script>
    export default {
        props:['is_enable']
    };
</script>

<style scoped>
    .indicator-auto-update{
        font-style: italic;
        font-size: 11px;
    }
    .indicator-auto-update-enable{
        color: #37b353;
    }
    .indicator-auto-update-disable{
        color: #ce4040;
    }
</style>
