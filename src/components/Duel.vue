<template>
    <div class="duel">
        <h1>Currently Duelling</h1>
        <p>
            Pick your move: <br>

            <button :disabled="myMove" @click="play(0)">Rock</button>
            <button :disabled="myMove" @click="play(1)">Paper</button>
            <button :disabled="myMove" @click="play(2)">Scissors</button>
        </p>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['player', 'duelId'],

    data() {
        return {
            myMove: null,
        };
    },
    mounted() {
        window.ws.addEventListener('message', event => {
            const data = JSON.parse(event.data);
            if (data.type === 'duel_result') {
                this.$emit('duelResult', data);
            }
        });
    },

    methods: {
        play(move) {
            this.myMove = move;
            axios.post('http://localhost:8080/move', { move, duel_id: this.duelId }, {
                headers: {
                    Player: this.player,
                },
            });
        },
    },
};
</script>
