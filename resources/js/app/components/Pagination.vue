<template>
    <div class="col-12 col-md-5">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item"
                    v-for="numPage in pagination"
                    :class="{'active': numPage.num === currentPage & numPage.title === currentPage}">

                    <a class="page-link"
                       v-if="numPage.num === currentPage "
                       @click.prevent=""
                    >{{numPage.title}}</a>

                    <a class="page-link"
                       v-else
                       @click="$emit('getTickets', numPage.num)"
                    >{{numPage.title}}</a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "Pagination",

        props: {
            countPage: {
              type: Number,
              required: true
            },
            currentPage: {
                type: Number,
                required: true
            }
        },

        computed: {

            pagination() {

                let tmp_pages = [];

                if (this.countPage > 0) {

                    tmp_pages.push({title: 'Первая', num: 1});

                    for (let i = 1; i <= this.countPage; i++) {

                        if (this.currentPage === i || this.currentPage + 1 === i || this.currentPage + 2 === i || this.currentPage + 3 === i ||
                            this.currentPage - 1 === i || this.currentPage - 2 === i || this.currentPage - 3 === i ||
                            10 === i || 20 === i || 30 === i || 40 === i || 50 === i || 60 === i || 70 === i || 80 === i || 90 === i || 100 === i || 500 === i){

                            tmp_pages.push({title: i, num:i});

                        }
                    }

                    tmp_pages.push({title: 'Последняя', num: this.countPage});
                }

                return  tmp_pages;
            }
        }
    }
</script>
