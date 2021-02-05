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
        <v-tabs v-if="auth_user.role_id != 1" v-model="tab"
        full-width
        grow
        centered
         >
      <v-tab
        v-for="item in ['Incoming','Outgoing']"
        :key="item"
      >
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
    <v-tabs-items v-if="auth_user.role_id != 1"  v-model="tab">
        <v-tab-item
            v-for="item in ['Incoming','Outgoing']"
            :key="item"
            >
        <data-table
         @editDocument="editDocument"
         @print="openDialog"
         :documents="userDocuments"
         :datatable_loader="datatable_loader"
         ></data-table>
        </v-tab-item>
    </v-tabs-items>
    <print-bar-code :item="item" @closeDialog="printDialog = false" :printDialog="printDialog">
    </print-bar-code>
</v-card>
</template>

<script>
import PrintBarCode from './PrintBarCode'

import DataTable from './DataTable';
import { mapGetters, mapActions } from "vuex";

export default {
    components: {DataTable, PrintBarCode},
    data() {
        return {
          printDialog: false,
          tab: 0,
          item: null,
        }
    },
    computed: {
        ...mapGetters(['documents', 'datatable_loader', 'auth_user']),
        userDocuments() {
            let type = this.tab ? 'originating_office' : 'destination_office_id'
            return JSON.parse(JSON.stringify(this.documents)).filter( doc => this.tab ? doc[type] == this.auth_user.office_id : doc['originating_office'] != this.auth_user.office_id  )
        },
    },
    methods: {
        openDialog (item = false){
         if(this.auth_user.role_id != 1 && !this.tab){
           return
         }
          this.item = item 
          this.printDialog = item && true
        },
        checkIfID(string) {
            return /^-?\d+$/.test(string);
        },
        redirectToReceivePage(document) {
            /**
            * TODO:
            * Save the document id or the document object to Vuex instead because the dynamic routing is messing
            * up the Vuex getter for auth_user creating a call for receive_document/auth_user which is non-existent
            **/
            if (this.$route.name !== "Receive Document") {
                this.$store.dispatch('setDocument', document);
                this.$router.push({ name: "Receive Document" });
            }
        },
        getNewDocumentRecordForm() {
            if (this.$route.name !== "Edit Document") {
            this.$store.dispatch("setLoader");
            this.$router.push({ name: "Edit Document", params:{type: 'Create'} });
            }
        },
        editDocument(id) {
            if (this.$route.name !== "Edit Document") {
                this.$store.dispatch("setLoader");
                this.$router.push({ name:"Edit Document", params: {id: id, type: 'Edit'}});
            }
        },
    },
  mounted() {
    this.$store.dispatch("unsetLoader");
    this.$store.dispatch("unsetDataTableLoader");
  },
};
</script>

<style>
/* TODO: Add media queries for tablet sized devices */
@media screen and (max-width: 600px) {
  #document_label {
    font-size: 0.8em;
  }
}
</style>