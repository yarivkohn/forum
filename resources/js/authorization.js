let user = window.App.user;

module.exports =  {
    updateReply(reply) {
        console.log(user.id);
        return reply.owner.id === user.id;
    }
};