<template>
    <div>
        <v-card>
            <v-card-title primary-title>
                <v-toolbar flat>
                    <v-toolbar-title>Archive List Report</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical/>
                    <v-spacer/>

                    <v-switch
                        v-model="switch1"
                        :label="`${(switch1)?'Group by Office':'View All'}`"
                    ></v-switch>

                    <v-btn color="primary" dark class="mb-2 ma-1" @click.stop="openDialog('new_office')">
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
                :items="offices"
                :items-per-page="10"
                :search="search"
                class="elevation-1"
                show-expand
                :expanded.sync="expanded"
            >
                <template v-slot:top>
                    <v-text-field v-model="search" label="Search" class="mx-4"/>

                    <v-select
                        v-model="sortBy"
                        flat
                        solo-inverted
                        hide-details
                        :items="keys"
                        prepend-inner-icon="mdi-magnify"
                        label="Select office"
                    />
                    <v-select
                        v-model="sortBy"
                        flat
                        solo-inverted
                        hide-details
                        :items="keys"
                        prepend-inner-icon="mdi-magnify"
                        label="Sort by"
                    />

                </template>

                <!--<template v-slot:top>
                    <v-toolbar flat>
                    <v-toolbar-title>Expandable Table</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-switch
                        v-model="singleExpand"
                        label="Single expand"
                        class="mt-2"
                    ></v-switch>
                    </v-toolbar>
                </template>-->
                <template v-slot:expanded-item="{ headers, item }">
                    <td :colspan="headers.length">
                    <!--More info about {{ item }}-->

                    <template>
                        <v-data-table
                            :headers="headers"
                            :items="offices"
                            :items-per-page="5"
                            class="elevation-1"
                        ></v-data-table>
                    </template>


                    <!--<template>
                        <v-data-table
                        :headers="headers"
                        :items="desserts"
                        :items-per-page="5"
                        class="elevation-1"
                        ></v-data-table>
                    </template>-->
                    </td>
                </template>
                <!--rr-->

               <!-- <template v-slot:item.actions="{ item }">
                    <v-row>
                        <v-col cols="12" xs="6" sm="6" md="6" lg="6" xl="6" class="pr-0" style="top:50%; text-align:right;">
                            <v-icon small @click="editOffice(item)" style="margin-right:4px;">mdi-pencil </v-icon>
                        </v-col>
                        <v-col cols="12" xs="6" sm="6" md="6" lg="6" xl="6" class="pl-0" style="top:50%; text-align:left;">
                            <v-icon small @click="deleteConfirmationDialog(item)" style="margin-left:4px;"> mdi-delete </v-icon>
                        </v-col>
                    </v-row>
                </template>-->

                <template v-slot:header>
                    <v-toolbar
                    dark
                    color="blue darken-3"
                    class="mb-1"
                    >
                    <template v-if="$vuetify.breakpoint.mdAndUp">
                        <v-spacer></v-spacer>
                        <v-select
                        v-model="sortBy"
                        flat
                        solo-inverted
                        hide-details
                        :items="keys"
                        prepend-inner-icon=""
                        label="Select office"
                        ></v-select>
                        <v-spacer></v-spacer>
                        <v-select
                            v-model="sortBy"
                            flat
                            solo-inverted
                            hide-details
                            :items="keys1"
                            prepend-inner-icon=""
                            label="Filter by"
                        ></v-select>
                        <v-spacer></v-spacer>
                         <v-select
                            v-model="sortBy"
                            flat
                            solo-inverted
                            hide-details
                            :items="keys2"
                            prepend-inner-icon=""
                            label="Source"
                        ></v-select>
                        <v-spacer></v-spacer>
                        <v-select
                            v-model="sortBy"
                            flat
                            solo-inverted
                            hide-details
                            :items="keys3"
                            prepend-inner-icon=""
                            label="Type"
                        ></v-select>
                        <!--<v-btn-toggle
                        v-model="sortDesc"
                        mandatory
                        >
                        <v-btn
                            large
                            depressed
                            color="blue"
                            :value="false"
                        >
                            <v-icon>mdi-arrow-up</v-icon>
                        </v-btn>
                        <v-btn
                            large
                            depressed
                            color="blue"
                            :value="true"
                        >
                            <v-icon>mdi-arrow-down</v-icon>
                        </v-btn>
                        </v-btn-toggle>-->
                    </template>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        clearable
                        flat
                        solo-inverted
                        hide-details
                        prepend-inner-icon="mdi-magnify"
                        label="Search"
                    ></v-text-field>
                    </v-toolbar>
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
            v-if="office_info && form_dialog == true"
            :form_dialog="form_dialog"
            :selected_office="office_info"
            :dialog_title="dialog_title"
            @close-dialog="closeDialog('form')"
        />

        <excel-dialog
            v-if="dialog_title && excel_dialog == true"
            :excel_dialog="excel_dialog"
            :dialog_title="dialog_title"
            :dialog_for="dialog_for"
            @close-dialog="closeDialog('excel')"
        />

    </div>

