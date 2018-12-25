<template>
    <li class="dropdown" v-if="notifications.length">
        <a href="#" class="nav-link" data-toggle="dropdown">
            <span class="fas fa-bell"></span>
        </a>
        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a class="dropdown-item" :href="notification.data.link" v-text="notification.data.message" @click="markAsRead(notification)"></a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                notifications: []
            };
        },
        created() {
            axios.get('/profile/' + window.App.user.name + '/notifications')
                .then(response => {
                    this.notifications = response.data;
                }).catch(error => console.log(error));
        },
        methods: {
            //"/profile/{$user->name}/notifications/".$user->unreadNotifications->first()->id
            markAsRead(notification) {
                let endPoint = "/profile/" + window.App.user.name + "/notifications/" + notification.id;
                axios.delete(endPoint);
            }
        }
    }
</script>