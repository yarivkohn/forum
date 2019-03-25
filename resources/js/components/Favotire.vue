<template>
    <span>
        <button type="submit" class="btn btn-link" @click="toggle">
            <i :class="computedClass"></i>
        </button>
        <span v-text="count"></span>
    </span>

</template>

<script>
    export default {
        props: ['reply'],
        data() {
            return {
                count : this.reply.favoritesCount,
                active : this.reply.isFavorited
            }
        },
        methods : {
            toggle() {
                console.log(this.active);
                return this.active ? this.destroy() : this.create();
                },
            create() {
                axios.post(this.endPoint);
                this.active = true;
                this.count++;

            },
            destroy() {
                axios.delete(this.endPoint);
                this.active = false;
                this.count--;
            }
        },
        computed: {
            computedClass() {
                // return ['btn', this.active? 'btn-primary' : 'btn-default'];
                return ['fa-heart', this.active? 'fas heart-red' : 'far heart-black'];
            },
            endPoint() {
                return '/replies/' + this.reply.data.id + '/favorites';
            }
        }

    }
</script>