<template>
    <div>
        <v-card>
            <v-card-title primary-title>
                <v-toolbar flat>
                    <v-toolbar-title>Archive List Report</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical/>
                    <v-spacer/>

                    <!--<v-switch
                        v-model="switch1"
                        :label="`${(switch1)?'Group by Office':'View All'}`"
                    ></v-switch>-->


                    <v-menu left bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn color="primary" dark class="mb-2 ma-1" v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon> GENERATE REPORT
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item :key="1" @click.stop="openDialog('import_office')">
                                <v-icon class="ma-1">mdi-file-export-outline</v-icon> BY OFFICE
                            </v-list-item>
                            <v-list-item :key="2" @click.stop="openDialog('export_office')">
                                <v-icon  class="ma-1">mdi-file-export-outline</v-icon> BY DATE RANGE
                            </v-list-item>
                            <v-list-item :key="2" @click.stop="openDialog('export_office')">
                                <v-icon  class="ma-1">mdi-file-export-outline</v-icon> ALL RECORD
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <v-menu left bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn color="primary" dark class="mb-2 ma-1" v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon> SEARCH OPTION
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item :key="1" @click.stop="search_option = 'normal'">
                                <v-icon class="ma-1">mdi-magnify</v-icon> NORMAL SEARCH
                            </v-list-item>
                            <v-list-item :key="2" @click.stop="search_option = 'advance'">
                                <v-icon  class="ma-1">mdi-magnify</v-icon> ADVANCED SEARCH
                            </v-list-item>
                        </v-list>
                    </v-menu>

                </v-toolbar>
            </v-card-title>
            <v-data-table
                :headers="headers"
                :items="extendedData"
                :items-per-page="10"
                :search="search"
                class="elevation-1"
            >
                <template v-slot:top>
                   <!-- <v-text-field v-model="search" label="Search" class="mx-4"/>-->





                    <div v-if="search_option == 'advance'" class="elevation-1 pt-5 pb-0" style="background-color:#E6F5FD;">
                        <v-row >
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-select class="mx-4" :items="keys" label="Originating Office" dense></v-select>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-select class="mx-4" :items="keys" label="Destination Office" dense></v-select>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="4" sm="4" md="4" lg="4" xl="4">
                                <v-select class="mx-4" :items="keys1" label="Document Column" dense></v-select>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="4" sm="4" md="4" lg="4" xl="4">
                                <v-select class="mx-4" :items="keys2" label="Document Source" dense></v-select>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="4" sm="4" md="4" lg="4" xl="4">
                                <v-select class="mx-4" :items="keys3" label="Document Type" dense></v-select>
                            </v-col>

                            <v-col class="ml-5">
                                <v-dialog
                                    ref="dialog"
                                    v-model="modal"
                                    :return-value.sync="date"
                                    persistent
                                    width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="date"
                                            label="FROM"
                                            prepend-icon=""
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="date"
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="modal = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="$refs.dialog.save(date)"
                                        >
                                            OK
                                        </v-btn>
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>

                            <v-col class="mr-5">
                                <v-dialog
                                    ref="dialog1"
                                    v-model="modal1"
                                    :return-value.sync="date1"
                                    persistent
                                    width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="date1"
                                            label="TO"
                                            prepend-icon=""
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="date1"
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="modal1 = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="$refs.dialog1.save(date1)"
                                        >
                                            OK
                                        </v-btn>
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                        </v-row>
                    </div>

                    <v-row>
                        <v-col class="d-flex" cols="12" xs="12" sm="12" md="12" lg="12" xl="12">
                            <v-text-field v-model="search" label="Search Value" class="mx-4"/>
                        </v-col>
                    </v-row>
                </template>

                <template v-slot:[`item.view_more`]="{ item }">
                    <td>
                        <v-btn
                            color="primary"
                            icon
                            @click.prevent="{selectDoc(item.id); viewDialog = true}"
                        >
                            <v-icon>mdi-more</v-icon>
                        </v-btn>
                    </td>
                </template>


            </v-data-table>
        </v-card>

        <v-row justify="center">
            <v-dialog v-model="userAE" persistent max-width="1000px">
                <v-card>
                    <v-card-title class="headline">{{ formTitle }}</v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-form ref="form" lazy-validation>
                                <v-row>hey hey</v-row>
                                <v-row justify="end">
                                    <v-btn color="primary" class="mb-5 mt-10 ma-5" @click="userAE = false">Close</v-btn>
                                    <v-btn color="primary" class="mb-5 mt-10 ma-5" dark> SAVE </v-btn>
                                </v-row>
                            </v-form>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-row>

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
                activeDoc: null,
                viewDialog: false,
               /* headers: [
                    { text: 'Office Name', value: 'builded_office_name' },
                    { text: '', value: 'data-table-expand' },
                   // { text: 'Action', value: 'actions', align: 'center', },
                ],*/
                headers: [
                    { text: 'Tracking ID', value: 'tracking_code' },
                    { text: 'Subject', value: 'subject' },
                    { text: 'Source', value: 'is_external' },
                    { text: 'Type', value: 'document_type.name' },
                    { text: 'Originating Office', value: 'originating_office' },
                    { text: 'Destination Office', value: 'destination.name' },
                    { text: 'Sender', value: 'sender_name' },
                    { text: 'View More', value: 'view_more' },
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
                ],
                modal: false,
                date: new Date().toISOString().substr(0, 10),
                modal1: false,
                date1: new Date().toISOString().substr(0, 10),
                items: ['Foo', 'Bar', 'Fizz', 'Buzz'],
                formTitle: 'advance serch',
                userAE: false,
                search_option: 'normal',
                table: []
            }
        },
        watch: {
            menu (val) {
                val && setTimeout(() => (this.$refs.picker.activePicker = 'YEAR'))
            },
        },
        computed: {
            ...mapGetters(['documentsArchive', 'datatable_loader', 'auth_user']),
            offices() {
                const offices = this.$store.state.offices.offices;
                offices.forEach(office => {
                    office.builded_office_name = office.office_code + ' - ' + office.name
                });
                return offices;
            },
            request(){
                return this.$store.state.snackbars.request;
            },
            extendedData() {
                return JSON.parse(JSON.stringify( this.documentsArchive)).map(doc=>{
                    doc.is_external = doc.is_external ? 'External' : 'Internal'
                    doc.sender_name = doc.sender?.name ?? doc.sender_name
                    doc.originating_office = doc.origin_office?.name ?? doc.originating_office
                    return doc
                })
            },
        },
        methods: {
            searchOption(key){
                switch (key) {
                    case 'normal':
                            this.search_option = 'normal'
                        break;
                    case 'advance':
                            this.search_option = 'advance'
                        break;
                }
            },
            jj($event){
                if(this.birthday.length > 2){
                    this.birthday.pop();
                   // alert('limit 2');
                }

                console.log('JJJJJJJJ ', $event);
            },
            sortBy(){},
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
            selectDoc(id){
                this.activeDoc = id
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
            this.$store.dispatch('getArchiveDocuments');
            this.$store.dispatch('unsetLoader');

        }
    }
</script>