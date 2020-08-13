<template>
    <div class="screenshots mt-1 mb-1">
        <div
            v-for="(screenshot, index) in screenshots"
            @click="indexScreenshot = null"
            :class="{'background-screenshot': index === indexScreenshot}">
            <div class="screenshot-container" :class="{'screenshot-container-full': index === indexScreenshot}">
                <img class="screenshot" :src="screenshot.url" alt="screenshot" @click.stop="indexScreenshot = index">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Screenshots",

        props: {
            screenshots: {
                type: Array,
                default: []
            }
        },

        data() {
            return {
                indexScreenshot : null
            }
        }
    }
</script>

<style scoped>

    .screenshots{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: center;
        padding: 30px;
    }

    .screenshot-container{
        width: 150px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .screenshot {
        width: 100%;
        height: auto;
    }

    .background-screenshot{
        z-index: 9999;
        width: 100%;
        height: 100%;
        position: fixed;
        left: 0;
        top: 0;
        background: #414141e0;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-content: center;
        /*align-items: center;*/
    }

    .screenshot-container-full{
        cursor: auto;
        width: 700px;
        max-height: 95%;
        margin: 0;
        overflow: auto;
    }

</style>
