<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <ul>
                    <li v-for="task in tasks" v-text="task.body"></li>
                </ul>
                <label for="new-task">Add Task</label>
                <input id="new-task" class="form-control" type="text" v-model="newTask" @blur="addTask"
                       @keydown="tapParticipants">
                <span v-if="activePeer" v-text="activePeer.name + ' is typing...'"></span>
            </div>
            <div class="col-md-4">
                <h4>Active Participants</h4>
                <ul>
                    <li v-for="participant in participants" v-text="participant.name"></li>
                </ul>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        props: ['dataProject'],
        data() {
            return {
                project: this.dataProject,
                tasks: this.dataProject.tasks,
                newTask: '',
                activePeer: false,
                typingTimer: false,
                participants: []
            }
        },
        created() {
            window.Echo.join('tasks.' + this.project.id)
                .here(users => {
                    this.participants = users;
                    console.log('here');
                    console.log(users);
                })
                .joining(user => {
                    console.log('Join');
                    console.log(user);
                })
                .leaving(user => {
                    console.log('leave');
                    console.log(user);
                })
                .listen('TaskCreated', e => {
                    this.tasks.push(e.task);
                    flash('List was updated');
                })
                .listenForWhisper('typing', e => {
                    this.activePeer = e;
                    if (this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }
                    this.typingTimer = setTimeout(() => {
                        this.activePeer = false;
                    }, 3000);
                });
        },
        methods: {
            addTask() {
                if (this.newTask) {
                    axios.post('/api/projects/' + this.project.id, {body: this.newTask})
                        .then(response => {
                            this.tasks.push(JSON.parse(response.config.data));
                            this.newTask = '';
                            this.activePeer = false;
                        });
                }
            },
            tapParticipants() {
                window.Echo.join('tasks.' + this.project.id)
                    .whisper('typing', {
                        name: window.App.user.name // this.project.user.name,
                    });
            }
        }
    }
</script>