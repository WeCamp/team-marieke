<template>
    <div id="app">
        <p>Currently signed on as <b>{{ usernameOfSignedOnUser }}</b></p>
        <users :username-of-signed-on-user.sync="usernameOfSignedOnUser"></users>
    </div>
</template>

<script>
    import Users from './components/Users.vue';

    export default {
    components: { Users },
    data() {
        return {
            usernameOfSignedOnUser: null,
        };
    },
};
</script>
