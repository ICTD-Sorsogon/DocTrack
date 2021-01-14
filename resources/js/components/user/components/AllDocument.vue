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
    <data-table v-if="auth_user.role_id === 1" :documents="documents" :datatable_loader="datatable_loader"></data-table>
    <v-tabs-items v-if="auth_user.role_id != 1"  v-model="tab">
        <v-tab-item
            v-for="item in ['Incoming','Outgoing']"
            :key="item"
            >
        <data-table :documents="userDocuments" :datatable_loader="datatable_loader"></data-table>
        </v-tab-item>
    </v-tabs-items>
    <table-modal 
        @close-dialog="closeDialog"
        :dialog="dialog" 
        v-if="selected_document" 
        :selected_document="selected_document"
    ></table-modal>
</v-card>
</template>

<script>
/**
 * TODO:
 * Build documents before inserting to table
 * FIXME: Search only displays rows from the current page
**/

import TableModal from './TableModal';
import DataTable from './DataTable';
import { mapGetters, mapActions } from "vuex";

export default {
    components: {TableModal, DataTable},
    data() {
        return {
            tab: 0,
            selected_document: ''
        }
    },
    computed: {
        ...mapGetters(['documents', 'datatable_loader', 'auth_user']),
        userDocuments() {
            let type = this.tab ? 'originating_office' : 'destination_office_id'
            return this.documents.filter( doc => doc[type] == this.auth_user.office_id )
        },
        offices() {
            return this.$store.state.offices.offices;
        },
        document_types() {
            return this.$store.state.documents.document_types;
        },
    },
    methods: {
        checkIfID(string) {
            return /^-?\d+$/.test(string);
        },

        seeDocumentDetails(document) {
            this.selected_document = document;
            this.dialog = true;
        },
        closeDialog(){
            this.dialog = false;
       },

        redirectToReceivePage(document) {
            /**
            * TODO:
            * Save the document id or the document object to Vuex instead because the dynamic routing is messing
            * up the Vuex getter for auth_user creating a call for receive_document/auth_user which is non-existent
            **/
            this.$store.dispatch('setDocument', document);
            this.$router.push(`receive_document`);
        },
    },
    getTrackingCodeColor(document, document_type_id) {
      document.color = "";
      document.color = colors[document_type_id];
      return colors[document_type_id];
    },
    findDocumentTypeName(document, document_type_id) {
      var document_type = this.document_types.find(
        (element) => element.id == document_type_id
      );
      if (document_type != null) {
        document.type_name = document_type.name;
        return document_type.name;
      }
    },
    findDocumentOriginatingOfficeName(document, originating_office) {
      var office = this.offices.find(
        (element) => element.id == originating_office
      );
      document.originating_office_name = "";
      if (office != null) {
        document.originating_office_name = office.name;
        return office.name;
      }
    },
    findDocumentCurrentOfficeName(document, current_office) {
      var office = this.offices.find((element) => element.id == current_office);
      document.current_office_name = "";
      if (office != null) {
        document.current_office_name = office.name;
        return office.name;
      }
    },
    findDocumentSenderName(document, sender_id) {
      var sender = this.users.find((element) => element.id == sender_id);
      document.sender_fullname = "";
      if (sender != null) {
        document.sender_fullname = sender.full_name;
        return sender.full_name;
      }
    },
    seeDocumentDetails(document) {
      this.selected_document = document;
      this.dialog = true;
    },
    paginateDocuments(page_number) {
      this.$store.dispatch("setDataTableLoader");
      this.$store.dispatch("getActiveDocuments", page_number).then(() => {
        this.$store.dispatch("unsetDataTableLoader");
      });
    },
    getNewDocumentRecordForm() {
      if (this.$route.name !== "New Document") {
        this.$store.dispatch("setLoader");
        this.$router.push({ name: "New Document" });
      }
    },
    editDocument(id) {
      if (this.$route.name !== "Edit Document") {
        this.$store.dispatch("setLoader");
        this.$router.push({ name:"Edit Document", params: {id}});
      }
    },
    redirectToReceivePage(document) {
      /**
       * TODO:
       * Save the document id or the document object to Vuex instead because the dynamic routing is messing
       * up the Vuex getter for auth_user creating a call for receive_document/auth_user which is non-existent
       **/
      this.$store.dispatch("setDocument", document);
      this.$router.push(`receive_document`);
    },
  mounted() {
    this.$store.dispatch("unsetLoader");
    this.$store.dispatch("getActiveDocuments").then(() => {
      if (this.offices && this.document_types) {
        this.$store.dispatch("unsetDataTableLoader");
      }
    });
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
.btn_close {
  background: blue;
  opacity: 0.7;
}
</style>