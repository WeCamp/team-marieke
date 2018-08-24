<template>
    <div id="app">
        <div v-if="!duelId">
            <challenge-notification
                :challengingPlayer.sync="challengingPlayer"
                :challengedPlayer="usernameOfSignedOnUser"
                @startDuel="(id) => this.duelId = id">
            </challenge-notification>

            <div v-if="usernameOfSignedOnUser !== null">
                <p>Currently signed on as <b>{{ usernameOfSignedOnUser }}</b></p>
                <players-to-challenge
                    :username-of-signed-on-user="usernameOfSignedOnUser"
                    @startDuel="(id) => this.duelId = id">
                </players-to-challenge>
            </div>
            <div v-else>
                <sign-on :username-of-signed-on-user.sync="usernameOfSignedOnUser"></sign-on>
            </div>
        </div>

        <duel v-if="duelId" :player="usernameOfSignedOnUser" :duelId="duelId"></duel>
    </div>
</template>

<script>
    import axios from 'axios';
    import SignOn from './components/SignOn.vue';
    import PlayersToChallenge from './components/PlayersToChallenge';
    import Duel from './components/Duel';
    import ChallengeNotification from './components/ChallengeNotification';

    export default {
        watch: {
            usernameOfSignedOnUser(newValue) {
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
            ChallengeNotification,
        },
        data() {
            return {
                usernameOfSignedOnUser: null,
                challengingPlayer: null,
                duelId: null,
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
