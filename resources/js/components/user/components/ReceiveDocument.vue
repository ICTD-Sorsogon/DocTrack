<template>
    <!-- TODO: CONTINUE THIS WITH AUTHORIZING PERSON TABLE -->
    <v-card flat>
        <v-card-title primary-title v-if="types=='receive'">
            Receive Document : {{ selected_document.tracking_code }}
            <v-row align="center" justify="end" class="pr-4">
            <v-btn color="primary" @click.prevent="navigateAllDocuments"
            >Back</v-btn
            >
            </v-row>
        </v-card-title>
        <v-card-title primary-title v-if="types=='forward'">
            Forward Document : {{ selected_document.tracking_code }}
            <v-row align="center" justify="end" class="pr-4">
            <v-btn color="primary" @click.prevent="navigateAllDocuments"
            >Back</v-btn
            >
            </v-row>
        </v-card-title>
        <v-card-title primary-title v-if="types=='terminal'">
            Terminate Document : {{ selected_document.tracking_code }}
            <v-row align="center" justify="end" class="pr-4">
            <v-btn color="primary" @click.prevent="navigateAllDocuments"
            >Back</v-btn
            >
            </v-row>
        </v-card-title>
        <v-card-text>
            <v-form ref="form">
                <v-row>
                    <v-col cols="12" xl="12" lg="8" md="12">
                        <v-text-field
                            v-model="selected_document.subject"
                            label="Document Title/Subject"
                            prepend-inner-icon="mdi-format-title"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="12" lg="4" md="12">
                         <v-radio-group
                            v-model="form.is_external"
                            row
                            :mandatory="true"
                            label="Origin: "
                            single-line
                            disabled
                        >
                        <v-radio value="Internal">
                            <template v-slot:label>
                            <div>Internal</div>
                            </template>
                        </v-radio>
                        <v-radio value="External">
                            <template v-slot:label>
                            <div>External </div>
                            </template>
                        </v-radio>
                        </v-radio-group>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" xl="6" lg="6" md="6">
                            <v-combobox
                                v-model="selected_document.origin_office.name"
                                item-text="name"
                                hide-selected
                                outlined
                                disabled
                                label="Originating Office"
                                prepend-inner-icon="mdi-office-building-marker-outline"
                            >{{ selected_document.originating_office }}</v-combobox>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <v-text-field
                            v-model="selected_document.sender.name"
                            label="Sender Name"
                            prepend-inner-icon="mdi-account-arrow-right-outline"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <v-text-field
                            v-model="selected_document.page_count"
                            label="Page Count"
                            prepend-inner-icon="mdi-numeric"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <v-text-field
                            v-model="selected_document.attachment_page_count"
                            label="Attachment Page Count"
                            prepend-inner-icon="mdi-numeric"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <v-dialog
                            ref="date_dialog"
                            v-model="datepicker_modal"
                            :return-value.sync="form.date_filed"
                            persistent
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="selected_document.date_filed"
                                    label="Date Filed"
                                    prepend-inner-icon="mdi-calendar"
                                    readonly
                                    outlined
                                    disabled
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="form.date_filed"
                                scrollable
                            >
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="datepicker_modal = false"
                                >
                                    Cancel
                                </v-btn>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="$refs.date_dialog.save(form.date_filed)"
                                >
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-dialog>
                    </v-col>
                     <v-col cols="12" xl="6" lg="6" md="12">
                        <v-dialog
                            ref="time_dialog"
                            v-model="timepicker_modal"
                            :return-value.sync="form.time_filed"
                            persistent
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="selected_document.time_filed"
                                    label="Time Filed"
                                    prepend-inner-icon="mdi-clock-time-four-outline"
                                    readonly
                                    outlined
                                    disabled
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-time-picker
                                v-if="timepicker_modal"
                                v-model="form.time_filed"
                                full-width
                            >
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="timepicker_modal = false"
                                >
                                    Cancel
                                </v-btn>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="$refs.time_dialog.save(form.time_filed)"
                                >
                                    OK
                                </v-btn>
                            </v-time-picker>
                        </v-dialog>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <v-combobox
                            v-if="types=='forward'"
                            v-model="sampleItems[1]"
                            item-text="name"
                            hide-selected
                            outlined
                            persistent-hint
                            label="To"
                            prepend-inner-icon="mdi-office-building-marker-outline"
                            required
                        ></v-combobox>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <v-combobox
                            v-if="['forward', 'receive'].includes(types)"
                            v-model="sampleItems[0]"
                            item-text="name"
                            hide-selected
                            outlined
                            persistent-hint
                            label="Through"
                            prepend-inner-icon="mdi-office-building-marker-outline"
                            required
                        ></v-combobox>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <v-textarea
                            outlined
                            auto-grow
                            clear-icon="mdi-close-circle"
                            prepend-inner-icon="mdi-comment-text-outline"
                            label="Remarks"
                            v-model="form.remarks"
                        ></v-textarea>
                    </v-col>
                    <v-col>
                        <div
                            class="my-2"
                            align="center"
                            justify="end"
                        >
                            <v-btn
                                    v-if="types=='receive'"
                                    color="primary"
                                    :loading="loading_create_new_document"
                                    @click="button_loader = 'loading_create_new_document'"
                                    type="submit"
                                >
                                    <v-icon left dark>
                                        mdi-email-receive-outline
                                    </v-icon>
                                    Receive
                                </v-btn>
                            <v-btn
                                v-if="types=='forward'"
                                color="primary"
                                :loading="loading_create_new_document"
                                @click="button_loader = 'loading_create_new_document'"
                                type="submit"
                            >
                                <v-icon left dark>
                                    mdi-email-send-outline
                                </v-icon>
                                Forward
                            </v-btn>
                             <v-btn
                                v-if="types=='terminal'"
                                color="primary"
                                :loading="loading_create_new_document"
                                @click="button_loader = 'loading_create_new_document'"
                                type="submit"
                            >
                                <v-icon left dark>
                                    mdi-email-off-outline
                                </v-icon>
                                Terminate
                            </v-btn>
                        </div>
                    </v-col>

                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';
export default {
    components: {
        ValidationProvider,
        ValidationObserver
    },
    computed: {
        ...mapGetters(['selected_document']),
        types(){
            return this.$store.state.documents.types;
        }
    },
    data() {
        return {
            button_loader: null,
            loading_create_new_document: false,
            datepicker_modal: false,
            timepicker_modal: false,
            status: ['Sample', 'Sample2'],
            action: ['Sample3', 'Sample4'],
            temp: {
                sample_data: 'sample'
            },
            form: {
                subject: '',
                is_external: false,
                originating_office: '',
                sender: {},
                page_count: '',
                attachment_page_count: '',
                date_filed: '',
                time_filed: '',
                remarks: ''
            },
            sampleItems: ['Docketing Office', 'Forward to Office']
        }
    },
    methods: {
        navigateAllDocuments() {
            if(this.$route.name !== 'All Active Documents') {
                this.$store.dispatch('setLoader');
                this.$router.push({ name: "All Active Documents"});
            }
        },
    },
    mounted() {
        console.log(this.selected_document);

    }
}
</script>