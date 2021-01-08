<template>
<div>
    <v-row v-if="user && home">
        <v-col cols="12" xl="6" lg="6" md="6">
            <v-card
                class="mx-auto document_record_card"
                color="#E1EBEE"
            >
                <v-card-title>
                    <span class="title font-weight-bold">New Document</span>
                </v-card-title>

                <v-divider class="mx-4"></v-divider>

                <v-card-text class="font-weight-light document_record_card_text">
                    <span
                        class="subtitle font-weight-bold"
                    >
                        <!-- TODO: Better presentation and wording of this description -->
                        Create a fresh document that will be added to the inter-office document list.
                        Only create a new document if it does not exist in your office's document database or
                        if your office is the original source of the document.
                        If the document is from an outside office, you can add the document in the record by using this
                        feature to include it in the list of documents that your office is working on.
                    </span>
                </v-card-text>
                <v-card-actions
                    align="right"
                    justify="end"
                >
                    <v-list-item class="grow">
                        <v-row no-gutters>
                            <v-col cols="12">
                                <v-btn
                                    class="ma-2"
                                    fab
                                    color="#0D47A1"
                                    dark
                                    link
                                    @click.prevent="getNewDocumentPage"
                                >
                                    <v-icon>mdi-file-document-multiple-outline</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-list-item>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6">
            <v-card
                class="mx-auto"
                color="#E1EBEE"
            >
                <v-card-title>
                    <span class="title font-weight-bold">Receive Document</span>
                </v-card-title>
                <v-divider class="mx-4"></v-divider>
                <v-card-text class="font-weight-light document_record_card_text">
                    <span class="subtitle font-weight-bold">
                        Receive an existing document routed to your office by scanning or entering the document
                        routing number
                    </span>
                </v-card-text>

                <v-card-actions
                    align="right"
                    justify="end"
                >
                    <v-list-item class="grow">
                        <v-row no-gutters>
                            <v-col cols="9">
                                <v-form ref="form">
                                    <v-text-field
                                        v-model="receive_routing_number"
                                        label="Routing Number"
                                        clearable
                                    ></v-text-field>
                                </v-form>
                            </v-col>
                            <v-col cols="3">
                                <v-btn
                                    class="ma-2"
                                    fab
                                    color="#0D47A1"
                                    dark
                                    link
                                    @click.prevent="getReceiveDocumentPage"
                                >
                                    <v-icon>mdi-email-open-outline</v-icon>
                                </v-btn>
                            </v-col>

                        </v-row>
                    </v-list-item>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6">
            <v-card
                class="mx-auto"
                color="#E1EBEE"
                max-width="100%"
            >
                <v-card-title>
                    <span class="title font-weight-bold">Forward Document</span>
                </v-card-title>
                <v-divider class="mx-4"></v-divider>
                <v-card-text class="font-weight-light document_record_card_text">
                    <span class="subtitle font-weight-bold">Forward an existing document to another office</span>
                </v-card-text>
                <v-card-actions
                    align="right"
                    justify="end"
                >
                    <v-list-item class="grow">
                        <v-row no-gutters>
                            <v-col cols="9">
                                <v-form ref="form">
                                    <v-text-field
                                        v-model="forward_routing_number"
                                        label="Routing Number"
                                        clearable
                                    ></v-text-field>
                                </v-form>
                            </v-col>
                            <v-col cols="3">
                                <v-btn
                                    class="ma-2"
                                    fab
                                    color="#0D47A1"
                                    dark
                                    link
                                    @click.prevent="getForwardDocumentPage"
                                >
                                    <v-icon>mdi-email-send-outline</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-list-item>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6">
            <v-card
                class="mx-auto"
                color="#E1EBEE"
                max-width="100%"
            >
                <v-card-title>
                    <span class="title font-weight-bold">Set Document to Terminal</span>
                </v-card-title>
                <v-divider class="mx-4"></v-divider>
                <v-card-text class="font-weight-light document_record_card_text">
                    <span class="subtitle font-weight-bold">Set an existing document as terminal</span>
                </v-card-text>

                <v-card-actions
                    align="right"
                    justify="end"
                >
                    <v-list-item class="grow">
                        <v-row no-gutters>
                            <v-col cols="9">
                                <v-form ref="form">
                                    <v-text-field
                                        v-model="terminal_routing_number"
                                        label="Routing Number"
                                        clearable
                                    ></v-text-field>
                                </v-form>
                            </v-col>
                            <v-col cols="3">
                                <v-btn
                                    class="ma-2"
                                    fab
                                    color="#0D47A1"
                                    dark
                                    link
                                    @click.prevent="getSetTerminalDocumentPage"
                                >
                                    <v-icon>mdi-close-outline</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-list-item>
                </v-card-actions>
            </v-card>
        </v-col>
    </v-row>
    <new-document
        @new-document-close="showDocumentRecordHome"
        v-if="home==false"
    ></new-document>
</div>
</template>

<script>
import NewDocument from './components/NewDocument';
export default {
    props: ['user'],
    components: {
        NewDocument
    },
    data() {
        return {
            home: true,
            receive_routing_number: '',
            forward_routing_number: '',
            terminal_routing_number: '',
        }
    },
    methods: {
        showDocumentRecordHome() {

        },
        getNewDocumentPage() {
            axios.get('new_document').then(() => {
                this.$router.push({ name: "New Document"})
            })
        },
        getReceiveDocumentPage() {
            if(this.routing_number){
                axios.get('receive_document').then(() => {
                    this.$router.push({
                        name: "Receive Document",
                        params: { routing_number: this.receive_routing_number }
                    })
                })
            }
        },
        getForwardDocumentPage() {
            if(this.routing_number){
                axios.get('forward_document').then(() => {
                    this.$router.push({
                        name: "Forward Document",
                        params: { routing_number: this.forward_routing_number }
                    })
                })
            }
        },
        getSetTerminalDocumentPage() {
            if(this.routing_number){
                axios.get('terminal_document').then(() => {
                    this.$router.push({
                        name: "Terminal Document",
                        params: { routing_number: this.terminal_routing_number }
                    })
                })
            }
        }
    }
}
</script>

<style lang="css">
    .document_record_card {
        min-height:150px;
    }
    .document_record_card_text {
        min-height:150px;
    }
</style>