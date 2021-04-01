<template>
    <div>
        <p class="mb-0"><b>Документы:</b></p>
        <ul>
            <li v-for="doc in docs" :key="doc.id">
                <a :href="doc.url" @click="currDoc = doc" :download="doc.name">{{ doc.name }}</a>
            </li>
        </ul>
<!--        <div v-if="currDoc.name">-->
<!--            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://helpdesk.sakh.dvec.ru/storage/docs/24.12.2020/UUF2GvDXV9dFiTC5oAjmetpGdmBzQV23iUKq0eUi.docx" frameborder="0"></iframe>-->
<!--        </div>-->
<!-- https://view.officeapps.live.com/op/embed.aspx?src=http://remote.url.tld/path/to/document.doc-->
<!-- <iframe src="https://docs.google.com/gview?url=http://remote.url.tld/path/to/document.doc&embedded=true"></iframe>-->

    </div>
</template>

<script>
    export default {
        name: "Docs",
        props: {
            docs: {
                required: true,
                type: Array
            }
        },
        data() {
            return {
                currDoc: {}
            }
        }
    }
</script>

<style lang="scss" scoped>
    a{
        color: #4c7fb3;
    }
    li{
        float: left;
        margin-top:8px;
        margin-right: 25px;
    }
    li:last-child{
        margin-right: 0;
    }
</style>