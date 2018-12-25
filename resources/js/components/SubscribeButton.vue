<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
    export default {
        props: ['active'],
        data() {
            return {
                isActive : this.active
            }
        },
        computed :{
            classes() {
                return ['btn', this.isActive? 'btn-danger' : 'btn-success' ]
            }
        },
        methods: {
            subscribe() {
                let requestType = this.isActive ? 'delete' : 'post';
                axios[requestType](location.pathname + '/subscription')
                    .then(() => {
                    this.isActive = !this.isActive;
                });

            }
        }

    }
</script>