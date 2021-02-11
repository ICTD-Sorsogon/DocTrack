<template>
    <div>
        <v-card>
            <v-card-title primary-title class="pr-0">
                <v-toolbar flat>
                    <v-toolbar-title>Archive List Report</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical/>
                    <v-spacer/>

                    <v-menu left bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn color="primary" dark class="mb-2 ma-1" v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon> GENERATE REPORT
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item :key="1" @click.stop="openDialog('import_office')">
                                <v-icon class="ma-1">mdi-file-export-outline</v-icon> GROUP BY OFFICE
                            </v-list-item>
                            <v-list-item :key="2" @click.stop="openDialog('export_office')">
                                <v-icon  class="ma-1">mdi-file-export-outline</v-icon> SELECTED DATA
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
                            <v-list-item :key="1" @click.stop="searchOption = 'normal'">
                                <v-icon class="ma-1 mr-1">mdi-magnify</v-icon> NORMAL
                            </v-list-item>
                            <v-list-item :key="2" @click.stop="searchOption = 'advance'">
                                <v-icon  class="ma-1 mr-1">mdi-cogs</v-icon> ADVANCED
                            </v-list-item>
                        </v-list>
                    </v-menu>

                </v-toolbar>
            </v-card-title>
            <v-data-table
                :headers="headers"
                :items="tableData"
                :items-per-page="10"
                :search="search"
                class="elevation-1"
            >
                <template v-slot:top>
                    <v-row>
                        <v-col class="d-flex" v-bind="breakpoint(3)">
                            <v-select
                                v-model="filterOptionSelected"
                                :items="filterOptionList"
                                label="FILTER BY:"
                                dense
                                outlined
                                class="mx-4"
                                filled
                                @change="filterBy"
                            ></v-select>
                            <v-divider class=" mt-0" inset vertical dense/>
                        </v-col>
                        <v-col class="d-flex"  v-bind="breakpoint(9)" v-if="isByYear">
                           <v-select
                                v-model="filterYearSelected"
                                :items="distinctYearFromDB"
                                small-chips
                                label="SELECT YEAR"
                                multiple
                                outlined
                                dense
                                class="mx-4"
                                deletable-chips
                                @change="filterChange(true, false)"
                            >
                                <template v-slot:prepend-item>
                                    <v-list-item ripple @click="selectAllYear">
                                        <v-list-item-action>
                                            <v-icon :color="filterYearSelected.length > 0 ? 'indigo darken-4' : ''"> {{ icon }} </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title>Select All</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2"/>
                                </template>
                            </v-select>
                        </v-col>
                        <v-col class="d-flex"  v-bind="breakpoint(9)" v-if="!isByYear">
                            <v-dialog
                                ref="dialog"
                                v-model="filterDateDialogFrom"
                                :return-value.sync="filterDateFrom"
                                persistent
                                width="290px"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="filterDateFrom"
                                        label="FROM"
                                        prepend-icon=""
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mx-4"
                                        outlined
                                        dense
                                    />
                                </template>
                                <v-date-picker
                                    v-model="filterDateFrom"
                                    scrollable
                                    :max="new Date(filterDateTo).toISOString().substr(0, 10)"
                                    :min="Math.min(...distinctYearFromDB).toString()"
                                >
                                    <v-spacer/>
                                    <v-btn text color="primary" @click="filterDateDialogFrom = false"> Cancel </v-btn>
                                    <v-btn text color="primary" @click="$refs.dialog.save(filterDateFrom); filterChange(false, true)"> OK </v-btn>
                                </v-date-picker>
                            </v-dialog>
                            <v-dialog
                                ref="dialog1"
                                v-model="filterDateDialogTo"
                                :return-value.sync="filterDateTo"
                                persistent
                                width="290px"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="filterDateTo"
                                        label="TO"
                                        prepend-icon=""
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mx-4"
                                        outlined
                                        dense
                                    />
                                </template>
                                <v-date-picker
                                    v-model="filterDateTo"
                                    scrollable
                                    :max="Math.max(...distinctYearFromDB).toString() + '-12-31'"
                                    :min="new Date(filterDateFrom).toISOString().substr(0, 10)"
                                >
                                    <v-spacer/>
                                    <v-btn text color="primary" @click="filterDateDialogTo = false"> Cancel </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.dialog1.save(filterDateTo); filterChange(false, true)"
                                    > OK </v-btn>
                                </v-date-picker>
                            </v-dialog>
                        </v-col>
                    </v-row>

                    <v-card v-if="searchOption == 'advance'" class="elevation-2 pt-5 pb-0 mb-5" style="background-color:#E6F5FD;border:1px solid #B8B8B8;">
                        <v-row class="d-flex">
                            <v-col v-bind="breakpoint(6)">
                                <v-text-field v-model="advanceSearch.trackingId" @input="textboxChange" label="Traking ID" class="mx-4"/>
                            </v-col>
                            <v-col v-bind="breakpoint(6)">
                                <v-text-field v-model="advanceSearch.search" @input="textboxChange" label="Subject" class="mx-4"/>
                            </v-col>
                            <v-col v-bind="breakpoint(6)">
                                <v-select class="mx-4" :items="keys2" v-model="advanceSearch.source" @change="textboxChange" label="Document Source" dense/>
                            </v-col>
                            <v-col v-bind="breakpoint(6)">
                                <v-select class="mx-4" :items="keys3" v-model="advanceSearch.type" @change="textboxChange" label="Document Type" dense/>
                            </v-col>
                            <v-col v-bind="breakpoint(6)">
                                <v-select class="mx-4" :items="keys" v-model="advanceSearch.originating" @change="textboxChange" label="Originating Office" dense/>
                            </v-col>
                            <v-col v-bind="breakpoint(6)">
                                <v-select class="mx-4" :items="keys" v-model="advanceSearch.destination" @change="textboxChange" label="Destination Office" dense/>
                            </v-col>

                            <v-col v-bind="breakpoint(6)">
                                <v-text-field v-model="advanceSearch.sender" @input="textboxChange" label="Sender Name" class="mx-4"/>
                            </v-col>
                            <v-col v-bind="breakpoint(6)">
                                <v-text-field v-model="advanceSearch.dateCreated" @input="textboxChange" label="Date Created" class="mx-4"/>
                            </v-col>
                        </v-row>
                         <v-btn v-show="true" color="#81B7DA" class="elevation-2" fab dark small absolute top right @click="searchOption = 'normal'">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card>

                    <v-row v-if="searchOption == 'normal'" class="d-flex">
                        <v-col v-bind="breakpoint(12)">
                            <v-text-field v-model="search" label="Search Value" class="mx-4"/>
                        </v-col>
                    </v-row>

                </template>

                <template v-slot:[`item.tracking_code`] = "{ item }">
					<v-chip class='trackin' label dark :color="getTrackingCodeColor(item, item.document_type_id)" >
						{{ item.tracking_code }}
					</v-chip>
	        	</template>

                <template v-slot:[`item.actions`]="{ item }">
                    <v-row>
                        <v-col v-bind="breakpoint(6)" class="pr-0" style="top:50%; text-align:right; padding-left:0px !important">
                            <v-btn fab icon raised x-small color = 'primary' title="Restore to Active" style="margin-right:4px;"
                                @click.prevent="{selectDoc(item.id); viewDialog = true}"
                            ><v-icon>mdi-backup-restore</v-icon></v-btn>
                        </v-col>
                        <v-col v-bind="breakpoint(6)" class="pl-0" style="top:50%; text-align:left; ">
                            <v-btn fab icon raised x-small color = 'primary' title="View More"
                                @click.prevent="{selectDoc(item.id); viewDialog = true}"
                            ><v-icon>mdi-more</v-icon></v-btn>
                        </v-col>
                    </v-row>
                </template>

                <v-alert slot="no-results" :value="true" type="error" icon="mdi-alert" align="left">
                    Your search for "{{ search }}" found no results.
                </v-alert>
            </v-data-table>
            <table-modal
                @closeDialog="closeDialog('dialog')"
                :dialog="viewDialog"
                v-if="selected_document"
                :selected_document="selected_document"
            />
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
            <v-dialog v-model="delete_dialog" persistent max-width="450px">
                <v-card color="grey lighten-2">
                    <v-card-title class="headline">
                        <v-icon class="mr-2" size="30px">mdi-delete-circle</v-icon> Delete Office
                    </v-card-title>
                    <v-card-text>
                        Are you sure you want to delete office from the list? <br> <strong>- {{ delete_info.name }}</strong>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" text @click="delete_dialog = false"> Cancel </v-btn>
                        <v-btn color="primary darken-1" text @click.prevent="deleteOffice"> Confirm </v-btn>
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
    import TableModal from './components/TableModal';
    import { colors } from '../../constants';
    import { mapGetters, mapActions } from "vuex";

    export default {
        components: { OfficeTableModal, ExcelDialog, TableModal },
        data() {
            return {
                activeDoc: null,
                viewDialog: false,
                headers: [
                    { text: 'Tracking ID', value: 'tracking_code' },
                    { text: 'Subject', value: 'subject' },
                    { text: 'Source', value: 'is_external' },
                    { text: 'Type', value: 'document_type.name' },
                    { text: 'Originating Office', value: 'originating_office' },
                    { text: 'Destination Office', value: 'destination.name' },
                    { text: 'Sender', value: 'sender_name' },
                    { text: 'Date Created', value: 'created_at' },
                    { text: 'Action', value: 'actions', align: 'center', },
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
                filterDateDialogFrom: false,
                filterDateFrom: new Date().toISOString().substr(0, 10),
                filterDateDialogTo: false,
                filterDateTo: new Date().toISOString().substr(0, 10),
                items: ['Foo', 'Bar', 'Fizz', 'Buzz'],
                formTitle: 'advance serch',
                userAE: false,
                searchOption: 'normal',
                table: [],
                distinctYearFromDB:[],
                filterYearSelected: [new Date().getFullYear().toString()],
                filterOptionList: ["Date", "Year"],
                filterOptionSelected: "Date",
                isByYear: false,
                filter: {},
                tableData: [],
                advanceSearch: {
                    trackingId: '',
                    subject: '',
                    source: '',
                    type: '',
                    originating: '',
                    destination: '',
                    sender: '',
                    dateCreated: ''
                },
            }
        },
        watch: {
            menu (val) {
                val && setTimeout(() => (this.$refs.picker.activePicker = 'YEAR'))
            }
        },
        computed: {
            ...mapGetters(['documentsArchive', 'datatable_loader', 'auth_user', 'request']),
            offices() {
                const offices = JSON.parse(JSON.stringify(this.$store.state.offices.offices));
                offices.forEach(office => {
                    office.builded_office_name = office.office_code + ' - ' + office.name
                });
                return offices;
            },
            extendedData: {
                get: function () {
                    //console.log('getter');
                    if (this.documentsArchive.length) {
                        const selected = this.documentsArchive[0].selected
                        const dataRes = (selected.filter == 'Year')? selected.year.data : selected.date.data
                        const data = JSON.parse(JSON.stringify(dataRes)).map(doc=>{
                            doc.is_external = doc.is_external ? 'External' : 'Internal'
                            doc.sender_name = doc.sender?.name ?? doc.sender_name
                            doc.originating_office = doc.origin_office?.name ?? doc.originating_office
                            doc.created_at = new Date(doc.created_at).toISOString().substr(0, 10)
                            return doc
                        })
                        this.tableData = data
                        return data
                    }
                },
                set: function (data) {
                    //console.log('setter')
                }
            },
            selected_document() {
                if (this.documentsArchive.length) {
                    return this.extendedData.find(data=>data.id == this.activeDoc)
                }
            },
            allData () {
                return this.filterYearSelected.length === this.distinctYearFromDB.length
            },
            icon () {
                if (this.allData) return 'mdi-close-box'
                return 'mdi-checkbox-blank-outline'
            },
        },
        methods: {
            breakpoint(col){
                return { cols:"12", xs:col, sm:col, md:col, lg:col, xl:col }
            },
            filterBy(){
                this.filter = {}
                this.filter.action = "update"
                this.filter.filterBy = this.filterOptionSelected
                const selected = this.documentsArchive[0]

                if (this.filterOptionSelected == "Year") {
                    this.isByYear = true
                    this.filterYearSelected = selected.selected.year.list
                    this.filter.year = {list: selected.selected.year.list}
                    this.filter.data = selected.selected.year.data
                } else {
                    this.isByYear = false
                    this.filterDateFrom = selected.selected.date.list[0]
                    this.filterDateTo = selected.selected.date.list[1]
                    this.filter.date = {list: selected.selected.date.list}
                    this.filter.data = selected.selected.date.data
                }
                this.filter.yearFromDb = selected.year
                this.$store.commit('GET_ALL_ARCHIVE_DOCUMENTS', this.filter)
            },
            mountState(){
                if (!this.documentsArchive.length) {
                    this.filter = {}
                    this.filter.action = "new"
                    this.filter.filterBy = this.filterOptionSelected
                    this.filter.date = { list: [this.filterDateFrom, this.filterDateTo] }
                    this.$store.dispatch('getArchiveDocuments', this.filter).then( () => {
                        this.distributeState()
                    })
                } else {
                    if (this.documentsArchive[0].hasNewTerminated) {
                        this.filter = {}
                        this.filter.action = "update"
                        this.filter.filterBy = this.documentsArchive[0].selected.filter
                        if (this.documentsArchive[0].selected.filter == "Year") {
                            this.filter.year = { list: this.documentsArchive[0].selected.year.list }
                        } else {
                            this.filter.date = { list: this.documentsArchive[0].selected.date.list }
                        }
                        this.$store.dispatch('getArchiveDocuments', this.filter).then( () => {
                            this.distributeState()
                        })
                    } else {
                        this.distributeState()
                    }
                }
            },
            distributeState(){
                const selected = this.documentsArchive[0]
                this.distinctYearFromDB = selected.year
                this.filterOptionSelected = selected.selected.filter
                if (selected.selected.filter == 'Year') {
                    this.isByYear = true
                    this.filterYearSelected = selected.selected.year.list
                } else {
                    this.isByYear = false
                    this.filterDateFrom = selected.selected.date.list[0]
                    this.filterDateTo = selected.selected.date.list[1]
                }
            },
            filterChange(isYearChange, isDateRangeChane){
                this.filter = {}
                this.filter.action = "update"
                this.filter.filterBy = this.filterOptionSelected
                if (isYearChange) {
                    this.filter.year = { list: this.filterYearSelected }
                } else {
                    this.filter.date = { list: [this.filterDateFrom, this.filterDateTo] }
                }
                this.$store.dispatch('getArchiveDocuments', this.filter).then( () => {
                    this.distributeState()
                })
            },
            selectAllYear () {
                this.$nextTick(() => {
                    if (this.allData) {
                        this.filterYearSelected = []
                        this.filterChange(true, false)
                    } else {
                        this.filterYearSelected = this.distinctYearFromDB.slice()
                        this.filterChange(true, false)
                    }
                })
            },
            textboxChange(value){

                /*const advanceSearch = {
                    trackingId: '',
                    subject: '',
                    source: '',
                    type: '',
                    originating: '',
                    destination: '',
                    sender: '',
                    dateCreated: ''
                }*/

                console.log(value)
                console.log(this.$refs)
                //this.$refs.trackingId.value)
                //trackingId
                //console.log(`${this.searchOption} : ` + value)
            },
            getTrackingCodeColor(document, document_type_id) {
                return colors[document_type_id];
            },
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
                    case 'dialog':
                        this.viewDialog = false
                        break;
                }
            }
        },
        async mounted() {
            await this.mountState()
            this.$store.dispatch('unsetLoader')
        }
    }
</script>