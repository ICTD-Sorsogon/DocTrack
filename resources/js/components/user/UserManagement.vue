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
                        <v-col cols="12" sm="6" md="3">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.first_name" label="First Name*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-format-text" required ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.middle_name" label="Middle Name*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-format-text" required ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.last_name" label="Last Name*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-format-text" required ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6" md="3">
                            <v-text-field v-model="form.suffix" label="Suffix (Optional)"
                            prepend-icon="mdi-format-text"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-select v-model="form.gender" :items="['Male', 'Female']" label="Gender*" required
                            :error-messages="errors" :success="valid" prepend-icon="mdi-gender-male-female"></v-select>
                            </ValidationProvider>
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
                                <ValidationProvider rules="required" v-slot="{ errors, valid }">
                                <v-text-field
                                    v-model="form.birthday"
                                    label="Birthdate*"
                                    prepend-icon="mdi-calendar"
                                    readonly
                                    :error-messages="errors"
                                    :success="valid"
                                    v-bind="attrs"
                                    v-on="on"
                                    required
                                ></v-text-field>
                                </ValidationProvider>
                                </template>
                                <v-date-picker
                                ref="picker"
                                v-model="form.birthday"
                                :max="new Date().toISOString().substr(0, 10)"
                                min="1950-01-01"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="4">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.id_number" label="ID Number*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-identifier" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="4">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.office_name" label="Office*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-office-building-outline" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <!-- <v-col cols="4">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.division_id" label="Division*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-account-group-outline" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="4">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.unit_id" label="Unit*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-vector-square" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="4">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.sector_id" label="Sector*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-chart-pie" required></v-text-field>
                            </ValidationProvider>
                        </v-col> -->
                        <v-col cols="4">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.is_active" label="Is Active*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-account-check" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.username" label="Username*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-account-circle" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12">
                            <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field v-model="form.password" label="Password*" :error-messages="errors"
                            :success="valid" prepend-icon="mdi-account-key" type="password" required></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        </v-row>
                        </ValidationObserver>
                    </v-container>
                    <small>*indicates required field</small>
                    </v-card-text>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-grey lighten-1" @click="dialog = false">Close</v-btn>
                    <v-btn color="green accent-4" @click="dialog = false">Save</v-btn>
                    </v-card-actions>
                </v-card>
                </v-dialog>

                <v-dialog v-model="dialogDelete" max-width="500px">
                <v-card>
                    <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-grey lighten-1" @click="closeDelete">Cancel</v-btn>
                    <v-btn color="red darken-1" @click="deleteItemConfirm">OK</v-btn>
                    <v-spacer></v-spacer>
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
                <v-text-field v-model="search" label="Search" class="mx-4" />
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
            </v-data-table>
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';

export default {
    data(){
        return{
            search: '',
            dialog: false,
            dialogDelete: false,
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
            // items: [],
            form: {
                first_name: '',
                middle_name: '',
                last_name: '',
                suffix: '',
                gender: '',
                birthday: '',
                id_number: '',
                office_name: '',
                // division_id: '',
                // unit_id: '',
                // sector_id: '',
                is_active: ''
            },
            defaultItem: {
               first_name: '',
                middle_name: '',
                last_name: '',
                suffix: '',
                gender: '',
                birthday: '',
                id_number: '',
                office_name: '',
                // division_id: '',
                // unit_id: '',
                // sector_id: '',
                is_active: ''
            },
            editedIndex: -1,
        }
    },
    components: {
        ValidationProvider,
        ValidationObserver
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
        editItem (item) {
            // if(item.gender == 1){
            //     item.gender = 'Male'
            // }
            // else
            //     item.gender = 'Female'
            this.editedIndex = this.users.indexOf(item)
            this.form = Object.assign({}, item)
            this.dialog = true
            console.log(item);
        },
        deleteItem (item) {
            this.editedIndex = this.users.indexOf(item)
            this.form = Object.assign({}, item)
            this.dialogDelete = true
        },
        close () {
            this.dialog = false
            this.$nextTick(() => {
            this.form = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
            })
        },
        deleteItemConfirm () {
            this.users.splice(this.editedIndex, 1)
            this.closeDelete()
        },
        closeDelete () {
            this.dialogDelete = false
            this.$nextTick(() => {
            this.form = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
            })
        },
        save () {
            if (this.editedIndex > -1) {
                Object.assign(this.users[this.editedIndex], this.users)
            } else {
                this.users.push(this.editedItem)
            }
            this.close()
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