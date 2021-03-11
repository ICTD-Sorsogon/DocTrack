<template>
    <v-card flat>
        <v-card-title primary-title>
            {{ $route.params.type.replace(/\w/, val => val.toUpperCase()) }}
            Document
            <v-row align="center" justify="end" class="pr-4">
                <v-btn color="primary" @click.prevent="navigateAllDocuments"
                >Back
                </v-btn
            >
            </v-row>
        </v-card-title>

        <v-card-text>
            <ValidationObserver ref="observer" v-slot="{ invalid }">
                <v-form ref="form" @submit.prevent="editDocument">
                    <v-row>
                        <v-col cols="12" xl="12" lg="12" md="12">
                            <ValidationProvider
                                rules="required"
                                v-slot="{ errors }"
                            >
                                <v-text-field
                                    v-model="form.subject"
                                    label="Document Title/Subject"
                                    prepend-inner-icon="mdi-format-title"
                                    outlined
                                    :error-messages="errors"
                                    required
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" xl="8" lg="8" md="12">
                            <ValidationProvider
                                rules="required"
                                v-slot="{ errors }"
                            >
                                <v-select
                                    v-model="form.document_type_id"
                                    :items="document_types"
                                    item-text="name"
                                    item-value="id"
                                    label="Document Type"
                                    prepend-inner-icon="mdi-file-document-multiple-outline"
                                    :menu-props="{
                                        bottom: true,
                                        offsetY: true,
                                        transition: 'slide-y-transition'
                                    }"
                                    outlined
                                    required
                                    :error-messages="errors"
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
                                <v-radio value="0">
                                    <template v-slot:label>
                                        <div>Internal</div>
                                    </template>
                                </v-radio>
                                <v-radio value="1">
                                    <template v-slot:label>
                                        <div>External</div>
                                    </template>
                                </v-radio>
                            </v-radio-group>
                        </v-col>
                        <v-col cols="12" xl="12" lg="12" md="12">
                            <ValidationProvider
                                rules="required"
                                v-slot="{ errors }"
                            >
                                <v-combobox
                                    v-model="form.destination_office_id"
                                    :items="office"
                                    item-text="name"
                                    clearable
                                    hide-selected
                                    outlined
                                    persistent-hint
                                    label="Destination Office"
                                    prepend-inner-icon="mdi-account-arrow-right-outline"
                                    :error-messages="errors"
                                    chips
                                    multiple
                                    required
                                    :search-input.sync="search"
                                    deletable-chips
                                >
                                    <template
                                        v-slot:selection="{
                                            item,
                                            select,
                                            selected
                                        }"
                                    >
                                        <v-tooltip top>
                                            <template
                                                v-slot:activator="{ on, attrs }"
                                            >
                                                <v-chip
                                                    color="primary"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    @click="select"
                                                    :input-value="selected"
                                                    close
                                                    @click:close="
                                                        removeChip(item.id)
                                                    "
                                                >
                                                    {{
                                                        item.office_code || item
                                                    }}
                                                </v-chip>
                                            </template>
                                            <span>{{ item.name || item }}</span>
                                        </v-tooltip>
                                    </template>
                                </v-combobox>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" xl="12" lg="12" md="12">
                            <ValidationProvider
                                rules="required"
                                v-slot="{ errors }"
                            >
                                <v-combobox
                                    v-model="sender_name"
                                    :items="all_users"
                                    item-text="full_name"
                                    clearable
                                    hide-selected
                                    outlined
                                    persistent-hint
                                    label="Sender Name"
                                    prepend-inner-icon="mdi-account-arrow-right-outline"
                                    :error-messages="errors"
                                    required
                                ></v-combobox>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" xl="6" lg="6" md="12">
                            <ValidationProvider
                                rules="required|numeric|min:0"
                                v-slot="{ errors }"
                            >
                                <v-text-field
                                    v-model="form.page_count"
                                    label="Page Count"
                                    prepend-inner-icon="mdi-numeric"
                                    outlined
                                    :error-messages="errors"
                                    required
                                    type="number"
                                    min="0"
                                    onkeypress="return event.charCode >= 48"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" xl="6" lg="6" md="12">
                            <ValidationProvider
                                rules="required|numeric|min:0"
                                v-slot="{ errors }"
                            >
                                <v-text-field
                                    v-model="form.attachment_page_count"
                                    label="Attachment Page Count"
                                    prepend-inner-icon="mdi-numeric"
                                    outlined
                                    :error-messages="errors"
                                    required
                                    type="number"
                                    min="0"
                                    onkeypress="return event.charCode >= 48"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" xl="12" lg="12" md="12">
                            <ValidationProvider
                                rules="required"
                                v-slot="{ errors }"
                            >
                                <v-textarea
                                    clearable
                                    outlined
                                    auto-grow
                                    clear-icon="mdi-close-circle"
                                    prepend-inner-icon="mdi-comment-text-outline"
                                    label="Remarks"
                                    v-model="form.remarks"
                                    required
                                    :error-messages="errors"
                                ></v-textarea>
                            </ValidationProvider>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <div class="my-2" align="center" justify="end">
                                <v-btn
                                    color="primary"
                                    :dark="!invalid"
                                    :loading="loading_create_new_document"
                                    @click="
                                        button_loader =
                                            'loading_create_new_document'
                                    "
                                    type="submit"
                                    :disabled="invalid"
                                >
                                    <v-icon left dark>
                                        mdi-plus
                                    </v-icon>
                                    Save
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
import { mapGetters } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from "vee-validate";
export default {
    components: {
        ValidationProvider,
        ValidationObserver
    },
    computed: {
        ...mapGetters([
            "auth_user",
            "document_types",
            "offices",
            "request",
            "all_users",
            "documents",
            "is_admin"
        ]),
        office() {
            return JSON.parse(JSON.stringify(this.offices)).filter(
                office => office.id != this.auth_user.office_id
            );
        },
        created_at() {
            return new Date(this.form.created_at).toDateString();
        },
        time_filed() {
            return new Date(this.form.created_at).toLocaleTimeString();
        },
        sender_name: {
            get() {
                return isNaN(this.form.sender_name)
                    ? this.form.sender_name
                    : this.form.sender.name;
            },
            set(val) {
                this.form.sender_name = val;
            }
        },
        selected_document() {
            return Object.values(JSON.parse(JSON.stringify(this.documents)))
                .flat()
                .find(document => {
                    return document.id == this.$route.params.id;
                });
        },
        destination() {
            return this.form.destination_office_id;
        }
    },
    data() {
        return {
            current_date: new Date().toISOString().substr(0, 10),
            datepicker_modal: false,
            timepicker_modal: false,
            button_loader: null,
            loading_create_new_document: false,
            search: null,
            form: {
                form_type: "new_document",
                subject: "",
                document_type_id: "",
                sender: {},
                destination_office_id: [],
                destination: {},
                sender_name: NaN,
                page_count: "",
                attachment_page_count: "",
                is_external: false,
                time_filed: "",
                remarks: ""
            }
        };
    },
    methods: {
        removeChip(id) {
            this.form.destination_office_id = this.form.destination_office_id.filter(
                data => {
                    return data.id != id;
                }
            );
        },
        navigateAllDocuments() {
            if (this.$route.name !== "All Active Documents") {
                this.$store.dispatch("setLoader");
                this.$router.push({ name: "All Active Documents" });
            }
        },
        sanitizeInputs() {
            let dataPayload = JSON.parse(JSON.stringify(this.form));
            dataPayload.destination_office_id = dataPayload.destination_office_id.reduce(
                (ids, item) => {
                    item.id && ids.push(item.id);
                    return ids;
                },
                []
            );
            dataPayload.sender_name =
                dataPayload.sender_name.id ?? dataPayload.sender_name;
            return dataPayload;
        },
        editDocument() {
            this.createNewDocument();
        },
        createNewDocument() {
            let body = this.sanitizeInputs();
            this[this.button_loader] = !this[this.button_loader];
            this.$store.dispatch("createNewDocument", body).then(() => {
                if (this.request.status == "success") {
                    this.$store
                        .dispatch("setSnackbar", {
                            type: "success",
                            message: this.request.message
                        })
                        .then(() => {
                            this[this.button_loader] = false;
                            this.button_loader = null;
                            if (this.$route.params.type == "Create") {
                                this.$refs.form.reset();
                                this.$refs.observer.reset();
                            }
                            this.$store.dispatch("officeReports");
                            this.$store.dispatch("getActiveDocuments");
                            this.$router.push({ name: "All Active Documents" });
                        });
                } else if (this.request.status == "failed") {
                    this.$store
                        .dispatch("setSnackbar", {
                            type: "error",
                            message: this.request.message
                        })
                        .then(() => {
                            this[this.button_loader] = false;
                            this.button_loader = null;
                        });
                }
            });
        },
        fillForm() {
            this.form =
                this.$route.params.type != "create"
                    ? this.$route.params.item ?? this.selected_document
                    : this.form;
        }
    },
    watch: {
        destination(value) {
            if (
                value?.length > 0 &&
                typeof value[value.length - 1] != "object"
            ) {
                this.$nextTick(() => this.destination.pop());
            }
            this.search = null;
        }
    },
    mounted() {
        this.fillForm();
        this.auth_user.role_id === 1 && this.$store.dispatch("getAllUsers");
        this.$store.dispatch("unsetLoader");
    }
};
</script>
