<template>
<v-card flat>
    <v-card-title primary-title>
        Add New Document
    </v-card-title>
    <v-card-text>
        <ValidationObserver ref="observer" v-slot="{ invalid }">
            <v-form ref="form" @submit.prevent="createNewDocument">
                <v-row>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-text-field
                                v-model="form.document_title"
                                label="Document Title/Subject"
                                prepend-inner-icon="mdi-format-title"
                                outlined
                                :error-messages="errors"
                                :success="valid"
                                required
                            ></v-text-field>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="8" lg="8" md="12">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-select
                                v-model="form.document_type"
                                :items="document_types"
                                item-text="name"
                                item-value="id"
                                label="Document Type"
                                prepend-inner-icon="mdi-file-document-multiple-outline"
                                :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                                outlined
                                required
                                :error-messages="errors"
                                :success="valid"
                            ></v-select>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="4" lg="4" md="12">
                        <v-radio-group
                            v-model="form.is_external"
                            row
                            :mandatory="true"
                            label="Origin: "
                            single-line
                            required
                        >
                            <v-radio
                                label="Internal"
                                value=false
                            ></v-radio>
                            <v-radio
                                label="External"
                                value=true
                            ></v-radio>
                        </v-radio-group>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-combobox
                                v-model="form.originating_office"
                                :items="offices"
                                item-text="name"
                                clearable
                                hide-selected
                                outlined
                                persistent-hint
                                label="Originating Office"
                                prepend-inner-icon="mdi-office-building-marker-outline"
                                :error-messages="errors"
                                :success="valid"
                                required
                            ></v-combobox>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-combobox
                                v-model="form.sender_name"
                                :items="all_users"
                                item-text="full_name"
                                clearable
                                hide-selected
                                outlined
                                persistent-hint
                                label="Sender Name"
                                prepend-inner-icon="mdi-account-arrow-right-outline"
                                :error-messages="errors"
                                :success="valid"
                                required
                            ></v-combobox>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="12">
                        <v-dialog
                            ref="date_dialog"
                            v-model="datepicker_modal"
                            :return-value.sync="form.date_filed"
                            persistent
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <ValidationProvider rules="required" v-slot="{ errors, valid }">
                                    <v-text-field
                                        v-model="form.date_filed"
                                        label="Date Filed"
                                        prepend-inner-icon="mdi-calendar"
                                        readonly
                                        outlined
                                        v-bind="attrs"
                                        v-on="on"
                                        :error-messages="errors"
                                        :success="valid"
                                    ></v-text-field>
                                </ValidationProvider>
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
                                <ValidationProvider rules="required" v-slot="{ errors, valid }">
                                    <v-text-field
                                        v-model="form.time_filed"
                                        label="Time Filed"
                                        prepend-inner-icon="mdi-clock-time-four-outline"
                                        readonly
                                        outlined
                                        v-bind="attrs"
                                        v-on="on"
                                        :error-messages="errors"
                                        :success="valid"
                                    ></v-text-field>
                                </ValidationProvider>
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

                    <v-col cols="12" xl="6" lg="6" md="12">
                        <ValidationProvider rules="required|numeric|min:0" v-slot="{ errors, valid }">
                            <v-text-field
                                v-model="form.page_count"
                                label="Page Count"
                                prepend-inner-icon="mdi-numeric"
                                outlined
                                :error-messages="errors"
                                :success="valid"
                                required
                                type="number"
                                min="0"
                                onkeypress="return event.charCode >= 48"
                            ></v-text-field>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="12">
                        <ValidationProvider rules="required|numeric|min:0" v-slot="{ errors, valid }">
                            <v-text-field
                                v-model="form.attachment_page_count"
                                label="Attachment Page Count"
                                prepend-inner-icon="mdi-numeric"
                                outlined
                                :error-messages="errors"
                                :success="valid"
                                required
                                type="number"
                                min="0"
                                onkeypress="return event.charCode >= 48"
                            ></v-text-field>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <v-textarea
                            clearable
                            outlined
                            auto-grow
                            clear-icon="mdi-close-circle"
                            prepend-inner-icon="mdi-comment-text-outline"
                            label="Remarks"
                            v-model="form.remarks"
                        ></v-textarea>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <div
                            class="my-2"
                            align="center"
                            justify="end"
                        >
                            <v-btn
                                color="primary"
                                :dark="!invalid"
                                :disabled="invalid"
                                :loading="loading_create_new_document"
                                @click="button_loader = 'loading_create_new_document'"
                                type="submit"
                            >
                                <v-icon left dark>
                                    mdi-plus
                                </v-icon>
                                Create
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-form>
        </ValidationObserver>
    </v-card-text>
