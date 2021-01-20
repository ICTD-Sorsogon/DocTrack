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
         :documents="userDocuments"
         :datatable_loader="datatable_loader"
         ></data-table>
        </v-tab-item>
    </v-tabs-items>
</v-card>
</template>

<script>
/**
 * TODO:
 * Build documents before inserting to table
 * FIXME: Search only displays rows from the current page
**/

import DataTable from './DataTable';
import { mapGetters, mapActions } from "vuex";

export default {
    components: {DataTable},
    data() {
        return {
            tab: 0,
        }
    },
    computed: {
        ...mapGetters(['documents', 'datatable_loader', 'auth_user']),
        userDocuments() {
            let type = this.tab ? 'originating_office' : 'destination_office_id'
            return JSON.parse(JSON.stringify(this.documents)).filter( doc => doc[type] == this.auth_user.office_id )
        },
    },
    methods: {
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
            this.$router.push({ name: "Edit Document" });
            }
        },
        editDocument(id) {
            if (this.$route.name !== "Edit Document") {
                this.$store.dispatch("setLoader");
                this.$router.push({ name:"Edit Document", params: {id}});
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