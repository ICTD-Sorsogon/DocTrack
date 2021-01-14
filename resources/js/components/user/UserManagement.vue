<template>
    <v-card flat>
        <v-row align="center">
            <v-col cols="12" sm="6">
                <v-card-title primary-title>
                    User Management
                </v-card-title>
             </v-col>
             <v-col cols="12" sm="6" class="buttons-alignment">
                 <v-dialog
                v-model="dialog"
                persistent
                max-width="1000px"
                >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn color="cyan" dark v-bind="attrs" v-on="on"><v-icon>mdi-plus</v-icon> Add</v-btn>
                </template>
                <v-card>
                    <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                    <v-container>
                        <v-row>
                        <v-col cols="12" sm="12" md="12">
                            <span><v-icon>mdi-account</v-icon>Full Name</span>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <v-text-field label="First Name*" required ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <v-text-field label="Middle Name*" required ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <v-text-field label="Last Name*" required ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <v-text-field label="Suffix (Optional)"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-select :items="['Male', 'Female']" label="Gender*" required prepend-icon="mdi-gender-male-female"></v-select>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-menu
                                ref="menu"
                                v-model="menu"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="form.birthdate"
                                    label="Birthday date*"
                                    prepend-icon="mdi-calendar"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    required
                                ></v-text-field>
                                </template>
                                <v-date-picker
                                ref="picker"
                                v-model="form.birthdate"
                                :max="new Date().toISOString().substr(0, 10)"
                                min="1950-01-01"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="ID Number*" prepend-icon="mdi-identifier" required></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Office*" prepend-icon="mdi-office-building-outline" required></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Division*" prepend-icon="mdi-account-group-outline" required></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Unit*" prepend-icon="mdi-vector-square" required></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Sector*" prepend-icon="mdi-chart-pie" required></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field label="Is Active*" prepend-icon="mdi-account-check" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field label="Username*" prepend-icon="mdi-account-circle" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field label="Password*" prepend-icon="mdi-account-key" type="password" required></v-text-field>
                        </v-col>
                        </v-row>
                    </v-container>
                    <small>*indicates required field</small>
                    </v-card-text>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken" @click="dialog = false">Close</v-btn>
                    <v-btn color="green darken" @click="dialog = false">Save</v-btn>
                    </v-card-actions>
                </v-card>
                </v-dialog>

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
            dialog: false,
            menu: false,
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
            form: {
                form_type: 'new_document',
                tracking_id: '',
                document_title: '',
                document_type: '',
                originating_office: '',
                sender_name: '',
                page_count: '',
                attachment_page_count: '',
                is_external: false,
                birthdate: '',
                time_filed: '',
                remarks: '',
            },
            editedIndex: -1,
            editedItem: {
                username: '',
                full_name: '',
                office: '',
                id_number: 0,
                is_active: 0,
            },
        }
    },
    computed: {
        offices() {
            return this.$store.state.offices.offices;
        },
        users() {
            return this.$store.state.users.all_users;
        },
         formTitle () {
            return this.editedIndex === -1 ? 'Add User' : 'Edit User'
        },
        // users_complete() {
        //     return this.$store.state.users.all_users_complete;
        // },
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
      menu (val) {
        val && setTimeout(() => (this.$refs.picker.activePicker = 'YEAR'))
      },
    },
    methods: {
        close () {
            this.dialog = false
            this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
            })
        },
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