<template>
<v-dialog v-model="excel_dialog" persistent scrollable fullscreen>
    <v-container fluid  class="pr-0 pl-0"  style="padding:0px;">
      <v-card v-if="selected_office" style="height:100%; width:100%; overflow:hidden;">

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
            <ValidationObserver ref="observer" v-slot="{ valid }">
                <v-form
                    ref="form"
                    lazy-validation
                >
                <v-row>
                <v-col cols="12" sm="12" md="12">
                    <ValidationProvider rules="required" v-slot="{ errors }">
                        <v-file-input label="File input" @change="fileSelected" outlined dense/>
                        <span>{{ errors[0] }}</span>
                    </ValidationProvider>
                </v-col>
                </v-row>
                <v-row justify="end">
                    <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="form.form_mode == 'new_office'" @click="saveNewOffice"> SAVE </v-btn>
                    <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="form.form_mode == 'edit_office'" @click="saveChangesToOffice"> SAVE CHANGES </v-btn>
                </v-row>
                </v-form >
            </ValidationObserver>

        </v-card-text>
      </v-card>
      </v-container>
    </v-dialog>
</template>

<script>
    import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';
    import { email, required } from '../../../validate'
    import { mapGetters } from 'vuex';

    import XLSX from 'xlsx';

    export default {
        components: { ValidationProvider, ValidationObserver },
        props: ['excel_dialog', 'dialog_title'],
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
                valid: true,
                btnloading: false,
                excel_error:[[], [], []],
                //file: []
            }
        },
        computed: {
            //...mapGetters(['form_requests']),
            form_requests(){
                return this.$store.state.snackbars.form_requests;
            },
            selected_office(){
                    return {id: '',
                    name: '',
                    address: '',
                    office_code: '',
                    contact_number: '',
                    contact_email: '',
                    form_mode: ''}
            }

        },
        methods: {
            randomKey(){
                return Math.random().toString(36).substring(7)
            },
            cellPosition(sheet_index, column_index, row_index){
                return "#"+ (sheet_index + 1) + " " + ((column_index + 1) + 9).toString(36).toUpperCase() + (row_index + 1)
            },
            fileSelected(file){

                var required_header = [
                    'Office_Name',
                    'Office_Code',
                    'Address',
                    'Contact_Number',
                    'Email_Address'
                ];

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
                                    this.excel_error[2].push({
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
                                            this.excel_error[1].push({
                                                id: this.randomKey(),
                                                value: '',
                                                message: "Header must have 5 column.",
                                                cell_position: this.cellPosition(i, C, R),
                                            });
                                        }
                                        if(R > 0 && C < 3){
                                            this.excel_error[2].push({
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
                                            this.excel_error[1].push({
                                                id: this.randomKey(),
                                                value: cell.v,
                                                message: "Required header did not match, download the sample excel file" + ' suggestion: ' + required_header[C],
                                                cell_position: this.cellPosition(i, C, R),
                                            });
                                        }
                                    }
                                }
                            }
                            /*_this.preview_excel_sheet_data.push({
                                title: sheetName,
                                name: i,
                                content: XLSX.utils.sheet_to_json(worksheet),
                                nameoftab: sheetName.replace(/\s/g, ''),
                                batch: ''
                            });
                            _this.current_tab_content.push(XLSX.utils.sheet_to_json(worksheet));*/
                        }
                        /*if(_this.excel_validation_error[0].length < 1 &&
                            _this.excel_validation_error[1].length < 1 &&
                            _this.excel_validation_error[2].length < 1
                        ){
                            _this.is_preview = true;
                        }else{
                            _this.is_preview = false;
                        }
                        _this.defaultTabSelected();
                        _this.is_hasfile = true;
                        _this.is_import = false;*/
                    }.bind(this)

                } catch (error) {
                    console.log('error found');
                }

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
