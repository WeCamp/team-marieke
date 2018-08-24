<template>
    <div>
        <h1>Players to challenge</h1>

        <div v-if="state !== 'initial'"
            class="alert" :class="`alert-${this.state}`">
            {{ message }}
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
        props: [
            'usernameOfSignedOnUser',
        ],

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
                axios.post('http://localhost:8080/challengeplayer', {
                    user_to_challenge: this.usernameOfChallengedToDuel
                }, {
                    headers: {
                        Player: this.usernameOfSignedOnUser,
                    }
                }).then(() => this.state = 'waiting');
            },
        },

        computed: {
            message() {
                if (this.state === 'waiting') {
                    return 'waiting for challenge acceptance';
                } else if (this.state === 'accepted') {
                    return 'challenge has been accepted by ' + this.usernameOfChallengedToDuel;
                } else if (this.state === 'rejected') {
                    return 'challenge denied!';
                }
            },
        },
    };
</script>
