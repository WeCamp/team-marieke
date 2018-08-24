<template>
    <div id="app">
        <challenge-notification
            :challengingPlayer="challengingPlayer"
            :challengedPlayer="usernameOfSignedOnUser">
        </challenge-notification>

        <div v-if="usernameOfSignedOnUser !== null">
            <p>Currently signed on as <b>{{ usernameOfSignedOnUser }}</b></p>
            <players-to-challenge :username-of-signed-on-user="usernameOfSignedOnUser"></players-to-challenge>
        </div>
        <div v-else>
            <sign-on :username-of-signed-on-user.sync="usernameOfSignedOnUser"></sign-on>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import SignOn from './components/SignOn.vue';
    import PlayersToChallenge from './components/PlayersToChallenge';
    import ChallengeNotification from './components/ChallengeNotification';

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
            ChallengeNotification,
        },
        data() {
            return {
                usernameOfSignedOnUser: null,
                challengingPlayer: null,
            };
        },
        mounted() {
            window.ws.addEventListener('message', event => {
                const data = JSON.parse(event.data);
                if (data.type === 'challenge_to_duel') {
                    this.challengingPlayer = data.challenging_player;
                }
            });
        },
    };
</script>
