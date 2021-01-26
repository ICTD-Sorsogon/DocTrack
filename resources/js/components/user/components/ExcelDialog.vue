<template>
    <v-dialog v-model="excel_dialog" persistent scrollable width="900px">
        <v-container fluid  class="pr-0 pl-0"  style="padding:0px;">
            <v-card style="overflow-x:hidden; overflow-y:auto">
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
                    <v-form ref="form" lazy-validation>
                        <v-row v-if="dialog_for == 'exportOfficeList'">
                            <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12">
                                <v-btn @click="dowload" color="primary" style="width:100%" elevation="4" depressed large>CONFIRM EXPORT</v-btn>
                            </v-col>
                        </v-row>
                        <v-row v-if="dialog_for == 'importOfficeList'">
                            <v-col cols="12" xs="10" sm="10" md="10" lg="10" xl="10">
                                <v-file-input
                                    label="Browse Excel File"
                                    prepend-icon=""
                                    accept=".xlsx"
                                    ref="files"
                                    @change="upload"
                                    chips
                                    show-size
                                    counter
                                    outlined
                                    dense
                                    clearable
                                    clear-icon="mdi-delete"
                                />
                            </v-col>
                            <v-col cols="12" xs="2" sm="2" md="2" lg="2" xl="2">
                                <v-btn color="primary" style="width:100%" large :dark="valid" :loading="btnloading" :disabled="!valid" v-if="dialog_for == 'importOfficeList'" @click="uploadToDatabase"> UPLOAD </v-btn>
                            </v-col>
                            <v-col v-show="is_preview && excel_data.length > 0 && excel_table_headers.length > 0">
                                <v-card>
                                    <v-tabs v-model="tab" background-color="primary" dark>
                                        <v-tab v-for="item in excel_data" :key="item.tab">
                                            {{ item.tab }}
                                        </v-tab>
                                    </v-tabs>
                                    <v-tabs-items v-model="tab">
                                        <v-tab-item v-for="item in excel_data" :key="item.id">
                                            <v-card flat>
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
                                <v-alert dense outlined type="error" style="border: 1px solid rgba(0,0,0,0) !important;">
                                    ERROR FOUND, Please see error log bellow
                                </v-alert>
                                <ul>
                                    <dl>
                                        <dt>CONTENT</dt>
                                        <ol>
                                            <li v-for="error in excel_error[0]" :key="error.id">
                                                <strong>{{error.value}}</strong>
                                                        {{error.message}}
                                                <strong>[{{error.cell_position}}]</strong>
                                            </li>
                                        </ol>
                                    </dl>
                                </ul>
                            </v-col>
                        </v-row>
                    </v-form >
                </v-card-text>
            </v-card>
        </v-container>
    </v-dialog>
</template>

