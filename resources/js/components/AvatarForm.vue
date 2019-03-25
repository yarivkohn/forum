<template>
    <div>
        <h1 v-text="profileUser.name"></h1>
        <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
            <image-upload name="avatar" @loaded="onLoad"></image-upload>
        </form>
        <img :src="avatar" width="50" height="50" alt="profileUser.name">

    </div>
</template>

<script>
    import ImageUpload from './ImageUpload.vue';
    export default {
        props: ['user'],
        components : {ImageUpload},
        data() {
            return {
                profileUser: this.user,
                avatar: '/storage/' + this.user.avatar_path
            }
        },
        computed: {
            canUpdate() {
                return this.authorized(user => user.id === this.profileUser.id);
            }
        },
        methods: {
            onLoad(avatar) {
                this.avatar = avatar.src;
                this.persist(avatar.file);
            },
            persist(avatar) {
                let data = new FormData();
                data.append('avatar', avatar);
                axios.post(`/api/users/${this.profileUser.name}/avatar`, data).then(() => {
                    flash('Avatar uploaded')
                });
            }
        }
    }
</script>