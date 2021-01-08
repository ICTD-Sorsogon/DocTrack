<template>
    <v-card flat>
        <v-card-title primary-title>
            User Management
        </v-card-title>
        <v-card-text>
            <v-data-table
                :headers="headers"
                :item="items"
                hide-default-footer
            >
            <!-- <template v-slot:[`item.actions`] = "{ item }">
            <v-icon
                small
                class="mr-2"
                @click="editItem(item)"
            >
                mdi-pencil
            </v-icon>
            <v-icon
                small
                @click="deleteItem(item)"
            >
                mdi-delete
            </v-icon>
            </template> -->
            </v-data-table>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            headers: [
                { text: 'Username', value: 'username', sortable: false },
                { text: 'Name', value: 'username', sortable: false },
                // { text: 'Office', value: 'office.name', sortable: false },
                // { text: 'Division', value: 'division', sortable: false },
                // { text: 'Unit', value: 'unit', sortable: false },
                // { text: 'Sector', value: 'sector', sortable: false },
                { text: 'Role', value: 'role_id', sortable: false },
                // { text: 'Permission', value: 'permission', sortable: false },
                // { text: 'Actions', value: 'actions', sortable: false },
            ],
            items: null,
        }
    },
    // computed: {
    //     offices() {
    //         return this.$store.state.offices.offices;
    //     },
    //     users() {
    //         return this.$store.state.users.all_users;
    //     },
    // },
    methods: {
        getUserMasterList() {
            axios.get('all_users_complete')
            .then(response => {
                this.items = response.data;
            });
        }
    },
    mounted() {
        this.getUserMasterList();
        this.$store.dispatch('unsetLoader');

    }
}
</script>