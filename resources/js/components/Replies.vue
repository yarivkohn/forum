<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :reply="reply" @deleted="remove(index)"></reply>
        </div>
        <new-reply @created="add"></new-reply>
        <paginator :dataSet="dataSet" @changed-page="fetch"></paginator>
    </div>
</template>

<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collection from '../mixins/collections';

    export default {
        components: { Reply, NewReply },
        mixins: [collection],
        data() {
            return {
                dataSet: false
                // items : [], // Will now come from the collection mixin
            }
        },
        methods: {
            fetch(page) {
              axios.get(this.url(page))
                  .then(this.refresh);
            },
            url(page){
                if(!page){
                    let query = location.search.match(/page=(\d+)/);
                    page = query ? query[1] : 1;
                }
                return `${location.pathname}/replies?page=${page}`;
            },
            refresh(response) {
                this.dataSet = response.data;
                this.items = response.data.data; //lots of data here...
                window.scrollTo(0,0);
            }

        },
        created() {
            this.fetch();
        }
    }
</script>