</template>

<script>
    import OfficeTableModal from './components/OfficeTableModal';
    import ExcelDialog from './components/ExcelDialog';
    import { mapGetters, mapActions } from "vuex";

    export default {
        components: { OfficeTableModal, ExcelDialog },
        data() {
            return {
                headers: [
                    { text: 'Office Name', value: 'builded_office_name' },
                    { text: '', value: 'data-table-expand' },
                   // { text: 'Action', value: 'actions', align: 'center', },
                ],
                search: '',
                form_dialog: false,
                excel_dialog: false,
                dialog_for: 'new_office',
                dialog_title: '',
                office_info: '',
                delete_dialog: false,
                delete_info: {
                    id: '',
                    name: ''
                },
                switch1: true,
                expanded: [],
                keys: [
                    'Office 1',
                    'Office 2',
                    'Office 3',
                    'Office 4',
                    'Office 5',
                    'Office 6',
                    'Office 7',
                    'Office 8',
                    'All'
                ],
                keys1: [
                    'Tracking ID',
                    'Subject',
                    'Originating Office',
                    'Destination Office',
                    'Sender',
                    'All'
                ],
                keys2: [
                    'External',
                    'Internal'
                ],
                keys3: [
                    'Executive Order',
                    'Provincial Ordinance',
                    'Letter',
                    'Purchase Order',
                    'Salary',
                    'Budget',
                    'Reports',
                    'Draft',
                    'Others'
                ]
            }
        },
        computed: {
            ...mapGetters(['datatable_loader']),
            offices() {
                const offices = this.$store.state.offices.offices;
                offices.forEach(office => {
                    office.builded_office_name = office.office_code + ' - ' + office.name
                });
                return offices;
            },
            request(){
                return this.$store.state.snackbars.request;
            }
        },
        methods: {
            editOffice(item){
                var mode = 'edit_office';
                item.form_mode = mode;
                this.office_info = item;
                this.openDialog(mode);
            },
            deleteConfirmationDialog(item){
                Object.assign(this.delete_info, item);
                this.delete_dialog = true;
            },
            deleteOffice(){
                this.$store.dispatch('deleteOffice', this.delete_info.id).then(() => {
                    if(this.request.status == 'success') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'success',
                            message: this.request.message
                        })
                        .then(() => {
                            this.$store.dispatch('getOffices');
                        });
                    } else if(this.request.status == 'failed') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message
                        })
                        .then(() => { });
                    }
                    this.delete_dialog = false;
                });
            },
            openDialog(key){
                switch (key) {
                    case 'new_office':
                        this.office_info = {
                            id: '',
                            name: '',
                            address: '',
                            office_code: '',
                            contact_number: '',
                            contact_email: '',
                            form_mode: 'new_office'
                        };
                        this.dialog_title = 'Office Details';
                        this.form_dialog = true
                        break;
                    case 'edit_office':
                        this.dialog_title = 'Office Details';
                        this.form_dialog = true
                        break;
                    case 'import_office':
                        this.dialog_for = 'importOfficeList';
                        this.dialog_title = 'Import Office List Via Excel File';
                        this.excel_dialog = true
                        break;
                    case 'export_office':
                        this.dialog_for = 'exportOfficeList';
                        this.dialog_title = 'Export Office List Via Excel File';
                        this.excel_dialog = true
                        break;
                }
            },
            closeDialog(key){
                switch (key) {
                    case 'form':
                        this.form_dialog = false;
                        break;
                    case 'excel':
                        this.excel_dialog = false;
                        break;
                }
            }
        },
        mounted() {
            this.$store.dispatch('unsetLoader');
        }
    }
</script>