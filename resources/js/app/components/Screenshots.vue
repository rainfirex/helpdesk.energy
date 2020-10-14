<template>
    <div class="mt-3">
        <p class="mb-0"><b>Загруженные файлы:</b></p>
        <div class="screenshots">
            <div
                v-for="(screenshot, index) in screenshots"
                @click="setLoaderBar(false);indexScreenshot = null"
                :class="{'background-screenshot': index === indexScreenshot}">
                <div class="screenshot-container" :class="{'full': index === indexScreenshot}">
                    <div class="btn-close">
                        <i class="fa fa-window-close ico-close" aria-hidden="true" title="Закрыть (ESC)"
                           v-show="index === indexScreenshot"></i>
                    </div>
                    <img class="screenshot" :src="screenshot.url" alt="screenshot"
                         @click.stop="setLoaderBar(true);indexScreenshot = index">
                    <div class="block-info">
                        <p class="title-img mb-0" v-show="index === indexScreenshot" @click.stop="">{{screenshot.name}}<br>
                            <a class="title-link" :href="screenshot.url" target="_blank"
                               title="Открыть в новом окне браузера">Открыть отдельно</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';
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
        },

        methods: {
            ...mapActions(['setLoaderBar'])
        },

        watch: {
            indexScreenshot() {
                if (this.indexScreenshot) {
                    //Что-то хотел сделать при открытии окна
                }
            }
        },

        mounted() {
            document.body.addEventListener('keydown', e => {
                if (e.code === 'Escape') {
                    this.indexScreenshot = null;
                    this.setLoaderBar(false)
                }

                if (this.indexScreenshot) {

                    const count = this.screenshots.length - 1;

                    //На 0 почему-то виснет this.indexScreenshot > 0
                    if (e.code === 'ArrowLeft' && this.indexScreenshot > 1) {
                        this.indexScreenshot--;
                    }

                    if (e.code === 'ArrowRight' && this.indexScreenshot < count) {
                        this.indexScreenshot++;
                    }
                }

            });

        }
    }
</script>

<style lang="scss" scoped>

    .screenshots{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: center;
        padding: 30px;

        .full{
            cursor: auto;
            width: 900px;
            max-height: 95%;
            margin: 0;
            overflow: auto;
        }
    }

    .screenshot-container{
        position: relative;
        width: 150px;
        margin-bottom: 10px;
        cursor: pointer;

        img {
            padding: 2px;
            background: aliceblue;
        }

        div.btn-close{
        position: absolute;
            top: 0;
            right: 0;

            .ico-close {
                cursor: pointer;
                position: fixed;
                background: #dcdcdc94;
                padding: 10px;
                color: #b51515;
                font-size: 1.4em;
                transform: translateX(-100%);
                transition: 0.3s;
            }
            .ico-close:hover {
                background: #F0F0F0;
            }
        }

        div.block-info{
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;

            .title-img{
                position: fixed;
                bottom: 0;
                left: 0;

                width: 100%;
                background: #333131eb;
                color: white;
                padding: 5px;
                overflow: hidden;
                text-align: center;
                white-space: nowrap;
            }

            .title-link{
                color: #f59b9b;
                display: inline-block;
                font-weight: 500;
                transition: 0.5s;
                text-decoration: none;
                font-size: 0.8em;
            }

            .title-link:hover {
                color: red;
            }
        }
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

</style>
