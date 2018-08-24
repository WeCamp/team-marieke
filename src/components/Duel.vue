<template>
    <div class="duel">
        <h1>Currently Duelling</h1>
        <p>
            Pick your move: <br>

            <button :disabled="myMove" @click="play('rock')">Rock</button>
            <button :disabled="myMove" @click="play('paper')">Paper</button>
            <button :disabled="myMove" @click="play('scissors')">Scissors</button>
        </p>
    </div>
</template>

<script>
export default {
    props: ['player'],

    data() {
        return {
            myMove: null,
        };
    },
    mounted() {
        window.ws.addEventListener('message', event => {
            const data = JSON.parse(event.data);
            if (data.type === 'result') {
                this.$emit('end', data.result);
            }
        });
    },

    methods: {
        play(move) {
            this.myMove = move;

            window.ws.send(JSON.stringify({
                type: 'move',
                username: this.player,
                move,
            }));
        },
    },
};
</script>
