<template>
    <v-dialog v-model="excel_dialog" persistent scrollable width="900px">
        <v-container fluid class="pr-0 pl-0" style="padding:0px;">
            <v-card style="overflow-x:hidden; overflow-y:auto">
                <v-row>
                    <v-col v-bind="bp([11, 10, 9, 6, 6])">
                        <v-card-title primary-title>
                            {{ dialog_title }}
                        </v-card-title>
                    </v-col>
                    <v-col v-bind="bp([1, 2, 3, 6, 6])">
                        <v-card-actions class="mr-1">
                            <v-spacer></v-spacer>
                            <v-btn
                                x-large
                                color="gray"
                                @click="$emit('close-dialog')"
                                icon
                            >
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-col>
                </v-row>
                <v-card-text>
                    <v-form ref="form" lazy-validation>
                        <v-row
                            v-if="
                                dialog_type == 'export' &&
                                    dialog_for != 'masterList' &&
                                    dialog_for != 'advanceExport'
                            "
                        >
                            <v-col v-bind="bp(12)">
                                <v-btn
                                    @click="download"
                                    color="primary"
                                    style="width:100%"
                                    elevation="4"
                                    depressed
                                    large
                                    >CONFIRM EXPORT</v-btn
                                >
                            </v-col>
                        </v-row>

                        <archive-report
                            v-if="
                                (this.dialog_type == 'export' &&
                                    this.dialog_for == 'masterList') ||
                                    this.dialog_for == 'advanceExport'
                            "
                            :dialog_for="dialog_for"
                        />

                        <v-row v-if="dialog_type == 'import'">
                            <v-col v-bind="bp(10)">
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
                            <v-col v-bind="bp(2)">
                                <v-btn
                                    color="primary"
                                    style="width:100%"
                                    large
                                    :dark="valid"
                                    :loading="btnloading"
                                    :disabled="!valid"
                                    v-if="dialog_type == 'import'"
                                    @click="uploadToDatabase"
                                >
                                    UPLOAD
                                </v-btn>
                            </v-col>
                            <v-col
                                v-show="
                                    is_preview &&
                                        excel_data.length > 0 &&
                                        excel_table_headers.length > 0
                                "
                            >
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
                                                <v-data-table
                                                    :headers="
                                                        excel_table_headers
                                                    "
                                                    :items="item.content"
                                                    class="elevation-1"
                                                >
                                                    <template
                                                        v-slot:header.Office_Name="{
                                                            header
                                                        }"
                                                    >
                                                        {{
                                                            header.text.toUpperCase()
                                                        }}
                                                    </template>
                                                </v-data-table>
                                            </v-card>
                                        </v-tab-item>
                                    </v-tabs-items>
                                </v-card>
                            </v-col>
                            <v-col
                                v-show="!is_preview && excel_data.length > 0"
                            >
                                <v-alert
                                    dense
                                    outlined
                                    type="error"
                                    style="border: 1px solid rgba(0,0,0,0) !important;"
                                >
                                    ERROR FOUND, Please see error log bellow
                                </v-alert>
                                <ul>
                                    <dl>
                                        <dt>CONTENT</dt>
                                        <ol>
                                            <li
                                                v-for="error in excel_error[0]"
                                                :key="error.id"
                                            >
                                                <strong>{{
                                                    error.value
                                                }}</strong>
                                                {{ error.message }}
                                                <strong
                                                    >[{{
                                                        error.cell_position
                                                    }}]</strong
                                                >
                                            </li>
                                        </ol>
                                    </dl>
                                </ul>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-container>
    </v-dialog>
</template>

<script>
import { mapGetters } from "vuex";
import { breakpoint } from "../../../constants";
import ArchiveReport from "./ArchiveReport";

