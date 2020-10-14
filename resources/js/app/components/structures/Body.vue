<template>
    <div class="body">
        <transition name="ani">
            <router-view></router-view>
        </transition>
    </div>

</template>

<script>
    export default {
        name: "Body"
    }
</script>

<style lang="scss" scoped>
    .body{
        padding-bottom: 90px;
    }

    .ani-enter-active{
        animation: fadeIn 0.6s ease-in;
    }

    .ani-leave-active{
        animation: moveInUp .1s ease-in;
    }

    @keyframes fadeIn{
        0%{
            transform: translateY(-1400px);
        }
        50%{
            transform: translateY(-500px);
        }
        100%{

            transform: translateY(0);
        }
    }

    @keyframes moveInUp {
        0%{
            opacity: 1;
        }
        50%{
            opacity: 0.5;
        }
        100%{
            opacity: 0;
        }
    }

</style>
