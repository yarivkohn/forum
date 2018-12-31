<template>
    <div :id="'reply-' + id" class="card">
        <div class="card-header">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profile/' + data.owner.name" v-text="data.owner.name"></a>
                    said <span v-text="ago"></span>
                </h5>
                <div v-if="signedIn">
                    <favorite :reply="{ data }"></favorite>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <form @submit="update">
                    <div class="form-group">
                        <textarea class="form-control" v-html="body" required></textarea>
                        <button class="btn btn-sm btn-primary">Update</button>
                        <button class="btn btn-sm btn-link" type="button" @click="cancel">Cancel</button>
                    </div>
                </form>
            </div>
            <div v-else class="body" v-html="body"></div>
        </div>

        <div class="card-footer level" v-if="canUpdate">
            <button class="btn btn-sm btn-info mr-1" @click="editing=true">Edit</button>
            <button class="btn btn-sm btn-danger" @click="destroy">Delete</button>
        </div>
    </div>
</template>
<script>
    import Favorite from './Favotire';
    import moment from 'moment';

    export default {
        props: ['data'],
        components: {Favorite},
        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body,
                originalBody: this.data.body
            }
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {
                    body: this.body
                }).then(response => {
                    flash('Updated!');
                    this.editing = false;
                }).catch(error => {
                    flash(error.response.data, 'danger');
                });
            },
            cancel() {
                this.editing=false;
                this.body = this.originalBody;
            },
            destroy() {
                axios.delete('/replies/' + this.data.id);
                this.$emit('deleted', this.data.id)
            }

        },
        computed: {
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },
            signedIn() {
                // var isSignedIn = document.head.querySelector('meta[name="signedIn"]');
                // console.log(isSignedIn, 'new val');

                return window.App.signedIn;
            },
            canUpdate() {
                return this.authorized(user => this.data.user_id == window.App.user.id);
                // return this.data.user_id == window.App.user.id;
            }
        }
    }
</script>

<style>
</style>
