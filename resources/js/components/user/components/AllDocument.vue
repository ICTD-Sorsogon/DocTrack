<template>
    <v-card flat>
        <v-card-title primary-title>
            All Active Documents
            <v-row align="center" justify="end" class="pr-4">
                <v-btn color="primary" @click.prevent="getNewDocumentRecordForm"
                    >Create</v-btn
                >
            </v-row>
        </v-card-title>
        <v-tabs
            v-if="auth_user.role_id != 1"
            v-model="tab"
            full-width
            grow
            centered
        >
            <v-tab v-for="item in ['Incoming', 'Outgoing']" :key="item">
                {{ item }}
            </v-tab>
        </v-tabs>
        <data-table
            @editDocument="editDocument"
            @print="openDialog"
            v-if="auth_user.role_id === 1"
            :documents="documents"
            :datatable_loader="datatable_loader"
        ></data-table>
        <v-tabs-items v-if="auth_user.role_id != 1" v-model="tab">
            <v-tab-item v-for="item in ['Incoming', 'Outgoing']" :key="item">
                <data-table
                    @editDocument="editDocument"
                    @print="openDialog"
                    :documents="userDocuments"
                    :datatable_loader="datatable_loader"
                    :incoming="!tab"
                ></data-table>
            </v-tab-item>
        </v-tabs-items>
        <print-bar-code
            :item="item"
            @closeDialog="printDialog = false"
            :printDialog="printDialog"
        >
        </print-bar-code>
    </v-card>
</template>

<script>
import PrintBarCode from "./PrintBarCode";

import DataTable from "./DataTable";
import { mapGetters, mapActions } from "vuex";

export default {
    components: { DataTable, PrintBarCode },
    data() {
        return {
            printDialog: false,
            tab: 0,
            item: null
        };
    },
    watch: {
        tab(location) {
            if (this.auth_user.role_id != 1)
                this.$store.commit("UPDATE_TAB", location);
        }
    },
    computed: {
        ...mapGetters(["documents", "datatable_loader", "auth_user", "tabs"]),
        userDocuments() {
            let type = this.tab ? "outgoing" : "incoming";
            return JSON.parse(JSON.stringify(this.documents))[type];
        }
    },
    methods: {
        openDialog(item = false) {
            if (this.auth_user.role_id != 1 && !this.tab) {
                return;
            }
            this.item = item;
            this.printDialog = item && true;
        },
        checkIfID(string) {
            return /^-?\d+$/.test(string);
        },
        redirectToReceivePage(document) {
            if (this.$route.name !== "Receive Document") {
                this.$store.dispatch("setDocument", document);
                this.$router.push({ name: "Receive Document" });
            }
        },
        getNewDocumentRecordForm() {
            if (this.$route.name !== "Edit Document") {
                this.$store.dispatch("setLoader");
                this.$router.push({
                    name: "Edit Document",
                    params: { type: "create" }
                });
            }
        },
        editDocument(item) {
            if (this.$route.name !== "Edit Document") {
                this.$store.dispatch("setLoader");
                this.$router.push({
                    name: "Edit Document",
                    params: { item: item, id: item.id, type: "edit" }
                });
            }
        }
    },
    mounted() {
        this.$store.dispatch("unsetLoader");
        this.$store.dispatch("unsetDataTableLoader");
        this.$store.dispatch("getAllUsers");
        if (this.auth_user.role_id != 1) this.tab = this.tabs;
    }
};
</script>

<style>
@media screen and (max-width: 600px) {
    #document_label {
        font-size: 0.8em;
    }
}
</style>
