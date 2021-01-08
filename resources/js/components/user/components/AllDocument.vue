<template>
<v-card flat>
    <v-card-title primary-title>
        All Active Documents
    </v-card-title>
    <v-card-text>
        <v-data-table
            v-if="documents"
            :headers="headers"
            :items="documents.data"
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
                <v-text-field
                    v-model="search"
                    label="Search"
                    class="mx-4"
                />
            </template>
            <template v-slot:[`item.tracking_code`] = "{ item }">
                        <v-chip
                            label
                            dark
                            :color="getTrackingCodeColor(item, item.document_type_id)"
                        >
                            {{ item.tracking_code }}
                        </v-chip>
            </template>
            <template v-slot:[`item.is_external`] = "{ item }">
                <td v-if="item.is_external">
                    External
                </td>
                <td v-else>
                    Internal
                </td>
            </template>
            <template v-slot:[`item.document_type_id`] = "{ item }">
                <div v-if="item">
                    {{ findDocumentTypeName(item, item.document_type_id) }}
                </div>
            </template>
            <template v-slot:[`item.originating_office`] = "{ item }">
                <div v-if="item && checkIfID(item.originating_office)">
                    {{ findDocumentOriginatingOfficeName(item, item.originating_office) }}
                </div>
                <div v-else>
                    {{ item.originating_office }}
                </div>
            </template>
            <template v-slot:[`item.current_office_id`] = "{ item }">
                <div v-if="item">
                    {{ findDocumentCurrentOfficeName(item, item.current_office_id) }}
                </div>
            </template>
            <template v-slot:[`item.sender_name`] = "{ item }">
                <div v-if="item && checkIfID(item.sender_name)">
                    {{ findDocumentSenderName(item, item.sender_name) }}
                </div>
                <div v-else>
                    {{ item.sender_name }}
                </div>
            </template>
            <template v-slot:[`item.is_terminal`] = "{ item }">
                <td v-if="item.is_terminal">
                    Yes
                </td>
                <td v-else>
                    No
                </td>
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
    </v-card-text>
    <v-dialog
      v-model="dialog"
      persistent
      scrollable
      max-width="1000px"
    >
        <v-card v-if="selected_document">
            <v-card-title primary-title>
                Document Details
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Tracking ID</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-chip
                                            label
                                            dark
                                            :color="selected_document.color"
                                            id="document_label"
                                        >
                                            {{ selected_document.tracking_code }}
                                        </v-chip>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>

                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Subject</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.subject}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>

                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Source</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title v-if="selected_document.is_external">External</v-list-item-title>
                                    <v-list-item-title v-else>Internal</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Type</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.type_name}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Originating Office</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.originating_office_name}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Current Office</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.current_office_name}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Sender</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.sender_fullname}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Date Filed</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.date_filed}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col cols="12" xl="4" lg="4" md="4" sm="12">
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Page Count</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.page_count}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <v-col cols="12" xl="4" lg="4" md="4" sm="12">
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Attachment Page Count</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.attachment_page_count}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <v-col cols="12" xl="4" lg="4" md="4" sm="12">
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Terminal</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title v-if="selected_document.is_terminal">Yes</v-list-item-title>
                                    <v-list-item-title v-else>No</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-divider inset/>
                <v-row>
                    <v-col>
                        <v-list
                            flat
                            subheader
                        >
                            <v-subheader>Remarks</v-subheader>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-icon>mdi-square-medium</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{selected_document.remarks}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    text
                    @click="dialog = false"
                >
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</v-card>
</template>

<script>
/**
 * TODO:
 * Build documents before inserting to table
 * FIXME: Search only displays rows from the current page
**/
import { colors } from '../../../constants';
import { mapGetters, mapActions } from "vuex";
export default {
    data() {
        return {
            search: '',
            expanded: [],
            headers: [
                { text: 'Tracking ID', value: 'tracking_code', sortable: false },
                { text: 'Subject', value: 'subject', sortable: false },
                { text: 'Source', value: 'is_external', sortable: false },
                { text: 'Type', value: 'document_type_id', sortable: false },
                { text: 'Originating Office', value: 'originating_office', sortable: false },
                { text: 'Current Office', value: 'current_office_id', sortable: false },
                { text: 'Sender', value: 'sender_name', sortable: false },
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
        ...mapGetters(['documents', 'datatable_loader']),
        offices() {
            return this.$store.state.offices.offices;
        },
        document_types() {
            return this.$store.state.documents.document_types;
        },
        users() {
            return this.$store.state.users.all_users;
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
        findDocumentTypeName(document, document_type_id) {
            var document_type = this.document_types.find(element => element.id == document_type_id);
            if (document_type != null) {
                document.type_name = document_type.name;
                return document_type.name;
            }
        },
        findDocumentOriginatingOfficeName(document, originating_office) {
            var office = this.offices.find(element => element.id == originating_office);
            document.originating_office_name = '';
            if (office != null) {
                document.originating_office_name = office.name;
                return office.name;
            }
        },
        findDocumentCurrentOfficeName(document, current_office) {
            var office = this.offices.find(element => element.id == current_office);
            document.current_office_name = '';
            if (office != null) {
                document.current_office_name = office.name;
                return office.name;
            }
        },
        findDocumentSenderName(document, sender_id) {
            var sender = this.users.find(element => element.id == sender_id);
            document.sender_fullname = '';
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