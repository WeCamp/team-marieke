<template>
    <div id="app">
        <div v-if="challengingPlayer !== null" class="alert alert-waiting">
            You have been challenged to a duel by <strong>{{challengingPlayer}}</strong>!
        </div>

        <div v-if="usernameOfSignedOnUser !== null">
            <p>Currently signed on as <b>{{ usernameOfSignedOnUser }}</b></p>
            <players-to-challenge :username-of-signed-on-user="usernameOfSignedOnUser"></players-to-challenge>
        </div>
        <div v-else>
            <sign-on :username-of-signed-on-user.sync="usernameOfSignedOnUser"></sign-on>
        </div>

        <duel
            :player="usernameOfSignedOnUser">
        </duel>
    </div>
</template>

<script>
    import axios from 'axios';
    import SignOn from './components/SignOn.vue';
    import PlayersToChallenge from './components/PlayersToChallenge';
    import Duel from './components/Duel';

    export default {
        watch: {
            usernameOfSignedOnUser(newValue, oldValue) {
                if (newValue) {
                    axios.get('http://localhost:8080/challengeofplayer', {headers: {Player: newValue}})
                        .then(response => {
                            const challengerUsername = response.data.challenger_username;
                            if (challengerUsername) {
                                this.challengingPlayer = challengerUsername;
                            }
                        });
                }
            }
        },
        components: {
            SignOn,
            PlayersToChallenge,
            Duel,
        },
        data() {
            return {
                usernameOfSignedOnUser: null,
                challengingPlayer: null,
            };
        },
        mounted() {
            window.ws.addEventListener("message", (e) => {
                const data = JSON.parse(e.data);
                this.challengingPlayer = data.challenging_player;
            });
        },
    };
</script>
