<template>
    <div v-if="challengingPlayer !== null" class="alert alert-waiting">
        You have been challenged to a duel by <strong>{{challengingPlayer}}</strong>!
        <br><br>
        <button @click="reply(true)">I graciously accept!</button>
        <button @click="reply(false)">I'm a coward</button>
    </div>
</template>

<script>
export default {
    props: ['challengingPlayer', 'challengedPlayer'],

    mounted() {
        window.ws.addEventListener('message', event => {
            const data = JSON.parse(event.data);
            if (data.type === 'challenge_response') {
                const { accept, duel_id } = data;
                if (accept) {
                     this.$emit('startDuel', { duel_id });
                } else {
                    this.$emit('update:challengingPlayer', null);
                }
            }
        });
    },

    methods: {
        reply(acceptance) {
            window.ws.send(JSON.stringify({
                type: 'challenge_response',
                challengedPlayer: this.challengedPlayer,
                challengingPlayer: this.challengingPlayer,
                accept: acceptance,
            }));
        }
    },
};
</script>
