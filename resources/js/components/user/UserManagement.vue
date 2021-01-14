<template>
    <v-card flat>
        <v-row align="center">
            <v-col cols="12" sm="6">
                <v-card-title primary-title>
                    User Management
                </v-card-title>
             </v-col>
             <v-col cols="12" sm="6" class="buttons-alignment">
                 <v-btn color="cyan" dark><v-icon>mdi-plus</v-icon> Add</v-btn>
                 <v-btn color="lime" dark><v-icon>mdi-import</v-icon> Import</v-btn>
                 <v-btn color="light-green" dark><v-icon>mdi-export</v-icon> Export</v-btn>
            </v-col>
        </v-row>
        <v-card-text>
            <v-data-table
            :headers="headers"
            :items="users"
            class="elevation-1"
            :search="search"
            hide-default-footer
            >
            <template v-slot:top>
            </template>
                <template v-slot:top>
                    <v-text-field
                        v-model="search"
                        label="Search"
                        class="mx-4"
                    />
                </template>
                <template v-slot:item.actions="{ item }">
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
                </template>
            </v-data-table>
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters, mapActions } from "vuex";

export default {
    data(){
        return{
            search: '',
            headers: [
                { text: 'Username', value: 'username', sortable: false },
                { text: 'Fullname', value: 'full_name', sortable: false },
                { text: 'Office', value: 'office.name', sortable: false },
                { text: 'ID Number', value: 'id_number', sortable: false },
                { text: 'Is Active', value: 'is_active', sortable: false },
                // { text: 'Division', value: 'division', sortable: false },
                // { text: 'Unit', value: 'unit', sortable: false },
                // { text: 'Sector', value: 'sector', sortable: false },
                // // { text: 'Role', value: 'role_id', sortable: false },
                // // { text: 'Permission', value: 'permission', sortable: false },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
            items: [],
        }
    },
    computed: {
        offices() {
            return this.$store.state.offices.offices;
        },
        users() {
            return this.$store.state.users.all_users;
        },
        // users_complete() {
        //     return this.$store.state.users.all_users_complete;
        // },
    },
    methods: {
    //     getUserMasterList() {
    //         axios.get('/api/all_users')
    //         .then(response => {
    //             this.items = response.data;
    //         });
    //     }
    },
    mounted() {
        console.log(this.users);
        this.$store.dispatch('unsetLoader');
        // this.getUserMasterList();
    }
}
</script>

<style scoped>
.buttons-alignment {
    text-align: end;
}
</style>