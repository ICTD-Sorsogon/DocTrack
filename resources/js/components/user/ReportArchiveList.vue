<template>
    <div>
        <v-card>
            <v-card-title primary-title class="pr-0">
                <v-toolbar flat>
                    <v-toolbar-title>Archive List Report</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical />
                    <v-spacer />

                    <v-menu left bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                class="mb-2 ma-1"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-dots-vertical</v-icon> GENERATE
                                REPORT
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item
                                :key="2"
                                @click.stop="openDialog('master_list')"
                            >
                                <v-icon class="ma-1"
                                    >mdi-file-export-outline</v-icon
                                >
                                MASTER LIST
                            </v-list-item>
                            <v-list-item
                                :key="1"
                                @click.stop="openDialog('advance_export')"
                            >
                                <v-icon class="ma-1">mdi-cogs</v-icon> CUSTOM
                                REPORT
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <v-menu left bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                class="mb-2 ma-1"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-dots-vertical</v-icon> SEARCH OPTION
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item
                                :key="1"
                                @click.stop="searchOption = 'normal'"
                            >
                                <v-icon class="ma-1 mr-1">mdi-magnify</v-icon>
                                NORMAL
                            </v-list-item>
                            <v-list-item
                                :key="2"
                                @click.stop="searchOption = 'advance'"
                            >
                                <v-icon class="ma-1 mr-1">mdi-cogs</v-icon>
                                ADVANCED
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
                        <v-col class="d-flex" v-bind="bp(3)">
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
                            <v-divider class=" mt-0" inset vertical dense />
                        </v-col>
                        <v-col class="d-flex" v-bind="bp(9)" v-if="isByYear">
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
                                            <v-icon
                                                :color="
                                                    filterYearSelected.length >
                                                    0
                                                        ? 'red darken-4'
                                                        : ''
                                                "
                                            >
                                                {{ icon }}
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title
                                                >Select All</v-list-item-title
                                            >
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2" />
                                </template>
                            </v-select>
                        </v-col>
                        <v-col class="d-flex" v-bind="bp(9)" v-if="!isByYear">
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
                                    :max="
                                        new Date(filterDateTo)
                                            .toISOString()
                                            .substr(0, 10)
                                    "
                                    :min="
                                        Math.min(
                                            ...distinctYearFromDB
                                        ).toString()
                                    "
                                >
                                    <v-spacer />
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
                                        @click="
                                            $refs.dialog.save(filterDateFrom);
                                            filterChange(false, true);
                                        "
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
                                    />
                                </template>
                                <v-date-picker
                                    v-model="filterDateTo"
                                    scrollable
                                    :max="new Date().toISOString().slice(0, 10)"
                                    :min="
                                        new Date(filterDateFrom)
                                            .toISOString()
                                            .substr(0, 10)
                                    "
                                >
                                    <v-spacer />
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
                                        @click="
                                            $refs.dialog1.save(filterDateTo);
                                            filterChange(false, true);
                                        "
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-dialog>
                        </v-col>
                    </v-row>

                    <v-card
                        v-if="searchOption == 'advance'"
                        class="elevation-2 pt-5 pb-0 mb-5"
                        style="background-color:#E6F5FD;border:1px solid #B8B8B8;"
                    >
                        <advance-search
                            :minimumYear="
                                Math.min(...distinctYearFromDB).toString()
                            "
                            @searchParameter="advanceSearchQuery"
                            @changeSearch="resetAdvanceSearch"
                            @noSearchParam="resetData"
                        />
                    </v-card>

                    <v-row v-if="searchOption == 'normal'" class="d-flex">
                        <v-col v-bind="bp(12)">
                            <v-text-field
                                v-model="search"
                                label="Search Value"
                                class="mx-4"
                            />
                        </v-col>
                    </v-row>
                </template>

                <template v-slot:[`item.tracking_code`]="{ item }">
                    <v-chip
                        class="trackin"
                        @click="printDetails(item)"
                        label
                        dark
                        :color="
                            getTrackingCodeColor(item.document_type_id)
                        "
                    >
                        {{ item.tracking_code }}
                    </v-chip>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <v-row>
                        <v-col
                            v-if="auth_user.role_id == 1"
                            v-bind="restoreAttribute"
                        >
                            <v-btn
                                fab
                                icon
                                raised
                                x-small
                                color="primary"
                                title="Restore to Active"
                                style="margin-right:4px;"
                                @click.prevent="
                                    {
                                        restoreDocument(item);
                                    }
                                "
                                ><v-icon>mdi-backup-restore</v-icon></v-btn
                            >
                        </v-col>
                        <v-col v-bind="viewAttribute">
                            <v-btn
                                fab
                                icon
                                raised
                                x-small
                                color="primary"
                                title="View More"
                                @click.prevent="
                                    {
                                        selectDoc(item.id);
                                        viewDialog = true;
                                    }
                                "
                                ><v-icon>mdi-more</v-icon></v-btn
                            >
                        </v-col>
                    </v-row>
                </template>

                <template v-slot:[`item.destination`]="{ item }">
                    <v-tooltip
                        :key="destination.office_code"
                        v-for="destination in item.destination"
                        top
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip
                                color="primary"
                                v-bind="attrs"
                                v-on="on"
                                :x-small="item.destination.length > 1"
                            >
                                {{ destination.office_code }}
                            </v-chip>
                        </template>
                        <span>{{ destination.name }}</span>
                    </v-tooltip>
                </template>

                <template v-slot:[`item.created_at`]="{ item }">
                    {{ dateCreated(item) }}
                </template>

                <v-alert
                    slot="no-results"
                    :value="true"
                    type="error"
                    icon="mdi-alert"
                    align="left"
                >
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

        <excel-dialog
            v-if="xDialog.visible"
            :param="xDialog"
            @close-dialog="closeDialog('excel')"
        />

        <restore-document
            v-if="auth_user.role_id == 1 && restore"
            :param="restoreParam"
            @reload-data="filterChange"
            @close-dialog="closeDialog('restore')"
        />

        <print-bar-code
            :item="item"
            @closeDialog="printDialog = false"
            :printDialog="printDialog"
        />
    </div>
