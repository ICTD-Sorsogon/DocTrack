<template>
<v-dialog v-model="excel_dialog" persistent scrollable fullscreen>
    <v-container fluid  class="pr-0 pl-0"  style="padding:0px;">
      <v-card v-if="selected_office" style="height:100%; width:100%; overflow-x:hidden; overflow-y:auto">

          <v-row>
            <v-col cols="6" sm="6">
              <v-card-title primary-title> {{ dialog_title }} </v-card-title>
            </v-col>
            <v-col cols="6" sm="6">
                <v-card-actions class="mr-1">
                    <v-spacer></v-spacer>
                    <v-btn x-large color="gray" @click="$emit('close-dialog')" icon>
                    <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-col>
          </v-row>

        <v-card-text>
           <!-- <ValidationObserver ref="observer" v-slot="{ valid }">-->
                <v-form
                    ref="form"
                    lazy-validation
                >
                <v-row>

                    <v-col cols="12" xs="10" sm="10" md="10" lg="10" xl="10">
                        <!--<ValidationProvider rules="required" v-slot="{ errors }">-->
                            <v-file-input
                                label="Browse Excel File"
                                prepend-icon="mdi-file-excel"
                                accept=".csv, .xlsx"
                                ref="files"
                                @change="fileSelected"
                                chips
                                show-size
                                counter
                                outlined
                                dense
                                clearable
                                clear-icon="mdi-delete"
                            />
                           <!-- <span>{{ errors[0] }}</span>
                        </ValidationProvider>-->
                    </v-col>
                    <v-col cols="12" xs="2" sm="2" md="2" lg="2" xl="2">
                         <v-btn color="primary" style="width:100%" large :dark="valid" :loading="btnloading" :disabled="!valid" v-if="dialog_for == 'import_office'" @click="uploadToDatabase"> UPLOAD </v-btn>
                         <v-btn color="primary" style="width:100%" large :dark="valid" :loading="btnloading" :disabled="!valid" v-if="dialog_for == 'export_office'" @click="saveChangesToOffice"> EXPORT </v-btn>

                    </v-col>
                    <v-col v-show="is_preview && excel_data.length > 0">
                        <v-card>
                            <v-tabs
                            v-model="tab"
                            background-color="primary"
                            dark
                            >
                                <v-tab
                                    v-for="item in excel_data"
                                    :key="item.tab"
                                >
                                    {{ item.tab }}
                                </v-tab>
                            </v-tabs>

                            <v-tabs-items v-model="tab">
                                <v-tab-item
                                    v-for="item in excel_data"
                                    :key="item.id"
                                >
                                    <v-card flat>
                                    <!--<v-card-text>{{ item.content }}</v-card-text>-->
                                        <v-data-table
                                            :headers="excel_table_headers"
                                            :items="item.content"
                                            class="elevation-1"
                                        >
                                            <template v-slot:header.Office_Name="{ header }">
                                                {{ header.text.toUpperCase() }}
                                            </template>
                                        </v-data-table>
                                    </v-card>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-card>
                    </v-col>
                    <v-col v-show="!is_preview && excel_data.length > 0">
                        <v-alert dense outlined type="error">
                             ERROR FOUND, Please see error log bellow
                        </v-alert>
                        <ul>
                            <dl>
                                <dt>HEADER</dt>
                                    <ol>
                                        <li v-for="error in excel_error[0]" :key="error.id">
                                            <strong>{{error.value}}</strong>
                                                    {{error.message}}
                                            <strong>[{{error.cell_position}}]</strong>
                                        </li>
                                    </ol>
                                <dt>CONTENT</dt>
                                    <ol>
                                        <li v-for="error in excel_error[1]" :key="error.id">
                                            <strong>{{error.value}}</strong>
                                                    {{error.message}}
                                            <strong>[{{error.cell_position}}]</strong>
                                        </li>
                                    </ol>
                            </dl>
                        </ul>
                    </v-col>
                </v-row>
                <!--<v-row justify="end">

                    <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="dialog_for == 'import_office'" @click="uploadToDatabase"> UPLOAD </v-btn>
                    <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="dialog_for == 'export_office'" @click="saveChangesToOffice"> EXPORT </v-btn>

                    <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="form.form_mode == 'new_office'" @click="saveNewOffice"> SAVE </v-btn>
                    <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="form.form_mode == 'edit_office'" @click="saveChangesToOffice"> SAVE CHANGES </v-btn>
                </v-row>-->
                </v-form >
            <!--</ValidationObserver-->

        </v-card-text>
      </v-card>
      </v-container>
    </v-dialog>
