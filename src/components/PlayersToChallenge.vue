<template>
    <div>
        <h1>Players to challenge</h1>

        <div class="alert alert-waiting" v-if="state === 'waiting'">
            Your challenge is awaiting a response
        </div>

        <div class="alert alert-accepted" v-if="state === 'accepted'">
            Your challenge has been accepted by {{ usernameOfChallengedToDuel }}
        </div>

        <div class="alert alert-rejected" v-if="state === 'rejected'">
            Your challenge has been rejected
        </div>

        <table v-if="state === 'initial'">
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users"
                    :key="user.username">
                    <td>
                        <button type="button" @click="challenge(user.username)">{{ user.username }}</button>
                        <strong v-if="usernameOfChallengedToDuel === user.username">challenged</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                users: [],
                usernameOfChallengedToDuel: null,
                state: 'initial', // initial -> waiting -> accepted
                                  //                    -> rejected
            };
        },

        mounted() {
            axios.get('http://localhost:8080/playerstochallenge', {
                headers: {
                    Player: this.usernameOfSignedOnUser,
                }
            }).then(response => {
                this.users = response.data;
            });
        },

        methods: {
            challenge(username) {
                this.usernameOfChallengedToDuel = username;
                this.state = 'waiting';
                axios.post('http://localhost:8080/challenge', {username: this.usernameOfChallengedToDuel})
                    .then(() => this.state = 'waiting');
            },
        },

        props: [
            'usernameOfSignedOnUser',
        ],
    };
</script>

<style scoped>
.alert {
    padding: 1em;
    border: 1px solid black;
    border-radius: 1em;
}

.alert-waiting {
    background-color: #fc6;
}

.alert-accepted {
    background-color: #cf6;
}

.alert-rejected {
    background-color: #f99;
}
</style>