</template>

<script>
import ExcelDialog from "./components/ExcelDialog";
import TableModal from "./components/TableModal";
import { colors, breakpoint } from "../../constants";
import { mapGetters } from "vuex";
import AdvanceSearch from "./components/ArchiveAdvanceSearch";
import RestoreDocument from "./components/RestoreDocument.vue";
import PrintBarCode from "./components/PrintBarCode";

export default {
    components: {
        ExcelDialog,
        TableModal,
        AdvanceSearch,
        RestoreDocument,
        PrintBarCode
    },
    data() {
        return {
            activeDoc: null,
            viewDialog: false,
            headers: [
                { text: "Tracking ID", value: "tracking_code" },
                { text: "Subject", value: "subject" },
                { text: "Source", value: "is_external" },
                { text: "Type", value: "document_type.name" },
                { text: "Originating Office", value: "originating_office" },
                { text: "Destination Office", value: "destination" },
                { text: "Sender", value: "sender_name" },
                { text: "Date Created", value: "created_at" },
                { text: "Action", value: "actions", align: "center" }
            ],
            search: "",
            filterDateDialogFrom: false,
            filterDateFrom: new Date().toISOString().substr(0, 10),
            filterDateDialogTo: false,
            filterDateTo: new Date().toISOString().substr(0, 10),
            searchOption: "normal",
            table: [],
            distinctYearFromDB: [],
            filterYearSelected: [new Date().getFullYear().toString()],
            filterOptionList: ["Date", "Year"],
            filterOptionSelected: "Date",
            isByYear: false,
            filter: {},
            tableData: [],
            restore: false,
            restoreParam: [],
            printDialog: false,
            item: null,
            xDialog: {
                title: "",
                func: "",
                type: "",
                data: [],
                visible: false
            }
        };
    },
    computed: {
        ...mapGetters([
            "document_types",
            "offices",
            "all_users",
            "documentsArchive",
            "datatable_loader",
            "auth_user",
            "request"
        ]),
        extendedData() {
            if (this.documentsArchive.length) {
                var selected = this.documentsArchive[0].selected;
                var dataRes =
                    selected.filter == "Year"
                        ? selected.year.data
                        : selected.date.data;
                var data = JSON.parse(JSON.stringify(dataRes)).map(doc => {
                    doc.is_external = doc.is_external ? "External" : "Internal";
                    doc.sender_name = doc.sender?.fullname ?? doc.sender_name;
                    doc.originating_office =
                        doc.origin_office?.name ?? doc.originating_office;
                    doc.created_at = new Date(doc.created_at)
                        .toISOString()
                        .substr(0, 10);
                    return doc;
                });
                this.tableData = data;
                return data;
            }
        },
        selected_document() {
            if (this.documentsArchive.length) {
                return this.extendedData.find(
                    data => data.id == this.activeDoc
                );
            }
        },
        allData() {
            return (
                this.filterYearSelected.length ===
                this.distinctYearFromDB.length
            );
        },
        icon() {
            if (this.allData) return "mdi-minus-box";
            return "mdi-checkbox-blank-outline";
        },
        styleActRestore() {
            return {
                top: "50%",
                textAlign: "right",
                paddingLeft: "0px !important"
            };
        },
        classActRestore() {
            return "pr-0";
        },
        restoreAttribute() {
            return {
                ...this.bp(this.auth_user.role_id == 1 ? 6 : 12),
                style: {
                    top: "50%",
                    textAlign: "right",
                    paddingLeft: "0px !important",
                    paddingRight: "0px"
                }
            };
        },
        viewAttribute() {
            let isAdmin = this.auth_user.role_id == 1;
            return {
                ...this.bp(isAdmin ? 6 : 12),
                style: {
                    top: "50%",
                    textAlign: isAdmin ? "left" : "center",
                    paddingLeft: isAdmin ? "0px" : ""
                }
            };
        }
    },
    methods: {
        dateCreated(item) {
            return !this.getMyDocument(item)
                ? item.created_at
                : new Date(this.getMyDocument(item).created_at)
                      .toISOString()
                      .substr(0, 10);
        },
        getMyDocument(document) {
            if (
                this.auth_user.role_id > 1 &&
                document.origin_office.id != this.auth_user.office_id
            ) {
                return document.incoming_trashed.find(
                    o => o.destination_office == this.auth_user.office_id
                );
            }
            return false;
        },
        async advanceSearchQuery(param) {
            var parameter = [];
            const column = [
                "trackingId",
                "subject",
                "source",
                "type",
                "originating",
                "destination",
                "sender",
                "dateCreated"
            ];
            const {
                trackingId,
                subject,
                source,
                type,
                originating,
                destination,
                sender,
                dateCreated
            } = param;
            let ptrackingId = (trackingId == null
              ? ""
              : trackingId.trim() != "")
                ? trackingId.trim()
                : "";
            let psubject = (subject == null
              ? ""
              : subject.trim() != "")
                ? subject.trim()
                : "";
            let psource = source.length > 0 ? source : "";
            let ptype = type.length > 0 ? type : "";
            let poriginating = originating.length > 0 ? originating : "";
            let pdestination = destination.length > 0 ? destination : "";
            let psender = sender.length > 0 ? sender : "";
            let pdateCreated = dateCreated != null ? dateCreated : "";

            column.forEach(col => {
                if (eval("p" + col) != "") parameter.push("p" + col);
            });
            var data = this.extendedData.filter(document => {
                var findedData = false;
                var satisfied = [];
                parameter.forEach(par => {
                    switch (par) {
                        case "ptrackingId":
                            satisfied.push(
                                ptrackingId == document.tracking_code
                                    ? true
                                    : false
                            );
                            break;
                        case "psubject":
                            function map(str) {
                                return str.replace(/  +/g, " ").split(" ");
                            }
                            var s = [];
                            map(document.subject).some(el => {
                                if (map(psubject).includes(el)) s.push(true);
                                s.push(false);
                            });
                            satisfied.push(
                                s.filter(x => x == true).length >= 1
                                    ? true
                                    : false
                            );
                            break;
                        case "psource":
                            satisfied.push(
                                psource.includes(document.is_external)
                                    ? true
                                    : false
                            );
                            break;
                        case "ptype":
                            satisfied.push(
                                ptype.includes(document.document_type.name)
                                    ? true
                                    : false
                            );
                            break;
                        case "poriginating":
                            satisfied.push(
                                poriginating
                                    .map(o => o.id)
                                    .includes(document.origin_office.id)
                                    ? true
                                    : false
                            );
                            break;
                        case "pdestination":
                            satisfied.push(
                                document.destination
                                    .map(d => d.id)
                                    .some(d =>
                                        pdestination.map(o => o.id).includes(d)
                                    )
                                    ? true
                                    : false
                            );
                            break;
                        case "psender":
                            satisfied.push(
                                psender
                                    .map(s => s.id)
                                    .includes(document.sender.id)
                                    ? true
                                    : false
                            );
                            break;
                        case "pdateCreated":
                            satisfied.push(
                                pdateCreated == document.created_at
                                    ? true
                                    : false
                            );
                            break;
                    }
                });
                if (satisfied.every(i => i == true)) findedData = true;
                return findedData;
            });
            this.tableData = data;
        },
        resetAdvanceSearch() {
            this.searchOption = "normal";
            this.resetData();
        },
        resetData() {
            this.tableData = this.extendedData;
        },
        bp(col) {
            return breakpoint(col);
        },
        restoreDocument(item) {
            this.restore = true;
            this.restoreParam = {
                dialog: true,
                title: "Restore Document",
                document: item,
                filter: this.filterOptionSelected
            };
        },
        filterBy() {
            this.filter = {};
            this.filter.action = "update";
            this.filter.filterBy = this.filterOptionSelected;
            var selected = this.documentsArchive[0];

            if (this.filterOptionSelected == "Year") {
                this.isByYear = true;
                this.filterYearSelected = selected.selected.year.list;
                this.filter.year = { list: selected.selected.year.list };
                this.filter.data = selected.selected.year.data;
            } else {
                this.isByYear = false;
                this.filterDateFrom = selected.selected.date.list[0];
                this.filterDateTo = selected.selected.date.list[1];
                this.filter.date = { list: selected.selected.date.list };
                this.filter.data = selected.selected.date.data;
            }
            this.filter.yearFromDb = selected.year;
            this.$store.commit("GET_ALL_ARCHIVE_DOCUMENTS", this.filter);
        },
        mountState() {
            if (!this.documentsArchive.length) {
                this.filter = {};
                this.filter.action = "new";
                this.filter.filterBy = this.filterOptionSelected;
                this.filter.date = {
                    list: [this.filterDateFrom, this.filterDateTo]
                };
                this.$store
                    .dispatch("getArchiveDocuments", this.filter)
                    .then(() => {
                        this.distributeState();
                    });
            } else {
                if (this.documentsArchive[0].hasNewTerminated) {
                    this.filter = {};
                    this.filter.action = "update";
                    this.filter.filterBy = this.documentsArchive[0].selected.filter;
                    if (this.documentsArchive[0].selected.filter == "Year") {
                        this.filter.year = {
                            list: this.documentsArchive[0].selected.year.list
                        };
                    } else {
                        this.filter.date = {
                            list: this.documentsArchive[0].selected.date.list
                        };
                    }
                    this.$store
                        .dispatch("getArchiveDocuments", this.filter)
                        .then(() => {
                            this.distributeState();
                        });
                } else {
                    this.distributeState();
                }
            }
        },
        distributeState() {
            var selected = this.documentsArchive[0];
            this.distinctYearFromDB = selected.year;
            this.filterOptionSelected = selected.selected.filter;
            if (selected.selected.filter == "Year") {
                this.isByYear = true;
                this.filterYearSelected = selected.selected.year.list;
            } else {
                this.isByYear = false;
                this.filterDateFrom = selected.selected.date.list[0];
                this.filterDateTo = selected.selected.date.list[1];
            }
        },
        filterChange(isYearChange) {
            this.filter = {};
            this.filter.action = "update";
            this.filter.filterBy = this.filterOptionSelected;
            if (isYearChange) {
                this.filter.year = { list: this.filterYearSelected };
            } else {
                this.filter.date = {
                    list: [this.filterDateFrom, this.filterDateTo]
                };
            }
            this.$store
                .dispatch("getArchiveDocuments", this.filter)
                .then(() => {
                    this.distributeState();
                });
        },
        selectAllYear() {
            this.$nextTick(() => {
                if (this.allData) {
                    this.filterYearSelected = [];
                    this.filterChange(true, false);
                } else {
                    this.filterYearSelected = this.distinctYearFromDB.slice();
                    this.filterChange(true, false);
                }
            });
        },
        getTrackingCodeColor(document_type_id) {
            return colors[document_type_id];
        },
        printDetails(item) {
            this.item = item;
            this.printDialog = item && true;
        },
        openDialog(key) {
            switch (key) {
                case "master_list":
                    this.xDialog.title = "Master List - Excel";
                    this.xDialog.func = "masterList";
                    break;
                case "advance_export":
                    this.xDialog.title = "Custom Report - Excel";
                    this.xDialog.func = "advanceExport";
                    break;
            }
            this.xDialog.type = "export";
            this.xDialog.visible = true;
        },
        selectDoc(id) {
            this.activeDoc = id;
        },
        closeDialog(key) {
            switch (key) {
                case "excel":
                    this.xDialog.visible = false;
                    break;
                case "dialog":
                    this.viewDialog = false;
                    break;
                case "restore":
                    this.restore = false;
                    break;
            }
        }
    },
    async mounted() {
        await this.mountState();
        this.$store.dispatch("unsetLoader");
    }
};
</script>