</template>

<script>
    //import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';
    //import { email, required } from '../../../validate'
    import { mapGetters } from 'vuex';

    import XLSX from 'xlsx';
    import { colors } from '../../../constants';

    //var _ = require('lodash');

    export default {
        //components: { ValidationProvider, ValidationObserver },
        props: ['excel_dialog', 'dialog_title', 'dialog_for'],
        data() {
            return {
                form_old: {
                    id: '',
                    name: '',
                    address: '',
                    office_code: '',
                    contact_number: '',
                    contact_email: '',
                    form_mode: ''
                },
                form: {
                    id: '',
                    name: '',
                    address: '',
                    office_code: '',
                    contact_number: '',
                    contact_email: '',
                    form_mode: ''
                },
                valid: false,
                btnloading: false,
                excel_data: [],
                excel_error:[[], []],
                //file: []
                tab: null,
                /*items: [
                    { tab: 'One', content: 'Tab 1 Content' },
                    { tab: 'Two', content: 'Tab 2 Content' },
                    { tab: 'Three', content: 'Tab 3 Content' },
                    { tab: 'Four', content: 'Tab 4 Content' },
                    { tab: 'Five', content: 'Tab 5 Content' },
                    { tab: 'Six', content: 'Tab 6 Content' },
                    { tab: 'Seven', content: 'Tab 7 Content' },
                    { tab: 'Eight', content: 'Tab 8 Content' },
                    { tab: 'Nine', content: 'Tab 9 Content' },
                    { tab: 'Ten', content: 'Tab 10 Content' },
                ],*/
                excel_table_headers: [
                    {
                        text: 'Office Name',
                        align: 'start',
                        value: 'Office_Name',
                    },
                    { text: 'Office Code', value: 'Office_Code' },
                    { text: 'Address', value: 'Address' },
                    { text: 'Contact Number', value: 'Contact_Number' },
                    { text: 'Email Address', value: 'Email_Address' }
                ],
                is_preview: false,
                offices: []
            }
        },
        computed: {
            //...mapGetters(['form_requests']),
            request(){
                return this.$store.state.snackbars.request;
            },
            selected_office(){
                    return {id: '',
                    name: '',
                    address: '',
                    office_code: '',
                    contact_number: '',
                    contact_email: '',
                    form_mode: ''}
            },

        },
        methods: {
            /*hh($event){
                console.log('clicke', $event);
            },*/
            uploadToDatabase(){

                this.btnloading = true;

                if(this.excel_error[0].length < 1 &&
                    this.excel_error[1].length < 1 &&
                    this.excel_data.length > 0
                ){
                    var office_data = { office_data: this.excel_data };
                    this.$store.dispatch("importNewOffice", office_data).then(() => {
                        if(this.request.status == 'success') {
                            this.$store.dispatch('setSnackbar', {
                                type: 'success',
                                message: this.request.message
                            })
                            .then(() => {

                                this.btnloading = false;
                                //this.$refs.form.reset();
                                //this.$refs.observer.reset();

                                this.$store.dispatch('getOffices');

                                var offices = this.$store.state.offices.offices;
                                this.offices = [];
                                offices.forEach(office => {
                                    this.offices.push(office.name.trim().toLowerCase().replace(/\s/g, ''));
                                });

                                //this.$refs.files.reset();
                            });

                        } else if(this.request.status == 'failed'){
                            this.$store.dispatch('setSnackbar', {
                                type: 'error',
                                message: this.request.message
                            })
                            .then(() => {
                                this.btnloading = false;
                            });
                        }
                    });
                }else{
                    this.$store.dispatch('setSnackbar', {
                        type: 'warning',
                        message: 'Upload request error, please check your file.'
                    });
                    this.btnloading = false;
                }
            },
            randomKey(){
                return Math.random().toString(36).substring(7)
            },
            cellPosition(sheet_index, column_index, row_index){
                return "#"+ (sheet_index + 1) + " " + ((column_index + 1) + 9).toString(36).toUpperCase() + (row_index + 1)
            },
            /*clickFileInput($event){
                //console.log('clicked')
                //this.excel_data = [];
                //this.excel_error = [[], []];
                //if(this.excel_data.length > 0){
                  //  alert('please remove the current file and re-select');
                //}
                //this.$refs.fileupload.reset()

                //this.$refs.fileupload.value = null

                //console.log(this.$refs.fileupload);

               // console.log($event);

               // console.log(this.excel_data);
              // this.excel_data = [];
               //this.excel_error = [[], []];

               //this.fileSelected();


            },*/
            fileSelected(file){



                try {


                    if (file.name.split(".").pop().toLowerCase() == 'xlsx'){

                        var offices = this.$store.state.offices.offices;
                        this.offices = [];
                        offices.forEach(office => {
                            this.offices.push(office.name.trim().toLowerCase().replace(/\s/g, ''));
                        });

                        //console.log(this.$store.state.offices.offices);
                        // Object.values(obj.toLowerCase()).includes(v.toLowerCase())

                       // console.log(_.mapValues(excel_data, _.method('toLowerCase')));

                       //var j = this.$store.state.offices.offices;
                       //console.log(_.mapValues(j, _.method('toLowerCase')));



                        var required_header = [
                            'Office_Name',
                            'Office_Code',
                            'Address',
                            'Contact_Number',
                            'Email_Address'
                        ];

                        this.excel_data = [];
                        this.excel_error = [[], []];


                        //console.log(file);




                        try {
                            //console.log(this.randomKey());

                        // debugger

                            //console.log(file);
                            var reader = new FileReader();
                            reader.readAsArrayBuffer(file);
                            reader.onloadend = function(e) {
                                var data = new Uint8Array(reader.result);
                                var wb = XLSX.read(data,{type:'array', cellDates:true, dateNF:'dd.mm.yyyy h:mm:ss AM/PM'});



                                this.sheet_length = wb.SheetNames.length;
                                for (let i = 0; i < wb.SheetNames.length; i++) {
                                    let sheetName = wb.SheetNames[i];
                                    let worksheet = wb.Sheets[sheetName];

                                    //console.log(worksheet);


                                    /*if(_this.checkSheetName(sheetName ,i) == "invalid"){
                                        this.excel_error[0].push({
                                            id: this.randomKey(),
                                            value: sheetName + " - ",
                                            message: "invalid format ",
                                            cell_position: 'worksheet #' + (i + 1),
                                        });
                                    }*/
                                    try {
                                        var range = XLSX.utils.decode_range(worksheet['!ref']);
                                        if(range.e.r < 1){
                                            this.excel_error[1].push({
                                                id: this.randomKey(),
                                                value: '',
                                                message: "It looks like you don't have any data in this page ",
                                                cell_position: 'worksheet #' + (i + 1),
                                            });
                                        }
                                    } catch (error) {}
                                    for(var R = range.s.r; R <= range.e.r; ++R) {
                                        for(var C = range.s.c; C <= range.e.c; ++C) {
                                            var cellref = XLSX.utils.encode_cell({c:C, r:R});
                                            if(!worksheet[cellref]){
                                                if(R == 0 && C < 6){
                                                    this.excel_error[0].push({
                                                        id: this.randomKey(),
                                                        value: '',
                                                        message: "Header must have 5 column.",
                                                        cell_position: this.cellPosition(i, C, R),
                                                    });
                                                }
                                                if(R > 0 && C < 3){
                                                    this.excel_error[1].push({
                                                        id: this.randomKey(),
                                                        value: '',
                                                        message: "This cell is required ",
                                                        cell_position: this.cellPosition(i, C, R),
                                                    });
                                                }
                                                continue;
                                            }
                                            var cell = worksheet[cellref];
                                            /*
                                            if(R > 0 && C < 3 && cell.v == ''){
                                                this.excel_error[2].push({
                                                    id: this.randomKey(),
                                                    value: '',
                                                    message: "This cell is required ",
                                                    cell_position: this.cellPosition(i, C, R),
                                                });
                                            }*/
                                            if(R == 0 && C < 6){
                                                if(!required_header.includes(cell.v)){
                                                    this.excel_error[0].push({
                                                        id: this.randomKey(),
                                                        value: cell.v,
                                                        message: "Required header did not match, download the sample excel file" + ' / Suggestion: "' + required_header[C] + '"',
                                                        cell_position: this.cellPosition(i, C, R),
                                                    });
                                                }
                                            }
                                            if(R > 0 && C == 0 && this.offices.includes((cell.v).trim().toLowerCase().replace(/\s/g, ''))){
                                                this.excel_error[1].push({
                                                    id: this.randomKey(),
                                                    value: cell.v,
                                                    message: "already exist in the database",
                                                    cell_position: this.cellPosition(i, C, R),
                                                });
                                            }
                                        }
                                    }
                                    this.excel_data.push({
                                        id: i,
                                        content: XLSX.utils.sheet_to_json(worksheet),
                                        tab: sheetName.replace(/\s/g, '')
                                    });
                                    /*_this.current_tab_content.push(XLSX.utils.sheet_to_json(worksheet));*/
                                }
                                if(this.excel_error[0].length < 1 &&
                                    this.excel_error[1].length < 1
                                ){
                                    this.is_preview = true;
                                    this.valid = true;

                                    var excel_data = {
                                        name: 'KKK',
                                        mm: 'JJ',
                                        LL: 'll'
                                    }

                                    //console.log(_.mapValues(excel_data, _.method('toLowerCase')));

                                }else{
                                    this.is_preview = false;
                                    this.valid = false;
                                }


                                /*_this.defaultTabSelected();
                                _this.is_hasfile = true;
                                _this.is_import = false;*/
                            }.bind(this)

                        } catch (error) {
                            //console.log('error found');
                            //this.excel_data = [];
                            //this.excel_error = [[], []];
                        }



                    }else{
                        this.$store.dispatch('snackbars/setSnackbar', {
                            showing: true,
                            text: 'To avoid error required file extension "xlsx" or "csv" ',
                            color: '#1565C0',
                            icon: 'mdi-information-outline',
                        })
                    }

                } catch (error) {
                    this.excel_data = [];
                    this.excel_error = [[], []];
                    this.valid = false;
                }

                //console.log(this.excel_data);

            },
            saveNewOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    //console.log("no changes found");
                    this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: 'No changes found',
                        color: '#1565C0',
                        icon: 'mdi-information-outline',
                    })


                }else{
                     this.btnloading = true;
                    this.$store.dispatch("createNewOffice", this.form).then(() => {



                        /*if (this.offices) {
                            this.$store.dispatch('setSnackbar', {
                                showing: true,
                                text: 'Successfully edited',
                                color: '#43A047',
                                icon: 'mdi-check-bold',
                            })
                            this.$store.dispatch("unsetDataTableLoader");
                            console.log(this.form.name);
                        }*/

                        //console.log('STATUS:', this.form_requests)



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
                                this.btnloading = false;
                                this.$refs.form.reset();
                                this.$refs.observer.reset();

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

                                this.btnloading = false;

                            });
                        }
                    });
                    //console.log('yes you have changes');

                }


                //console.log("form_request:", this.form_requests);
                //console.log("form_request:", this.$store.state.snackbar.form_requests);


            },

            saveChangesToOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    //console.log("no changes found");
                    this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: 'No changes found',
                        color: '#1565C0',
                        icon: 'mdi-information-outline',
                    })
                }else{
                    this.btnloading = true;
                    this.$store.dispatch('updateExistingOffice', this.form).then(() => {

                        /*if (this.offices) {
                            this.$store.dispatch('setSnackbar', {
                                showing: true,
                                text: 'Successfully edited',
                                color: '#43A047',
                                icon: 'mdi-check-bold',
                            })
                            this.$store.dispatch("unsetDataTableLoader");
                            console.log(this.form.name);
                        }*/

                        //console.log('STATUS:', this.form_requests)



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

                                Object.assign(this.form_old, this.form)

                                this.btnloading = false;

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
                                this.btnloading = false;

                            });
                        }
                    });
                    //console.log('yes you have changes');
                    /*this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: 'Successfully edited',
                        color: '#43A047',
                        icon: 'mdi-check-bold',
                    })*/
                }
            }
        },
        mounted() {
            Object.assign(this.form_old, this.selected_office)
            Object.assign(this.form, this.selected_office)
            //console.log('ff',this.selected_office);

        }
    }

</script>

<style scoped>

</style>
