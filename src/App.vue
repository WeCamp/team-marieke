<template>
    <div id="app">
        <challenge-notification
            :challengingPlayer="challengingPlayer"
            :myself="usernameOfSignedOnUser">
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
    import SignOn from './components/SignOn.vue';
    import PlayersToChallenge from './components/PlayersToChallenge';
    import ChallengeNotification from './components/ChallengeNotification';

    export default {
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
            window.ws.addEventListener("message", (e) => {
                const data = JSON.parse(e.data);
                this.challengingPlayer = data.challenging_player;
            });
        },
    };
</script>
