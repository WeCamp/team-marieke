<template>
    <div id="app">
        <p>Currently signed on as <b>{{ usernameOfSignedOnUser }}</b></p>
        <sign-on :username-of-signed-on-user.sync="usernameOfSignedOnUser"></sign-on>
    </div>
</template>

<script>
    import SignOn from './components/SignOn.vue';

    export default {
    components: { SignOn },
    data() {
        return {
            usernameOfSignedOnUser: null,
        };
    },
};
</script>
