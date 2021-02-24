<template>
    <!-- TODO: CONTINUE THIS WITH AUTHORIZING PERSON TABLE -->
    <v-card flat>
        <v-card-title primary-title>
            {{ $route.params.type.replace(/\w/, val=>val.toUpperCase()) }} Document : {{ selected_document.tracking_code }}
            <v-row align="center" justify="end" class="pr-4">
            <v-btn color="primary" @click.prevent="navigateAllDocuments"
            >Back</v-btn
            >
            </v-row>
        </v-card-title>
        <v-card-text>
            <ValidationObserver ref="observer" v-slot="{ valid , invalid }">
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
                            v-model="selected_document.is_external"
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
                                v-model="selected_document.originating_office"
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
                            v-model="selected_document.sender_name"
                            label="Sender Name"
                            prepend-inner-icon="mdi-account-arrow-right-outline"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" :xl="types=='forward'? 4 : 6" :lg="types=='forward'? 4 : 6" :md="types=='forward'? 4 : 6">
                        <v-text-field
                            v-model="selected_document.page_count"
                            label="Page Count"
                            prepend-inner-icon="mdi-numeric"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" :xl="types=='forward'? 4 : 6" :lg="types=='forward'? 4 : 6" :md="types=='forward'? 4 : 6">
                        <v-text-field
                            v-model="selected_document.attachment_page_count"
                            label="Attachment Page Count"
                            prepend-inner-icon="mdi-numeric"
                            outlined
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="4" lg="4" md="4" v-if="types=='forward'">
                        <v-select
                            v-model="form.forwarded_by"
                            :items="offices"
                            item-text="name"
                            item-value="id"
                            label="Forwarded by"
                            outlined
                            prepend-inner-icon="mdi-office-building-outline"
                            :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                            disabled
                        ></v-select>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12" v-if="types=='forward'">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-select
                            v-model="form.forwarded_to"
                            :items="offices"
                            item-text="name"
                            item-value="id"
                            label="Forwarded to"
                            outlined
                            prepend-inner-icon="mdi-office-building-outline"
                            :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                            required
                            :error-messages="errors"
                            :success="valid"
                        ></v-select>
                         </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="12" lg="6" md="12" v-if="['forward', 'receive'].includes(types)">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-select
                            v-model="form.through"
                            :items="coming_from"
                            item-text="show"
                            item-value="value"
                            label="Through"
                            prepend-inner-icon="mdi-transit-connection-horizontal"
                            :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                            required
                            outlined
                            :error-messages="errors"
                            :success="valid"
                        ></v-select>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="12" :lg="types=='terminal'? 12 : 6" md="12" v-if="['forward', 'receive', 'terminal'].includes(types)">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-text-field
                            v-model="form.approved_by"
                            label="Approved by"
                            prepend-inner-icon="mdi-account-tie-outline"
                            outlined
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12" v-if="types=='acknowledge' || ('hold' || 'release' && isAdmin)">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-select
                            v-model="form.priority_levels"
                            :items="priority_level"
                            item-text="show"
                            item-value="value"
                            label="Priority Level"
                            prepend-inner-icon="mdi-priority-high"
                            :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                            required
                            outlined
                            :error-messages="errors"
                            :success="valid"
                        ></v-select>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6" v-if="types=='Change Date'">
                        <v-dialog
                            ref="date_dialog"
                            v-model="datepicker_modal"
                            :return-value.sync="form.date_filed"
                            persistent
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="form.date_filed"
                                    label="Date Filed"
                                    prepend-inner-icon="mdi-calendar"
                                    readonly
                                    outlined
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="form.date_filed" scrollable>
                                <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="datepicker_modal = false">
                                    Cancel
                                </v-btn>
                                <v-btn text color="primary" @click="$refs.date_dialog.save(form.date_filed)">
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-dialog>
                    </v-col>
                     <v-col cols="12" xl="6" lg="6" md="12" v-if="types=='Change Date'">
                        <v-dialog
                            ref="time_dialog"
                            v-model="timepicker_modal"
                            :return-value.sync="form.time_filed"
                            persistent
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="form.time_filed"
                                    label="Time Filed"
                                    prepend-inner-icon="mdi-clock-time-four-outline"
                                    readonly
                                    outlined
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-time-picker v-if="timepicker_modal" v-model="form.time_filed" full-width>
                                <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="timepicker_modal = false">
                                    Cancel
                                </v-btn>
                                <v-btn text color="primary" @click="$refs.time_dialog.save(form.time_filed)">
                                    OK
                                </v-btn>
                            </v-time-picker>
                        </v-dialog>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-textarea
                            outlined
                            auto-grow
                            clear-icon="mdi-close-circle"
                            prepend-inner-icon="mdi-comment-text-outline"
                            label="Remarks*"
                            v-model="form.documentRemarks"
                            required
                            :error-messages="errors"
                            :success="valid"
                        ></v-textarea>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="12" lg="12" md="12">
                        <div class="my-2" align="center" justify="end">
                            <v-btn
                                v-if="types=='receive'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="!invalid"
                                :disabled="invalid"
                                >
                                    <v-icon left dark>
                                        mdi-email-receive-outline
                                    </v-icon>
                                    Receive
                                </v-btn>
                            <v-btn
                                v-if="types=='forward'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="valid"
                                :disabled="!valid"
                                >
                                    <v-icon left dark>
                                        mdi-email-send-outline
                                    </v-icon>
                                    Forward
                            </v-btn>
                             <v-btn
                                v-if="types=='terminal'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="!invalid"
                                :disabled="invalid"
                                >
                                    <v-icon left dark>
                                        mdi-email-off-outline
                                    </v-icon>
                                    Terminate
                            </v-btn>
                            <v-btn
                                v-if="types=='acknowledge'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="!invalid"
                                :disabled="invalid"
                                >
                                    <v-icon left dark>
                                        mdi-email-check-outline
                                    </v-icon>
                                    Acknowledge
                            </v-btn>
                            <v-btn
                                v-if="types=='Hold'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="!invalid"
                                :disabled="invalid"
                                >
                                    <v-icon left dark>
                                        mdi-email-alert-outline
                                    </v-icon>
                                    Hold
                            </v-btn>
                            <v-btn
                                v-if="types=='Change Date'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="valid"
                                :disabled="!valid"
                                >
                                    <v-icon left dark>
                                        mdi-calendar-check-outline
                                    </v-icon>
                                    Save
                            </v-btn>
                            <v-btn
                                v-if="types=='Release'"
                                color="primary"
                                :loading="btnloading"
                                @click.prevent="showDocumentDialog"
                                type="submit"
                                :dark="valid"
                                :disabled="!valid"
                                >
                                    <v-icon left dark>
                                        mdi-email-mark-as-unread
                                    </v-icon>
                                    Release
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-form>
            </ValidationObserver>
        </v-card-text>

        <v-row justify="center">
            <v-dialog v-model="documentDialog" persistent max-width="450px">
                <v-card color="grey lighten-2">
                    <v-card-title class="headline">
                        <v-icon class="mr-2" size="30px">mdi-information</v-icon> {{ $route.params.type.replace(/\w/, val=>val.toUpperCase()) }} Document
                    </v-card-title>
                    <v-card-text>
                        Are you sure you want to {{ $route.params.type.replace(/\w/, val=>val) }} this document? <br> <strong>- {{ selected_document.subject }}</strong>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" text @click="documentDialog = false">
                            Cancel
                        </v-btn>
                        <v-btn v-if="types=='receive'" color="primary darken-1" text @click.prevent="receiveDocumentConfirm">
                            Receive
                        </v-btn>
                        <v-btn v-if="types=='forward'" color="primary darken-1" text @click.prevent="forwardDocumentConfirm">
                            Forward
                        </v-btn>
                        <v-btn v-if="types=='terminal'" color="primary darken-1" text @click.prevent="terminateDocumentConfirm">
                            Terminate
                        </v-btn>
                        <v-btn v-if="types=='acknowledge'" color="primary darken-1" text @click.prevent="acknowledgeDocumentConfirm">
                            Acknowledge
                        </v-btn>
                        <v-btn v-if="types=='Hold'" color="primary darken-1" text @click.prevent="holdDocumentConfirm">
                            Hold
                        </v-btn>
                        <v-btn v-if="types=='Change Date'" color="primary darken-1" text @click.prevent="changeDateDocumentConfirm">
                            Save
                        </v-btn>
                        <v-btn v-if="types=='Release'" color="primary darken-1" text @click.prevent="releaseDocumentConfirm">
                            Release
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>

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
        ...mapGetters(['find_document' , 'documents' , 'find_document', 'is_admin', 'auth_user']),
        types() {
            return this.$route.params.type;
        },
        selected_document() {

            let userDoc = !this.is_admin && ['terminate', 'Change Date'].includes(this.$route.params.type) ? this.documents['outgoing'] : this.documents['incoming']

            return JSON.parse(JSON.stringify( this.is_admin ? this.documents : userDoc)).map(doc=>{
				doc.is_external = doc.is_external ? 'External' : 'Internal'
				doc.sender_name = doc.sender?.name ?? doc.sender_name
				doc.originating_office = doc.origin_office?.name ?? doc.originating_office
				return doc
			}).find(doc => doc.id == this.$route.params.id)
        },
        request(){
            return this.$store.state.snackbars.request;
        },
        offices() {
            return this.$store.state.offices.offices;
        },
        users() {
            return this.$store.state.users.all_users;
        },
        isAdmin() {
			return this.auth_user.role_id == 1
		},
    },
    watch: {
        documentDialog (val) {
            val || this.closeDocumentDialog()
        },
    },
    data() {
        return {
            btnloading: false,
            documentDialog: false,
            datepicker_modal: false,
            timepicker_modal: false,
            sent: false,
            coming_from: [
                { show: 'Docket Office', value: 'docket office' },
                { show: 'Email', value: 'email' },
                { show: 'Personal', value: 'personal' },
                { show: 'Others', value: 'others' }
            ],
            priority_level: [
                { show: 'High', value: '1' },
                { show: 'Medium', value: '2' },
                { show: 'Low', value: '3' },
                { show: 'Indefinite', value: '4' }
            ],
            form: {
                document_id: '',
                action: '',
                touched_by: '',
                priority_levels: '',
                last_touched: '',
                through: '',
                approved_by: '',
                documentRemarks: '',
                forwarded_by: this.auth_user?.office_id || '',
                forwarded_to: '',
                status: '',
                date_filed: '',
                time_filed: '',
            },
            defaultItem: {
                document_id: '',
                action: '',
                touched_by: '',
                priority_levels: '',
                last_touched: '',
                through: '',
                approved_by: '',
                documentRemarks: '',
                forwarded_by: '',
                forwarded_to: '',
                status: '',
                date_filed: '',
                time_filed: '',
            }
        }
    },
    methods: {
        navigateAllDocuments() {
            if(this.$route.name !== 'All Active Documents') {
                this.$store.dispatch('setLoader');
                this.$router.push({ name: "All Active Documents"});
            }
        },
        showDocumentDialog() {
            this.documentDialog = true
        },
        closeDocumentDialog () {
            this.documentDialog = false
        },
        sanitize() {
            this.form.destination = this.form.destination[0].id;
            this.form.recipient_id = this.is_admin ? null : this.form.recipient[0].recipient_id;
            this.form.forwarded_by = this.form.forwarded_by.id;
        },
        receiveDocumentConfirm() {
            this.btnloading = true;
            !this.sent && this.sanitize();
                this.$store.dispatch("receiveDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.sent = true
                this.closeDocumentDialog()
        },
        forwardDocumentConfirm() {
            this.btnloading = true;
            !this.sent && this.sanitize();
                this.$store.dispatch("forwardDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.sent = true
                this.closeDocumentDialog()
        },
        terminateDocumentConfirm() {
            this.btnloading = true;
                this.$store.dispatch("terminateDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.closeDocumentDialog()
        },
        acknowledgeDocumentConfirm() {
            this.btnloading = true;
                this.$store.dispatch("acknowledgeDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.closeDocumentDialog()
        },
        holdDocumentConfirm() {
            this.btnloading = true;
                this.$store.dispatch("holdDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.closeDocumentDialog()
        },
        changeDateDocumentConfirm() {
            this.btnloading = true;
                this.$store.dispatch("changeDateDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.closeDocumentDialog()
        },
        releaseDocumentConfirm() {
            this.btnloading = true;
                this.$store.dispatch("releaseDocumentConfirm", this.form).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getActiveDocuments');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
                this.closeDocumentDialog()
        },
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.form = this.$route.params.item ?? this.selected_document;
        this.form.forwarded_by = this.auth_user.office;
    }

}
</script>