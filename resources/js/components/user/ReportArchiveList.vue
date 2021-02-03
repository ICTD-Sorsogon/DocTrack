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
                            <v-list-item :key="1" @click.stop="searchOption = 'normal'">
                                <v-icon class="ma-1">mdi-magnify</v-icon> NORMAL SEARCH
                            </v-list-item>
                            <v-list-item :key="2" @click.stop="searchOption = 'advance'">
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

                   <v-row>
                       <!--<v-col class="d-flex" cols="12" xs="9" sm="9" md="9" lg="9" xl="9">
                           <v-select
                                v-model="filterYearSelected"
                                :items="distinctYearFromDB"
                                small-chips
                                label="Select Year"
                                multiple
                                outlined
                                dense
                                class="mx-4"
                                deletable-chips
                                @change="loadData"
                            >
                                <template v-slot:prepend-item>
                                    <v-list-item ripple @click="select(); loadData('All')">
                                        <v-list-item-action>
                                            <v-icon :color="filterYearSelected.length > 0 ? 'indigo darken-4' : ''">
                                                {{ icon }}
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title>Select All</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2"></v-divider>
                                </template>
                            </v-select>
                       </v-col>-->
                       <v-col class="d-flex" cols="12" xs="3" sm="3" md="3" lg="3" xl="3">
                           <v-select
                                v-model="filterOptionSelected"
                                :items="filterOptionList"
                                label="Select Record"
                                dense
                                outlined
                                class="mx-4"
                                filled
                                @change="filterChange(false, false)"
                            ></v-select>
                            <v-divider class=" mt-0" inset vertical dense/>
                       </v-col>
                       <v-col class="d-flex" cols="12" xs="9" sm="9" md="9" lg="9" xl="9" v-if="isByYear">
                           <v-select
                                v-model="filterYearSelected"
                                :items="distinctYearFromDB"
                                small-chips
                                label="Select Year"
                                multiple
                                outlined
                                dense
                                class="mx-4"
                                deletable-chips
                                @change="filterChange(true, false)"
                            >
                                <template v-slot:prepend-item>
                                    <v-list-item ripple @click="select()">
                                        <v-list-item-action>
                                            <v-icon :color="filterYearSelected.length > 0 ? 'indigo darken-4' : ''">
                                                {{ icon }}
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title>Select All</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2"></v-divider>
                                </template>
                            </v-select>
                       </v-col>
                       <v-col class="d-flex" cols="12" xs="9" sm="9" md="9" lg="9" xl="9" v-if="!isByYear">
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
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="filterDateFrom"
                                    scrollable
                                    :max="new Date(filterDateTo).toISOString().substr(0, 10)"
                                    :min="Math.min(...distinctYearFromDB).toString()"
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="filterDateDialogFrom = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.dialog.save(filterDateFrom); filterChange(false, true)"
                                    >
                                        OK
                                    </v-btn>
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
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="filterDateTo"
                                    scrollable
                                    :max="Math.max(...distinctYearFromDB).toString() + '-12-31'"
                                    :min="new Date(filterDateFrom).toISOString().substr(0, 10)"
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="filterDateDialogTo = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.dialog1.save(filterDateTo); filterChange(false, true)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-dialog>
                       </v-col>
                   </v-row>





                    <!--<div v-if="searchOption == 'advance'" class="elevation-3 pt-5 pb-0 mb-5" style="background-color:#E6F5FD;">-->
                    <v-card v-if="searchOption == 'advance'" class="elevation-2 pt-5 pb-0 mb-5" style="background-color:#E6F5FD;border:1px solid #B8B8B8;">
                        <v-row>
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-text-field v-model="search" @input="textboxChange" label="Traking ID" class="mx-4"/>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-text-field v-model="search" @input="textboxChange" label="Subject" class="mx-4"/>
                            </v-col>

                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-select class="mx-4" :items="keys2" label="Document Source" dense></v-select>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-select class="mx-4" :items="keys3" label="Document Type" dense></v-select>
                            </v-col>

                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-select class="mx-4" :items="keys" label="Originating Office" dense></v-select>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-select class="mx-4" :items="keys" label="Destination Office" dense></v-select>
                            </v-col>

                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-text-field v-model="search" @input="textboxChange" label="Sender Name" class="mx-4"/>
                            </v-col>
                            <v-col class="d-flex" cols="12" xs="6" sm="6" md="6" lg="6" xl="6">
                                <v-text-field v-model="search" @input="textboxChange" label="Date Created" class="mx-4"/>
                            </v-col>



                            <!--<v-col class="ml-5">
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
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="filterDateTo"
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="filterDateDialogTo = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="$refs.dialog1.save(filterDateTo)"
                                        >
                                            OK
                                        </v-btn>
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>-->

                        </v-row>
                         <v-btn
                            v-show="true"
                            color="red"
                            fab
                            dark
                            small
                            absolute
                            top
                            right
                            @click="searchOption = 'normal'"
                        >
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card>
                    <!--</div>-->

                    <v-row v-if="searchOption == 'normal'">
                        <v-col class="d-flex" cols="12" xs="12" sm="12" md="12" lg="12" xl="12">
                            <v-text-field v-model="search" @input="textboxChange" label="Search Value" class="mx-4"/>
                        </v-col>
                    </v-row>
                </template>

                <template v-slot:[`item.tracking_code`] = "{ item }">
					<v-chip class='trackin' label dark :color="getTrackingCodeColor(item, item.document_type_id)" >
						{{ item.tracking_code }}
					</v-chip>
	        	</template>

                <!--<template v-slot:[`item.restore`]="{ item }">
                    <td>
                        <v-btn
                            color="primary"
                            icon
                            @click.prevent="{selectDoc(item.id); viewDialog = true}"
                            title="dd"
                        >
                            <v-icon>mdi-backup-restore</v-icon>
                        </v-btn>
                    </td>
                </template>-->

                <template v-slot:[`item.actions`]="{ item }">
                    <v-row>
                        <v-col cols="12" xs="6" sm="6" md="6" lg="6" xl="6" class="pr-0" style="top:50%; text-align:right; padding-left:0px !important">
                            <v-btn fab icon raised x-small color = 'primary' title="Restore to Active" style="margin-right:4px;"
                                @click.prevent="{selectDoc(item.id); viewDialog = true}"
                            ><v-icon>mdi-backup-restore</v-icon></v-btn>
                        </v-col>
                        <v-col cols="12" xs="6" sm="6" md="6" lg="6" xl="6" class="pl-0" style="top:50%; text-align:left; ">
                            <v-btn fab icon raised x-small color = 'primary' title="View More"
                                @click.prevent="{selectDoc(item.id); viewDialog = true}"
                            ><v-icon>mdi-more</v-icon></v-btn>
                        </v-col>
                    </v-row>
                </template>


                <!--<template v-slot:[`item.view_more`]="{ item }">
                    <td>
                        <v-btn
                            color="primary"
                            icon
                            @click.prevent="{selectDoc(item.id); viewDialog = true}"
                        >
                            <v-icon>mdi-more</v-icon>
                        </v-btn>
                    </td>
                </template>-->


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
    import TableModal from './components/TableModal';
    import { colors } from '../../constants';
    import { mapGetters, mapActions } from "vuex";

    export default {
        components: { OfficeTableModal, ExcelDialog, TableModal },
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
                    { text: 'Date Created', value: 'created_at' },
                   // { text: 'Restore', value: 'restore' },
                   // { text: 'View More', value: 'view_more' },
                    { text: 'Action', value: 'actions', align: 'center', },
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
                filterOptionList: ["By Date Range", "By Year"],
                filterOptionSelected: "By Date Range",
                isByYear: false
            }
        },
        watch: {
            menu (val) {
                val && setTimeout(() => (this.$refs.picker.activePicker = 'YEAR'))
            },
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
                    //console.log('ll:' + this.documentsArchive)
                    console.log('getter');
                    return JSON.parse(JSON.stringify( this.documentsArchive[0].data)).map(doc=>{
                        doc.is_external = doc.is_external ? 'External' : 'Internal'
                        doc.sender_name = doc.sender?.name ?? doc.sender_name
                        doc.originating_office = doc.origin_office?.name ?? doc.originating_office
                        doc.created_at = new Date(doc.created_at).toISOString().substr(0, 10)

                        const year = new Date(doc.created_at).getFullYear().toString()
                        if(!this.distinctYearFromDB.includes(year)){
                            this.distinctYearFromDB.push(year);
                        }

                        return doc
                    })
                },
                set: function (data) {
                    console.log('setter')
                    return JSON.parse(JSON.stringify(data)).map(doc=>{
                        doc.is_external = doc.is_external ? 'External' : 'Internal'
                        doc.sender_name = doc.sender?.name ?? doc.sender_name
                        doc.originating_office = doc.origin_office?.name ?? doc.originating_office
                        doc.created_at = new Date(doc.created_at).toISOString().substr(0, 10)

                        const year = new Date(doc.created_at).getFullYear().toString()
                        if(!this.distinctYearFromDB.includes(year)){
                            this.distinctYearFromDB.push(year);
                        }

                        return doc
                    })
                }
            },
            selected_document() {
                return this.extendedData.find(data=>data.id == this.activeDoc)
            },
            allData () {
                //console.log('compare:' + this.filterYearSelected.length === this.distinctYearFromDB.length);
                return this.filterYearSelected.length === this.distinctYearFromDB.length
            },
            icon () {
                if (this.allData) return 'mdi-close-box'
                if (this.allData) return 'mdi-minus-box'
                return 'mdi-checkbox-blank-outline'
            },
            /*filterYearSelected: {
                get: function () {
                    return [new Date().getFullYear().toString()]
                },
                set: function (year) {
                    console.log('hh:');
                    console.log(year)
                    console.log('end')
                    return year
                }
            }*/
        },
        methods: {
            filterChange(isYearChange, isDateRangeChane){
               // console.log(this.filterOptionSelected, ...this.filterYearSelected)

               /* console.log(
                    this.filterYearSelected.includes(new Date(this.date).getFullYear().toString()) &&
                    this.filterYearSelected.includes(new Date(this.filterDateTo).getFullYear().toString())
                )
                console.log(new Date(this.date).getFullYear().toString(), new Date(this.filterDateTo).getFullYear().toString());*/
                const filterSelected = {
                    filterBy: this.filterOptionSelected,
                    filterSelected: []
                }

                const dateFromYear = new Date(this.filterDateFrom).getFullYear().toString();
                const dateToYear = new Date(this.filterDateTo).getFullYear().toString();

                const isInState = this.filterYearSelected.includes(dateFromYear) && this.filterYearSelected.includes(dateToYear);




                if(this.filterOptionSelected == 'By Date Range'){

                    this.isByYear = false
                    if (isDateRangeChane) {
                        console.log('date change')
                        filterSelected.filterSelected = [this.filterDateFrom, this.filterDateTo]
                        filterSelected.data = this.extendedData
                        this.$store.commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelected);
                    } else {
                        if(isInState){
                            console.log('get data from state : state match');
                            filterSelected.filterSelected = [this.filterDateFrom, this.filterDateTo]
                            filterSelected.data = this.extendedData
                            this.$store.commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelected);
                        }else{
                            filterSelected.filterSelected = [this.filterDateFrom, this.filterDateTo]
                            console.log('fetch data from db : state did not match');
                            this.$store.dispatch('getArchiveDocuments', filterSelected);
                        }
                    }
                }else if(this.filterOptionSelected == 'By Year'){
                    this.isByYear = true
                    //this.$store.dispatch('getArchiveDocuments', this.filterYearSelected);
                    if (isYearChange) {
                        if(this.filterYearSelected.length > 0){
                        console.log('change year', this.filterYearSelected);
                        }
                        filterSelected.filterSelected = this.filterYearSelected
                        filterSelected.data = this.extendedData
                        this.$store.commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelected);
                    } else {
                        if(isInState){
                            console.log('2get data from state : state match');
                            filterSelected.filterSelected = this.filterYearSelected
                            filterSelected.data = this.extendedData
                            this.$store.commit('GET_ALL_ARCHIVE_DOCUMENTS', filterSelected);
                        }else{
                            filterSelected.filterSelected = this.filterYearSelected
                            console.log('2fetch data from db : state did not match');
                            this.$store.dispatch('getArchiveDocuments', filterSelected);
                        }
                    }
                }

            },
            /*loadData(data){
                if(data == 'All'){
                    console.log('All data here', this.distinctYearFromDB);
                    this.filterChange('byYear')
                }else{
                    console.log(data);
                    this.filterChange('byYear')
                }
            },*/
            select () {
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
                console.log(`${this.searchOption} : ` + value)
            },
            getTrackingCodeColor(document, document_type_id) {
                return colors[document_type_id];
            },
            /*searchOption(key){
                switch (key) {
                    case 'normal':
                            this.searchOption = 'normal'
                        break;
                    case 'advance':
                            this.searchOption = 'advance'
                        break;
                }
            },*/
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
                    case 'dialog':
                        this.viewDialog = false
                        break;
                }
            }
        },
        mounted() {
            //console.log('from', this.$store.state.documents.documentsArchive.data, 'end')
            if (this.$store.state.documents.documentsArchive[0].data == undefined) {
                const currentDate = {
                    filterBy: "By Date Range",
                    filterSelected: [
                        new Date().toISOString().substr(0, 10),
                        new Date().toISOString().substr(0, 10)
                    ]
                }
                console.log('no data found in state');
                this.$store.dispatch('getArchiveDocuments', currentDate);
                console.log(': reloaded as of now');
            }else{
                console.log('has data in state');
                console.log(this.$store.state.documents.documentsArchive[0]);

                const filterBy = this.$store.state.documents.documentsArchive[0].filter
                const filterSelected = this.$store.state.documents.documentsArchive[0].filter_selected
                const data = this.$store.state.documents.documentsArchive[0].data

                this.filterOptionSelected = filterBy
                console.log('--',this.filterOptionSelected);
                if (filterBy == 'By Date Range') {
                    this.filterDateFrom = filterSelected[0]
                    this.filterDateTo = filterSelected[1]
                    this.isByYear = false
                    console.log('dd', filterSelected[0]);
                } else {
                    this.filterYearSelected = filterSelected
                    this.isByYear = true
                }
                this.extendedData = data
            }
           /* setTimeout(function() {
                console.log('from', this.$store.state.documents.documentsArchive.data, 'end')
               // debugger
            }.bind(this), 5000);
            debugger
*/
            //console.log(this.documentsArchive.data);
            this.$store.dispatch('unsetLoader');

            //console.log(new Date('2020', '12', 0).getDate().toString())

        }
    }
</script>