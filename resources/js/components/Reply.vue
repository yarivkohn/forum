<template>
    <div :id="'reply-' + id" class="card">
        <div class="card-header bg-" :class="isBest? ' bg-success' : ''">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profile/' + reply.owner.name" v-text="reply.owner.name"></a>
                    said <span v-text="ago"></span>
                </h5>
                <div v-if="signedIn">
                    <favorite :reply="{ reply }"></favorite>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <form @submit="update">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body" required></textarea>
                        <button class="btn btn-sm btn-primary">Update</button>
                        <button class="btn btn-sm btn-link" type="button" @click="cancel">Cancel</button>
                    </div>
                </form>
            </div>
            <div v-else class="body" v-html="body"></div>
        </div>

        <div class="card-footer level" v-if="(authorized('owns', reply) || authorized('owns', thread))">
            <div  v-if="authorized('owns', reply)">
                <button class="btn btn-sm btn-info mr-1" @click="editing=true">Edit</button>
                <button class="btn btn-sm btn-danger mr-1" @click="destroy">Delete</button>
            </div>
            <button class="btn btn-sm btn-default ml-a" @click="markBestReply" v-if="authorized('owns', reply.thread) ">Best Reply</button>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favotire';
    import moment from 'moment';
    export default {
        props: ['reply'],
        components: {Favorite},
        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                originalBody: this.reply.body,
                isBest: this.reply.isBest,
            }
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.reply.id, {
                    body: this.body
                }).then(response => {
                    flash('Updated!');
                    this.editing = false;
                }).catch(error => {
                    flash(error.response.reply, 'danger');
                });
            },
            cancel() {
                this.editing=false;
                this.body = this.originalBody;
            },
            destroy() {
                axios.delete('/replies/' + this.reply.id);
                this.$emit('deleted', this.reply.id)
            },
            markBestReply() {
                axios.post('/replies/'+ this.reply.id +'/best', {id: this.reply});
                window.events.$emit('best-reply-selected', this.reply.id);
            }

        },
        computed: {
            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            }
        },
        created() {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        }
    }
</script>

<style>
</style>