</v-card>
</template>

<script>
// TODO: Add preloaders for the select input
import { mapGetters } from 'vuex';
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';
export default {
    components: {
        ValidationProvider,
        ValidationObserver
    },
    computed: mapGetters(['auth_user', 'document_types', 'offices', 'form_requests', 'all_users']),
    data() {
        return {
            current_date: new Date().toISOString().substr(0, 10),
            datepicker_modal: false,
            timepicker_modal: false,
            button_loader: null,
            loading_create_new_document: false,
            form: {
                form_type: 'new_document',
                tracking_id: '',
                document_title: '',
                document_type: '',
                originating_office: '',
                sender_name: '',
                page_count: '',
                attachment_page_count: '',
                is_external: false,
                date_filed: '',
                time_filed: '',
                remarks: '',
            }
        }
    },
    methods: {
        generateTrackingCode(document_data) {
            var tracking_number = '';
            var origin = 'I';
            var salt = '';
            for(var iterator = 0; iterator < 5; iterator++) {
                salt = salt + (~~(Math.random() * 10)).toString();
            }
            if(document_data.is_external) {
                origin = 'E'
            }
            var date_stripped = document_data.date_filed.split('-');
            tracking_number = tracking_number +
                origin + '-' +
                this.auth_user.office.office_code + '-' +
                date_stripped[0]+date_stripped[1]+date_stripped[2] + '-' +
                salt + '-' +
                document_data.attachment_page_count;
            return tracking_number;
        },
        sanitizeInputs() {
            this.form.is_external = this.form.is_external == 'true' ? true : false;
            this.form.tracking_id = this.generateTrackingCode(this.form);
            this.form.document_title = this.form.document_title.toString();
            if(typeof this.form.originating_office === 'object' && this.form.originating_office !== null) {
                this.form.originating_office = this.form.originating_office.id;
            } else {
                this.form.originating_office = this.form.originating_office.toString();
            }
            if(typeof this.form.sender_name === 'object' && this.form.sender_name !== null) {
                this.form.sender_name = this.form.sender_name.id;
            } else {
                this.form.sender_name = this.form.sender_name.toString();
            }
            this.form.sender_name = this.form.sender_name.toString();
            this.form.remarks = this.form.remarks != null && typeof this.form.remarks != 'undefined' ?
                this.form.remarks.toString() : null;
        },
        createNewDocument() {
            this.sanitizeInputs();
            this[this.button_loader] = !this[this.button_loader];
            this.$store.dispatch('createNewDocument', this.form).then(() => {
                if(this.form_requests.request_status == 'SUCCESS') {
                    this.$store.dispatch('setSnackbar', {
                        showing: true,
                        text: this.form_requests.status_message,
                        color: '#43A047',
                        icon: 'mdi-check-bold',
                    })
                    .then(() => {
                        this[this.button_loader] = false
                        this.button_loader = null;
                        this.$refs.form.reset();
                        this.$refs.observer.reset();
                    });
                } else {
                    this.$store.dispatch('setSnackbar', {
                        showing: true,
                        text: this.form_requests.status_message,
                        color: '#D32F2F',
                        icon: 'mdi-close-thick',
                    })
                    .then(() => {
                        this[this.button_loader] = false
                        this.button_loader = null;
                    });
                }
            });
        },
        debuggerButton() {
            // console.log(this.form);
        },
        createAndForward() {
            // TODO: Create new document then forward to office
        },
    },
    mounted() {
        this.$store.dispatch('getAllUsers');
        this.$store.dispatch('getDocumentTypes');
        this.$store.dispatch('getOffices');
        this.$store.dispatch('unsetLoader');
    }
}
</script>