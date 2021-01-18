<template>
    <div>
        <v-card>
            <v-card-title primary-title>
                <v-toolbar flat>
                    <v-toolbar-title>Office List Report</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                <v-btn color="primary" dark class="mb-2 ma-1" @click.stop="openDialog('new_office')">
                <v-icon>mdi-plus</v-icon>ADD
                </v-btn>

                <v-menu left bottom>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="primary"
                        dark
                        class="mb-2 ma-1"
                        v-bind="attrs"
                        v-on="on"
                        >
                        <v-icon>mdi-dots-vertical</v-icon> EXCEL
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :key="1" @click="() => {}"> <v-icon class="ma-1">mdi-file-upload-outline</v-icon> Import </v-list-item>
                        <v-list-item :key="2" @click="() => {}"> <v-icon  class="ma-1">mdi-file-export-outline</v-icon> Export </v-list-item>
                    </v-list>
                </v-menu>

                </v-toolbar>
            </v-card-title>
            <v-data-table
                :headers="headers"
                :items="offices"
                :items-per-page="10"
                :search="search"
                class="elevation-1"
            >
                <template v-slot:top>
                    <v-text-field v-model="search" label="Search" class="mx-4"/>
                </template>

                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2" @click="editOffice(item)">mdi-pencil </v-icon>
                    <v-icon small @click="deleteConfirmationDialog(item)"> mdi-delete </v-icon>
                </template>

            </v-data-table>
        </v-card>

        <v-row justify="center">
            <v-dialog
                v-model="delete_dialog"
                persistent
                max-width="450px"
            >
                <v-card color="grey lighten-2">
                    <v-card-title class="headline">
                        <v-icon class="mr-2" size="30px">mdi-delete-circle</v-icon> Delete Office
                    </v-card-title>
                    <v-card-text>
                        Are you sure you want to delete office from the list? <br> <strong>- {{ delete_info.name }}</strong>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary darken-1"
                            text
                            @click="delete_dialog = false"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="primary darken-1"
                            text
                            @click.prevent="deleteOffice"
                        >
                            Confirm
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>

        <office-table-modal
            @close-dialog="closeDialog"
            :dialog="dialog"
            v-if="items && dialog == true"
            :selected_office="items"
        />

    </div>

</template>

<script>

import OfficeTableModal from './components/OfficeTableModal';
import { mapGetters, mapActions } from "vuex";
import { colors } from '../../constants';

export default {
    components: {OfficeTableModal},
    data() {
        return {
            headers: [
                { text: 'Office', value: 'name' },
                { text: 'Office Code', value: 'office_code' },
                { text: 'Contact Number', value: 'contact_number' },
                { text: 'Contact Email', value: 'contact_email' },
                { text: 'Action', value: 'actions' },
            ],
            search: '',
            dialog: false,
            dialog_for: 'new_office',
            items: '',
            delete_dialog: false,
            delete_info: {
                id: '',
                name: ''
            }
        }
    },
    computed: {
        ...mapGetters(['datatable_loader']),
        offices() {
            return this.$store.state.offices.offices;
        },
        form_requests(){
            return this.$store.state.snackbars.form_requests;
        }
    },
    methods: {
        editOffice(item){
            //console.log("gg", item);
            //this.dialog = true;
            var mode = 'edit_office';
            item.form_mode = mode;
            this.items = item;
            //this.dialog = true;
            this.openDialog(mode);
            console.log(this.items);
        },
        deleteConfirmationDialog(item){
            console.log("gg", item);
            //this.delete_info = [];
            Object.assign(this.delete_info, item);
            this.delete_dialog = true;

            console.log(this.delete_info);
        },
        deleteOffice(){
            console.log('gg', this.delete_info.id);
            this.$store.dispatch('deleteOffice', this.delete_info.id).then(() => {
                if(this.form_requests.request_status == 'SUCCESS') {
                    this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: this.form_requests.status_message,
                        color: '#43A047',
                        icon: 'mdi-check-bold',
                    })
                    .then(() => {
                        //this[this.button_loader] = false
                        //this.button_loader = null;
                        //this.$refs.form.reset();
                        //this.$refs.observer.reset();

                        this.$store.dispatch('getOffices');


                    });

                } else {
                    this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: this.form_requests.status_message,
                        color: '#D32F2F',
                        icon: 'mdi-close-thick',
                    })
                    .then(() => {
                        //this[this.button_loader] = false
                        //this.button_loader = null;

                    });
                }
            });
        },
        openDialog(key){
            switch (key) {
                case 'new_office':
                    this.items = {
                        id: '',
                        name: '',
                        address: '',
                        office_code: '',
                        contact_number: '',
                        contact_email: '',
                        form_mode: 'new_office'
                    };
                    this.dialog = true
                    break;
                case 'edit_office':
                    this.dialog = true
                    break;
            }
        },
        closeDialog(){
            //console.log("jj" ,item);
            this.dialog = false;
        }
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
    }
}
</script>