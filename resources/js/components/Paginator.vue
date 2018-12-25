<template>
    <nav aria-label="Page navigation">
        <ul class="pagination" v-if="shouldPaginate">
            <li v-show="prevUrl" class="page-item">
                <a href="#" aria-label="Previous" @click.prevent="page--">
                    <span aria-hidden="true">&laquo; Previous</span>
                </a>
            </li>
            <li v-show="nextUrl" class="page-item">
                <a href="#" aria-label="Next" @click.prevent="page++">
                    <span aria-hidden="true">Next  &raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        props: ['dataSet'],
        data() {
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false,
            }
        },
        watch: {
            dataSet() {
                this.page = this.dataSet.current_page;
                this.nextUrl = this.dataSet.next_page_url;
                this.prevUrl = this.dataSet.prev_page_url;
            },
            page() {
                this.broadcast().updateUrl();
            }

        },
        computed: {
            shouldPaginate() {
                return !!this.prevUrl || !!this.nextUrl;
            }
        },
        methods: {
            broadcast() {
                return this.$emit('changed-page', this.page);
            },
            updateUrl() {
                history.pushState(null, null, '?page=' + this.page);
            }
        }

    }
</script>