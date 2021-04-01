<template>
    <div class="content">
        <div class="row">
            <div class="col-4">
                <p><b>Страница</b> {{ currentPage }} / {{ InfoCount }}</p>
            </div>
            <div class="col-4">
                <button class="btn btn-control" @click="prevPage"><-</button>
            </div>
            <div class="col-4">
                <button class="btn btn-control" @click="nextPage">-></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <tr class="text-center">
                    <th>Создан</th>
                    <th>Имя</th>
                    <th>Руководитель подразделения</th>
                    <th>Должность</th>
                    <th>Подразделение</th>
                    <th>Период</th>
                </tr>
                <tr v-for="(resource, index) in InfoResource" :key="resource.id" class="row-info" @click="getInfoResource(resource.id, index)" :class="{'row-info-select': currentIndex === index}">
                    <td>{{ dateFormat(resource.created_at) }}</td>
                    <td>{{ resource.name }}</td>
                    <td>{{ resource.leader }}</td>
                    <td>{{ resource.function }}</td>
                    <td>{{ resource.unit }}</td>
                    <td>{{ dateFormat(resource.perStart) }} - {{ dateFormat(resource.perEnd) }}</td>
                </tr>
            </table>
        </div>
        <div class="resource-info" v-if="resource.name" @click="closeInfoResource">
            <div class="form" @click.stop="">
                <div class="form-close">
                    <button class="btn btn-danger" @click="closeInfoResource">X</button>
                </div>
                <div class="row">
                    <div class="col-6">
                        <p><b>Сотрудник:</b> {{ resource.name }}</p>
                    </div>
                    <div class="col-6">
                        <p><b>Доступ</b> с {{ dateFormat(resource.perStart) }} по {{ dateFormat(resource.perEnd) }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <p><b>Заявка создана:</b> {{ dateFormat(resource.created_at) }}</p>
                    </div>
                    <div class="col-6">
                        <p><a :href="resource.pdf_url" download>Скачать PDF файл</a></p>
                    </div>
                </div>
                <div v-if="resource.pdf_url">
                    <embed class="pdf-view" :src="resource.pdf_url"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters, mapActions} from 'vuex';
    import Sound from "../../../assets/js/Sound";
    export default {
        name: "ShowInfoResource",
        data(){
            return {
                isLoading: false,
                currentIndex: -1,
                resources : [],
                currentPage: 1,
                lengthItemOnPage:25,
                count: 0,
                resource: {}
            }
        },
        computed: {
            InfoResource() {
                return this.resources.filter((item, index) => {
                    let start = (this.currentPage - 1) * this.lengthItemOnPage;
                    let end = this.currentPage * this.lengthItemOnPage;

                    if (index >= start && index < end) return true;
                });
            },
            InfoCount() {
                return Math.ceil(this.resources.length / this.lengthItemOnPage);
            }
        },
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            getInfoResources(){
                this.isLoading = true;
                this.setLoaderBar(true);

                axios.get('/api/resource-access')
                    .then(response => {
                        this.count = response.data.count;
                        this.resources = response.data.resources;
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.isLoading = false;
                        this.setLoaderBar(false);
                    });
            },
            getInfoResource(resourceId, index){
                this.isLoading = true;
                this.setLoaderBar(true);

                axios.get(`/api/resource-access/${ resourceId }`)
                    .then(response => {

                        this.resource = response.data.resource;
                        this.currentIndex = index;

                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.isLoading = false;
                        this.setLoaderBar(false);
                        document.body.style.overflow = 'hidden';
                    });
            },
            closeInfoResource(){
                this.resource = {};
                document.body.style.overflow = '';
            },
            dateFormat(dateTime){
                return new Date(dateTime).toLocaleDateString();
            },
            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage -= 1;

                    this.currentIndex = -1;
                }
            },
            nextPage() {
                if ((this.currentPage * this.lengthItemOnPage) < this.resources.length) {
                    this.currentPage += 1;

                    this.currentIndex = -1;
                }
            },
            listenerKeyDown(e) {
                if (e.code === 'ArrowRight' && e.key === 'ArrowRight') {
                    this.nextPage();
                }
                else if (e.code === 'ArrowLeft' && e.key === 'ArrowLeft') {
                    this.prevPage();
                }
            }
        },
        created() {
            this.getInfoResources();
            document.addEventListener('keydown', this.listenerKeyDown);
        },
        beforeDestroy() {
            document.body.removeEventListener('keydown', this.listenerKeyDown);
        }
    }
</script>

<style lang="scss" scoped>
    .row-info{
        cursor: pointer;
        &:hover{
          background: #d0d0d0;
        }
        &:active{
            background: #afafaf;
        }
        &.row-info-select{
            background: #96afca;
            color: white;
        }
    }
    .pdf-view{
        width: 100%;
        height: 1100px;
    }
    .resource-info{
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        user-select: none;
        background: rgba(31, 33, 34, 0.79);
        overflow: auto;
        .form{
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            position: relative;
            .form-close{
                position: absolute;
                right: 11px;
                top: 6px;
                z-index: 1;
            }
        }
    }
</style>