export default {
    props: ["param"],
    components: { ArchiveReport },
    data() {
        return {
            valid: false,
            btnloading: false,
            excel_data: [],
            excel_error: [[]],
            tab: null,
            excel_table_headers: [],
            is_preview: false,
            offices: []
        };
    },
    computed: {
        ...mapGetters(["request", "document_types", "auth_user"]),
        excel_dialog() {
            return this.param.visible;
        },
        dialog_title() {
            return this.param.title;
        },
        dialog_for() {
            return this.param.func;
        },
        dialog_type() {
            return this.param.type;
        }
    },
    methods: {
        bp(col) {
            return breakpoint(col);
        },
        randomKey() {
            return Math.random()
                .toString(36)
                .substring(7);
        },
        cellPosition(sheet_index, column_index, row_index) {
            return (
                "ws#" +
                (sheet_index + 1) +
                " " +
                (column_index + 1 + 9).toString(36).toUpperCase() +
                (row_index + 1)
            );
        },
        upload(file) {
            try {
                var fileExtension = file.name
                    .split(".")
                    .pop()
                    .toLowerCase();
                if (fileExtension == "xlsx") {
                    this[this.dialog_for](file);
                } else {
                    this.$store.dispatch("setSnackbar", {
                        type: "error",
                        message: 'To avoid error required file extension "xlsx"'
                    });
                }
            } catch (error) {
                this.excel_data = [];
                this.excel_error = [[]];
                this.valid = false;
            }
        },
        uploadToDatabase() {
            this.btnloading = true;
            if (this.excel_error[0].length < 1 && this.excel_data.length > 0) {
                const functionName = this.dialog_for + "ToDatabase";
                this[functionName]();
            } else {
                this.$store.dispatch("setSnackbar", {
                    type: "warning",
                    message: "Upload request error, please check your file."
                });
                this.btnloading = false;
            }
        },
        download() {
            this[this.dialog_for]();
        },
        async importOfficeList(file) {
            const Excel = await require("exceljs");

            this.excel_table_headers = [];
            this.excel_table_headers.push(
                { text: "Office Name", align: "start", value: "Office_Name" },
                { text: "Office Code", value: "Office_Code" },
                { text: "Address", value: "Address" },
                { text: "Contact Number", value: "Contact_Number" },
                { text: "Email Address", value: "Email_Address" }
            );
            var offices = this.$store.state.offices.offices;
            this.offices = [];
            offices.forEach(office => {
                this.offices.push(
                    office.name
                        .trim()
                        .toLowerCase()
                        .replace(/\s/g, "")
                );
            });
            this.excel_data = [];
            this.excel_error = [[]];
            const wb = new Excel.Workbook();
            const reader = new FileReader();
            reader.readAsArrayBuffer(file);
            reader.onload = function() {
                const buffer = reader.result;
                wb.xlsx.load(buffer).then(
                    function(workbook) {
                        workbook.eachSheet(
                            function(sheet, id) {
                                var sheetIndex = id - 1;
                                this.excel_data.push({
                                    id: sheetIndex,
                                    tab: workbook.worksheets[
                                        sheetIndex
                                    ].name.toUpperCase(),
                                    content: []
                                });
                                sheet.eachRow(
                                    { includeEmpty: true },
                                    function(row, rowNumber) {
                                        var rowIndex = rowNumber - 1;
                                        if (rowIndex > 0) {
                                            var dataCol = row.values;
                                            this.excel_data[
                                                sheetIndex
                                            ].content.push({
                                                Office_Name:
                                                    dataCol[1] == undefined
                                                        ? null
                                                        : dataCol[1],
                                                Office_Code:
                                                    dataCol[2] == undefined
                                                        ? null
                                                        : dataCol[2],
                                                Address:
                                                    dataCol[3] == undefined
                                                        ? null
                                                        : dataCol[3],
                                                Contact_Number:
                                                    dataCol[4] == undefined
                                                        ? null
                                                        : dataCol[4],
                                                Email_Address:
                                                    dataCol[5] instanceof Object
                                                        ? dataCol[5] ==
                                                          undefined
                                                            ? null
                                                            : dataCol[5].text
                                                        : dataCol[5] ==
                                                          undefined
                                                        ? null
                                                        : dataCol[5]
                                            });
                                        }
                                        row.eachCell(
                                            { includeEmpty: true },
                                            function(cell, colNumber) {
                                                var colIndex = colNumber - 1;
                                                if (
                                                    rowIndex > 0 &&
                                                    colIndex < 3
                                                ) {
                                                    if (
                                                        cell.value != null &&
                                                        cell.value.trim() !== ""
                                                    ) {
                                                        if (
                                                            colIndex == 0 &&
                                                            this.offices.includes(
                                                                cell.value
                                                                    .trim()
                                                                    .toLowerCase()
                                                                    .replace(
                                                                        /\s/g,
                                                                        ""
                                                                    )
                                                            )
                                                        ) {
                                                            this.excel_error[0].push(
                                                                {
                                                                    id: this.randomKey(),
                                                                    value:
                                                                        cell.value,
                                                                    message:
                                                                        "already exist in the database",
                                                                    cell_position: this.cellPosition(
                                                                        sheetIndex,
                                                                        colIndex,
                                                                        rowIndex
                                                                    )
                                                                }
                                                            );
                                                        }
                                                    } else {
                                                        if (
                                                            cell.value ==
                                                                null ||
                                                            cell.value.trim ==
                                                                ""
                                                        ) {
                                                            this.excel_error[0].push(
                                                                {
                                                                    id: this.randomKey(),
                                                                    value: "",
                                                                    message:
                                                                        "This cell is required ",
                                                                    cell_position: this.cellPosition(
                                                                        sheetIndex,
                                                                        colIndex,
                                                                        rowIndex
                                                                    )
                                                                }
                                                            );
                                                        }
                                                    }
                                                }
                                            }.bind(this)
                                        );
                                    }.bind(this)
                                );
                            }.bind(this)
                        );
                        if (this.excel_error[0].length < 1) {
                            this.is_preview = true;
                            this.valid = true;
                        } else {
                            this.is_preview = false;
                            this.valid = false;
                        }
                    }.bind(this)
                );
            }.bind(this);
        },
        importOfficeListToDatabase() {
            var office_data = { office_data: this.excel_data };
            this.$store.dispatch("importNewOffice", office_data).then(() => {
                if (this.request.status == "success") {
                    this.$store
                        .dispatch("setSnackbar", {
                            type: "success",
                            message: this.request.message
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch("getOffices");
                            var offices = this.$store.state.offices.offices;
                            this.offices = [];
                            offices.forEach(office => {
                                this.offices.push(
                                    office.name
                                        .trim()
                                        .toLowerCase()
                                        .replace(/\s/g, "")
                                );
                            });
                        });
                } else if (this.request.status == "failed") {
                    this.$store
                        .dispatch("setSnackbar", {
                            type: "error",
                            message: this.request.message
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                }
            });
        },
        exportLogs() {
            import("./modules/logs").then(({ logs }) => {
                logs({ data: this.$store.state.users.logs });
            });
        },
        exportUser() {
            import("./modules/users").then(({ users }) => {
                users({ data: this.$store.state.users.all_users });
            });
        },
        exportOfficeList() {
            import("./modules/officelist").then(({ officelist }) => {
                officelist({ data: this.$store.state.offices.offices });
            });
        }
    },
    mounted() {}
};
</script>

<style scoped></style>
