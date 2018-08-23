<template>
    <div>
        <h1>Players to challenge</h1>

        <table>
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
                this.$emit('update:usernameOfChallengedToDuel', username);
            }
        },

        props: [
            'usernameOfSignedOnUser',
            'usernameOfChallengedToDuel',
        ],
    };
</script>
