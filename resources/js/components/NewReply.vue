<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
            <textarea
                    id="body"
                    class="form-control"
                    placeholder="Have something to say?"
                    rows="5"
                    required
                    v-model="body"></textarea>
            </div>
            <button type="submit" class="btn btn-default" @click="addReply" v-if="body != ''">Post</button>
        </div>
        <div v-else>
            <p class="text-center">Please <a href="/login/">sign in</a> to participate in this thread</p>
        </div>
    </div>
</template>

<script>
    import 'jquery.caret';
    import 'at.js';
    export default {
        data() {
            return {
                body: '',
            }
        },
        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', {body: this.body})
                    .then(response => {
                        this.body = '';
                        flash('Your reply has been posted.');
                        this.$emit('created', response.data);
                    })
                    .catch(function (errors) {
                        flash(errors.response.data, 'danger');
                    });
            }
        },
        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },
        mounted() {
            $('#body').atwho(
                {
                    at: '@',
                    delay: 750,
                    callbacks: {
                        remoteFilter: function(query, callback) {
                            $.getJSON('/api/users', {name: query}, function(username){
                               callback(username);
                            });
                        }
                    }
                });
        }

    }
</script>