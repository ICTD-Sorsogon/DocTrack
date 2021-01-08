<template>
<v-card flat>
    <!-- TODO: Dynamic Title -->
    <v-card-title primary-title>
        {Action} Document
    </v-card-title>
    <!-- FIXME: Breadcrumb link not working properly -->
    <!-- TODO: Dynamic Breadcrumbs -->
    <v-card-subtitle>
        <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:divider>
            <v-icon>mdi-arrow-right-thick</v-icon>
        </template>
        </v-breadcrumbs>
    </v-card-subtitle>
    <v-card-text>
        <v-card class="mx-auto">
            <v-list-item two-line>
                <v-list-item-content>
                    <v-row>
                        <v-col cols=8  class="d-flex flex-row">
                            <v-list-item-title
                                class="headline"
                            >
                                {{document_record.title}}
                            </v-list-item-title>
                        </v-col>
                        <v-col cols=4
                            align="end"
                            justify="center"
                        >
                            <!-- TODO: Dynamic Action button -->
                            <v-btn
                                tile
                                color="primary"
                            >
                            <v-icon left>
                                mdi-email-open-outline
                            </v-icon>
                                Receive
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <v-row>
                    <v-col>
                        <v-simple-table fixed-header>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            Attribute
                                        </th>
                                        <th class="text-center">
                                            Value
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="subtitle-2"><strong>Tracking Number</strong></td>
                                        <td class="subtitle-1">{{document_record.tracking_code}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Source Type</strong></td>
                                        <td class="subtitle-1">{{document_record.is_external ? "External" : "Internal"}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Document Type</strong></td>
                                        <td class="subtitle-1">{{document_record.document_type_id}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Originating Office</strong></td>
                                        <td class="subtitle-1">{{document_record.originating_office}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Sender Name</strong></td>
                                        <td class="subtitle-1">{{document_record.sender_name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Current Office</strong></td>
                                        <td class="subtitle-1">{{document_record.current_office}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-1"><strong>Date Filed</strong></td>
                                        <td class="subtitle-1">{{document_record.date_filed}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Page Count</strong></td>
                                        <td class="subtitle-1">{{document_record.page_count}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Attacment Page Count</strong></td>
                                        <td class="subtitle-1">{{document_record.attachment_page_count}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Status</strong></td>
                                        <td class="subtitle-1">{{document_record.is_terminal ? "Terminal" : "Active"}}</td>
                                    </tr>
                                    <tr>
                                        <td class="subtitle-2"><strong>Remarks</strong></td>
                                        <td class="subtitle-1">{{document_record.remarks}}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-col>
                </v-row>
            </v-list-item>

        </v-card>
    </v-card-text>
</v-card>
</template>

<script>
// TODO: Backend functionality
export default {
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Document Records',
                    disabled: false,
                    to: 'document_records',
                },
                {
                    text: 'Receive Document',
                    disabled: true,
                    to: 'receive_document',
                },
            ],
            document_record: {
                tracking_code: '2012-21323-1234454',
                title: 'Some Random Document',
                document_type_id: '1',
                originating_office: 'Governor\'s Office',
                is_external: false,
                current_office: null,
                sender_name: 'FirstName MiddleName LastName',
                page_count: 10,
                date_filed: new Date().toISOString().substr(0, 10),
                is_terminal: false,
                remarks: 'No attachment',
                attachment_page_count: 0,
            }
        }
    }
}
</script>