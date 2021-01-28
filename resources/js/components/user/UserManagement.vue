<template>
    <div>
        <v-card>
        <v-card-title primary-title>
            <v-toolbar flat>
                <v-toolbar-title>User Management</v-toolbar-title>
                <v-spacer/>
                <v-btn color="primary" dark class="mb-2 ma-1" @click="userAE='true'">
                    <v-icon>mdi-plus</v-icon> ADD
                </v-btn>
                <v-menu left bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn color="primary" dark class="mb-2 ma-1" v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon> EXCEL
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :key="1" @click.stop="openDialog('import_office')">
                            <v-icon class="ma-1">mdi-file-upload-outline</v-icon> Import
                        </v-list-item>
                        <v-list-item :key="2" @click.stop="openDialog('export_office')">
                            <v-icon  class="ma-1">mdi-file-export-outline</v-icon> Export
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-toolbar>
        </v-card-title>
            <v-data-table
            :headers="headers"
            :items="users"
            :items-per-page="10"
            :search="search"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-text-field prepend-inner-icon="mdi-magnify" v-model="search" label="Search" class="mx-4"/>
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="editUser(item)">mdi-pencil </v-icon>
                <v-icon small @click="deleteUser(item)"> mdi-delete </v-icon>
            </template>

        </v-data-table>
        </v-card>

        <v-row justify="center">
            <v-dialog v-model="userAE" persistent max-width="1000px">
                <v-card>
                <v-card-title class="headline">{{ formTitle }}</v-card-title>
                <v-card-text>
                <v-container>
                    <ValidationObserver ref="observer" v-slot="{ valid }">
                    <v-form ref="form" lazy-validation>
                    <v-row>
                    <v-col cols="12" sm="12" md="12">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-radio-group
                        v-model="form.role_id"
                        row
                        :mandatory="true"
                        prepend-icon="mdi-account-box-multiple"
                        label="Role*: "
                        single-line
                        required
                        >
                            <v-radio label="Adminstrator" value=1></v-radio>
                            <v-radio label="User" value=2></v-radio>
                            <v-radio label="GO" value=3></v-radio>
                        </v-radio-group>
                        </ValidationProvider>
                    </v-col>
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
                        <v-radio-group
                        v-model="form.gender"
                        row
                        :mandatory="true"
                        prepend-icon="mdi-gender-male-female"
                        label="Gender: "
                        single-line
                        required
                        >
                            <v-radio label="Male" value=1></v-radio>
                            <v-radio label="Female" value=2></v-radio>
                        </v-radio-group>
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
                    <v-col cols="3">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-text-field v-model="form.id_number" label="ID Number*" :error-messages="errors"
                        :success="valid" prepend-icon="mdi-identifier" required></v-text-field>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="3">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-select
                            v-model="form.office_id"
                            :items="offices"
                            item-text="name"
                            item-value="id"
                            label="Office"
                            prepend-icon="mdi-office-building-outline"
                            :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                            required
                            :error-messages="errors"
                            :success="valid"
                        ></v-select>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="6">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-radio-group
                        v-model="form.is_active"
                        row
                        :mandatory="true"
                        prepend-icon="mdi-account-check"
                        label="Is Active: "
                        single-line
                        required
                        >
                            <v-radio label="Active" value=1></v-radio>
                            <v-radio label="Inactive" value=0></v-radio>
                        </v-radio-group>
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
                        <v-row justify="end">
                        <v-btn color="primary" class="mb-5 mt-10 ma-5" @click="close()">Close</v-btn>
                        <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" @click="save"> SAVE </v-btn>
                    </v-row>
                    </v-form>
                    </ValidationObserver>
                </v-container>
                </v-card-text>
                </v-card>
            </v-dialog>
        </v-row>

        <v-row justify="center">
            <v-dialog v-model="dialogDelete" persistent max-width="450px">
                <v-card color="grey lighten-2">
                    <v-card-title class="headline">
                        <v-icon class="mr-2" size="30px">mdi-delete-circle</v-icon> Delete User
                    </v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this user from the list? <br> <strong>- {{ form.username }}</strong>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" text @click="dialogDelete = false">
                            Cancel
                        </v-btn>
                        <v-btn color="primary darken-1" text @click.prevent="deleteExistingUser">
                            Confirm
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';

export default {
    components: { ValidationProvider, ValidationObserver },
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
        request(){
            return this.$store.state.snackbars.request;
        }
    },
    data(){
        return {
            headers: [
                { text: 'Username', value: 'username', sortable: false },
                { text: 'Fullname', value: 'full_name', sortable: false },
                { text: 'Office', value: 'office.name', sortable: false },
                { text: 'ID Number', value: 'id_number', sortable: false },
                { text: 'Is Active', value: 'is_active', sortable: false },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
            search: '',
            userAE: false,
            menu: false,
            dialogDelete: false,
            valid: false,
            btnloading: false,
            form: {
                role_id: '',
                first_name: '',
                middle_name: '',
                last_name: '',
                suffix: '',
                gender: '',
                birthday: '',
                id_number: '',
                office_id: '',
                is_active: '',
                username: '',
                password:'',
            },
            defaultItem: {
                role_id: '',
                first_name: '',
                middle_name: '',
                last_name: '',
                suffix: '',
                gender: '',
                birthday: '',
                id_number: '',
                office_id: '',
                is_active: '',
                username: '',
                password:'',
            },
            editedIndex: -1,
        }
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
        editUser (item) {
            this.editedIndex = this.users.indexOf(item)
            this.form = Object.assign({}, item)
            this.userAE = true
        },
        deleteUser (item) {
            this.editedIndex = this.users.indexOf(item)
            this.form = Object.assign({}, item)
            this.dialogDelete = true
        },
        close () {
            this.userAE = false
            this.$nextTick(() => {
            this.form = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
            })
        },
        deleteUserConfirm () {
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
            if (this.editedIndex > -1) { //edit
                this.btnloading = true;
                    this.$store.dispatch('updateExistingUser', this.form).then(() => {
                        if(this.request.status == 'SUCCESS') {
                            this.$store.dispatch('setSnackbar', {
                                type: 'success',
                                message: this.request.message,
                            })
                            .then(() => {
                                Object.assign(this.defaultItem, this.form)
                                this.btnloading = false;
                                this.$store.dispatch('getAllUsers');
                            });
                        } else if(this.request.status == 'FAILED') {
                            this.$store.dispatch('setSnackbar', {
                                type: 'error',
                                message: this.request.message,
                            })
                            .then(() => {
                                this.btnloading = false;
                            });
                        }
                    });
            } else { //add
                this.btnloading = true;
                this.$store.dispatch("addNewUser", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })

                        .then(() => {
                            this.btnloading = false;
                            this.$refs.form.reset();
                            this.$refs.observer.reset();
                            this.$store.dispatch('getAllUsers');
                        });
                    } else if (this.form_requests.request_status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
            }
            this.close()
        },
        deleteExistingUser(){
                this.$store.dispatch('deleteExistingUser', this.form.id).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.$store.dispatch('getAllUsers');
                        });
                    } else if (this.request.status == 'FAILED')  {
                        this.$store.dispatch('setSnackbar', {
                            type: 'success',
                            message: this.request.message,
                        })
                        .then(() => { });
                    }
                    this.dialogDelete = false;
                });
            },
    },
    mounted(){
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('getAllUsers');
        this.$store.commit('SET_TYPES', null);
    }
}
</script>