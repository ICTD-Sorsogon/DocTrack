<template>
    <v-dialog
      v-model="dialog"
      persistent
      scrollable
      max-width="1000px"
    >
    <v-card>
        <v-card-title primary-title>
            Document Details
        </v-card-title>
        <v-card-text>
            <v-row>
                <v-col>
                    <v-list flat subheader>
                        <v-subheader>Tracking ID</v-subheader>
                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>mdi-square-medium</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-chip label dark :color="selected_document.color" id="document_label">
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
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col cols="12" xl="4" lg="4" md="4" sm="12">
                    <v-list flat subheader>
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
                    <v-list flat subheader>
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
                    <v-list flat subheader>
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
            <v-divider inset />
            <v-row>
                <v-col>
                    <v-list flat subheader>
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
            <v-btn color="primary" text @click="$emit('close-dialog')">
                Close
            </v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>
</template>

<script>
    export default {
		props: ['selected_document', 'dialog'],
    }

</script>

<style>

</style>