<template>
    <div id="app">
        <div v-if="usernameOfSignedOnUser !== null">
            <p>Currently signed on as <b>{{ usernameOfSignedOnUser }}</b></p>
            <players-to-challenge :username-of-challenged-to-duel.sync="usernameOfChallengedToDuel" :username-of-signed-on-user="usernameOfSignedOnUser"></players-to-challenge>
        </div>
        <div v-else>
            <sign-on :username-of-signed-on-user.sync="usernameOfSignedOnUser"></sign-on>
        </div>
    </div>
</template>

<script>
    import SignOn from './components/SignOn.vue';
    import PlayersToChallenge from './components/PlayersToChallenge';

    export default {
        components: {
            SignOn,
            PlayersToChallenge,
        },
        data() {
            return {
                usernameOfSignedOnUser: null,
                usernameOfChallengedToDuel: null,
            };
        },
    };
</script>