<script>
    import { mapGetters } from 'vuex';
    import * as Excel from 'exceljs';

    export default {
        props: ['excel_dialog', 'dialog_title', 'dialog_for'],
        data() {
            return {
                valid: false,
                btnloading: false,
                excel_data: [],
                excel_error:[[]],
                tab: null,
                excel_table_headers: [],
                is_preview: false,
                offices: [],
                marian_blue: '0675BB'
            }
        },
        computed: {
            ...mapGetters(['request'])
        },
        methods: {
            randomKey(){
                return Math.random().toString(36).substring(7)
            },
            cellPosition(sheet_index, column_index, row_index){
                return "ws#"+ (sheet_index + 1) + " " + ((column_index + 1) + 9).toString(36).toUpperCase() + (row_index + 1)
            },
            upload(file){
                try {
                    var fileExtension = file.name.split(".").pop().toLowerCase();
                    if (fileExtension == 'xlsx'){
                        this[this.dialog_for](file)
                    } else {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: 'To avoid error required file extension "xlsx"'
                        })
                    }
                } catch (error) {
                    this.excel_data = [];
                    this.excel_error = [[]];
                    this.valid = false;
                }
            },
            uploadToDatabase(){
                this.btnloading = true;
                if(this.excel_error[0].length < 1 && this.excel_data.length > 0){
                    const functionName = this.dialog_for + 'ToDatabase';
                    this[functionName]()
                }else{
                    this.$store.dispatch('setSnackbar', {
                        type: 'warning',
                        message: 'Upload request error, please check your file.'
                    });
                    this.btnloading = false;
                }
            },
            dowload(){
                this[this.dialog_for]()
            },
            importOfficeList(file){
                this.excel_table_headers = [];
                this.excel_table_headers.push(
                    { text: 'Office Name', align: 'start', value: 'Office_Name' },
                    { text: 'Office Code', value: 'Office_Code' },
                    { text: 'Address', value: 'Address' },
                    { text: 'Contact Number', value: 'Contact_Number' },
                    { text: 'Email Address', value: 'Email_Address' }
                );
                var offices = this.$store.state.offices.offices;
                this.offices = [];
                offices.forEach(office => {
                    this.offices.push(office.name.trim().toLowerCase().replace(/\s/g, ''));
                });
                this.excel_data = [];
                this.excel_error = [[]];
                const wb = new Excel.Workbook();
                const reader = new FileReader();
                reader.readAsArrayBuffer(file)
                reader.onload = function() {
                    const buffer = reader.result;
                    wb.xlsx.load(buffer).then(function(workbook) {
                        workbook.eachSheet(function(sheet, id) {
                            var sheetIndex = id - 1;
                            this.excel_data.push({
                                id: sheetIndex,
                                tab: (workbook.worksheets[sheetIndex].name).toUpperCase(),
                                content: []
                            });
                            sheet.eachRow({ includeEmpty: true }, function(row, rowNumber) {
                                var rowIndex = rowNumber - 1;
                                if (rowIndex > 0) {
                                    var dataCol = row.values;
                                    this.excel_data[sheetIndex].content.push({
                                        Office_Name: (dataCol[1] == undefined)? null : dataCol[1],
                                        Office_Code: (dataCol[2] == undefined)? null : dataCol[2],
                                        Address: (dataCol[3] == undefined)? null : dataCol[3],
                                        Contact_Number: (dataCol[4] == undefined)? null : dataCol[4],
                                        Email_Address: (dataCol[5] instanceof Object)?
                                            ((dataCol[5] == undefined)? null : dataCol[5].text) :
                                            ((dataCol[5] == undefined)? null : dataCol[5])
                                    });
                                }
                                row.eachCell({ includeEmpty: true }, function(cell, colNumber) {
                                    var colIndex = colNumber - 1;
                                    if (rowIndex > 0 && colIndex < 3) {
                                        if (cell.value != null && cell.value.trim() !== '') {
                                            if (colIndex == 0 && this.offices.includes((cell.value).trim().toLowerCase().replace(/\s/g, ''))) {
                                                this.excel_error[0].push({
                                                    id: this.randomKey(),
                                                    value: cell.value,
                                                    message: "already exist in the database",
                                                    cell_position: this.cellPosition(sheetIndex, colIndex, rowIndex),
                                                });
                                            }
                                        } else {
                                            if (cell.value == null || cell.value.trim == '') {
                                                this.excel_error[0].push({
                                                    id: this.randomKey(),
                                                    value: '',
                                                    message: "This cell is required ",
                                                    cell_position: this.cellPosition(sheetIndex, colIndex, rowIndex),
                                                });
                                            }
                                        }
                                    }
                                }.bind(this));
                            }.bind(this))
                        }.bind(this))
                        if (this.excel_error[0].length < 1) {
                            this.is_preview = true;
                            this.valid = true;
                        } else {
                            this.is_preview = false;
                            this.valid = false;
                        }
                    }.bind(this))
                }.bind(this)
            },
            importOfficeListToDatabase(){
                var office_data = { office_data: this.excel_data };
                this.$store.dispatch("importNewOffice", office_data).then(() => {
                    if(this.request.status == 'success') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'success',
                            message: this.request.message
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getOffices');
                            var offices = this.$store.state.offices.offices;
                            this.offices = [];
                            offices.forEach(office => {
                                this.offices.push(office.name.trim().toLowerCase().replace(/\s/g, ''));
                            });
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
            },
            exportOfficeList(){
                const header_color = this.marian_blue;
                const data = this.$store.state.offices.offices;
                let workbook = new Excel.Workbook()
                let worksheet = workbook.addWorksheet('Office List')
                worksheet.columns = [
                    { header: 'Office Name', key: 'Office_Name', width: 55 },
                    { header: 'Office Code', key: 'Office_Code', width: 13 },
                    { header: 'Address', key: 'Address', width: 60 },
                    { header: 'Contact Number', key: 'Contact_Number', width: 18 },
                    { header: 'Email Address', key: 'Email_Address', width: 35 }
                ]
                data.forEach((e, index) => {
                    worksheet.addRow({
                        Office_Name: e.name,
                        Office_Code: e.office_code,
                        Address: e.address,
                        Contact_Number: e.contact_number,
                        Email_Address: e.contact_email
                    })
                })
                worksheet.eachRow({ includeEmpty: false }, function (row, rowNumber) {
                    const headerColumns = ['A','B', 'C', 'D', 'E']
                    headerColumns.forEach((v) => {
                        if(rowNumber == 1){
                            worksheet.getCell(`${v}${rowNumber}`).style = {
                                fill: {
                                    type: 'pattern',
                                    pattern:'solid',
                                    fgColor:{ argb: header_color }
                                },
                                font: {
                                    color: {argb: "ffffff"},
                                    bold: true
                                }
                            }
                        }else{
                            worksheet.getCell(`${v}${rowNumber}`).style = {
                                border: {
                                    top: { style: 'thin' },
                                    left: { style: 'thin' },
                                    bottom: { style: 'thin' },
                                    right: { style: 'thin' }
                                }
                            }
                        }
                    })
                })
                worksheet.views = [{ state: 'frozen', xSplit: 0, ySplit: 1, activeCell: 'B2' }]
                this.saveExcelFile('Office List', workbook);
            },
            saveExcelFile(filename, workbook){
                var buff = workbook.xlsx.writeBuffer().then(function (data) {
                    var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    const date = new Date();
                    link.setAttribute('download', `${filename} _${date.getFullYear()}${date.getMonth()+1}${date.getDate()}.xlsx`);
                    document.body.appendChild(link);
                    link.click();
                });
            },
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
