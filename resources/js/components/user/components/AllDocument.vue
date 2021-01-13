<template>
<v-card flat>
    <v-card-title primary-title>
        All Active Documents
    </v-card-title>
        <v-tabs v-model="tab"
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
    <v-card-text>
        <v-tabs-items v-model="tab">
    <v-tab-item
        v-for="item in ['Incoming','Outgoing']"
        :key="item"
        >
        <v-data-table
            v-if="documents"
            :headers="headers"
            :items="tableData"
            :items-per-page="10"
            item-key="id"
            hide-default-footer
            :loading="datatable_loader"
            loading-text="Loading... Please wait"
            class="elevation-1"
            :search="search"
            :single-expand="false"
            :expanded.sync="expanded"
            show-expand
        >
        <template v-slot:top>
        </template>
            <template v-slot:top>
                <v-text-field
                    v-model="search"
                    label="Search"
                    class="mx-4"
                />
            </template>
            <template v-slot:[`item.tracking_code`] = "{ item }">
                        <v-chip label dark :color="getTrackingCodeColor(item, item.document_type_id)" >
                            {{ item.tracking_code }}
                        </v-chip>
            </template>
            <template v-slot:[`item.view_more`]="{ item }">
                <td>
                    <v-btn
                        color="primary"
                        icon
                        @click="seeDocumentDetails(item)"
                    >
                        <v-icon>mdi-more</v-icon>
                    </v-btn>
                </td>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                    <v-row>
                        <v-col cols="12" sm="3">
                            <v-btn
                                text
                                color="#26A69A"
                                block
                            >
                                <v-icon left>
                                    mdi-pencil
                                </v-icon>
                                Edit
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="3">
                            <v-btn
                                @click.prevent="redirectToReceivePage(item)"
                                text
                                color="#FFCA28"
                                block
                            >
                                <v-icon left>
                                    mdi-email-send-outline
                                </v-icon>
                                Receive
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="3">
                            <v-btn
                                link @click.prevent="redirectToReceivePage(item)"
                                text
                                color="#9575CD"
                                block
                            >
                                <v-icon left>
                                    mdi-email-receive-outline
                                </v-icon>
                                Forward
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="3">
                            <v-btn
                                text
                                color="#F06292"
                                block
                            >
                                <v-icon left>
                                    mdi-email-off-outline
                                </v-icon>
                                Terminal
                            </v-btn>
                        </v-col>
                    </v-row>
                </td>
            </template>
        </v-data-table>
        <div class="text-center pt-2">
            <v-pagination
                v-model="current_page"
                :length="last_page"
                :total-visible="10"
            ></v-pagination>
        </div>
            </v-tab-item>
    </v-tabs-items>
    </v-card-text>
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

import TableModal from './TableModal'
import { colors } from '../../../constants';
import { mapGetters, mapActions } from "vuex";
export default {
    components: {TableModal},
    data() {
        return {
            tab: 0,
            search: '',
            expanded: [],
            headers: [
                { text: 'Tracking ID', value: 'tracking_code', sortable: false },
                { text: 'Subject', value: 'subject', sortable: false },
                { text: 'Source', value: 'is_external', sortable: false },
                { text: 'Type', value: 'document_type.name', sortable: false },
                { text: 'Originating Office', value: 'origin_office.name', sortable: false },
                { text: 'Current Office', value: 'destination_office.name', sortable: false },
                { text: 'Sender', value: 'sender.name', sortable: false },
                { text: 'Date Filed', value: 'date_filed', sortable: false },
                { text: 'View More', value: 'view_more', sortable: false },
                { text: 'Actions', value: 'data-table-expand', sortable: false },
            ],
            dialog: false,
            selected_document: '',
        }
    },
    watch: {
        current_page(new_value, old_value) {
            this.paginateDocuments(new_value);
        }
    },
    computed: {
        ...mapGetters(['documents', 'datatable_loader', 'auth_user']),
        tableData() {
            let type = this.tab ? 'originating_office' : 'destination_office_id'
             return this.documents.data.filter( doc => doc[type] == this.auth_user.office_id )
        },
        offices() {
            return this.$store.state.offices.offices;
        },
        document_types() {
            return this.$store.state.documents.document_types;
        },
        current_page: {
            get() {
                return this.$store.state.documents.documents.current_page;
            },
            set(value) {
                return this.$store.commit('SET_CURRENT_PAGE', value);
            }
        },
        last_page: {
            get() {
                return this.$store.state.documents.documents.last_page;
            },
        },

    },
    methods: {
        checkIfID(string) {
            return /^-?\d+$/.test(string);
        },
        getTrackingCodeColor(document, document_type_id) {
            document.color = '';
            document.color = colors[document_type_id];
            return colors[document_type_id];
        },
        seeDocumentDetails(document) {
            this.selected_document = document;
            this.dialog = true;
        },
        closeDialog(){
            this.dialog = false;
       },
        paginateDocuments(page_number) {
            this.$store.dispatch('setDataTableLoader');
            this.$store.dispatch('getActiveDocuments', page_number).then(() => {
                this.$store.dispatch('unsetDataTableLoader');
            });
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
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('getActiveDocuments').then(() => {
            if (this.offices && this.document_types) {
                this.$store.dispatch('unsetDataTableLoader');
            }
        });
    }
}
</script>

<style>
    /* TODO: Add media queries for tablet sized devices */
    @media screen and (max-width: 600px) {
        #document_label {
            font-size: 0.8em;
        }
    }
</style>