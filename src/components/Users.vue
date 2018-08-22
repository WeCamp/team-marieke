<template>
    <div>
        <h1>Users</h1>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users"
                    :key="user.username">
                    <td>{{ user.username }}</td>
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
        axios.get('http://localhost:8080/').then(response => {
            this.users = response.data;
        });
    },
};
</script>
