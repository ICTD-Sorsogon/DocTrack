<template>
    <v-form
        ref="form"
        @submit.prevent="updateAccountDetails"
    >
        <ValidationObserver ref="observer" v-slot="{ invalid }">
            <v-row>
                <v-col cols="12" xl="6" lg="6" md="12" sm="12">
                    <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-text-field
                            outlined
                            v-model="name_form.first_name"
                            label="First Name"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
                <v-col cols="12" xl="6" lg="6" md="12" sm="12">
                    <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-text-field
                            outlined
                            v-model="name_form.middle_name"
                            label="Middle Name"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" xl="9" lg="9" md="9" sm="12">
                    <ValidationProvider rules="required" v-slot="{ errors, valid }">
                        <v-text-field
                            outlined
                            v-model="name_form.last_name"
                            label="Last Name"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
                <v-col cols="12" xl="3" lg="3" md="3" sm="12">
                    <ValidationProvider rules="alpha_spaces" v-slot="{ errors }">
                        <v-text-field
                            id="suffix_field"
                            outlined
                            v-model="name_form.name_suffix"
                            label="Suffix (Optional)"
                            :error-messages="errors"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
            </v-row>
            <v-row>
                <v-col
                    align="center"
                    justify="end"
                >
                    <v-btn
                        color="primary"
                        type="submit"
                        :dark="!invalid"
                        :disabled="invalid"
                        :loading="loading_edit_details"
                        @click="loader = 'loading_edit_details'"
                    >
                        <v-icon left dark>
                            mdi-send-circle-outline
                        </v-icon>
                        Submit
                    </v-btn>
                </v-col>
            </v-row>
        </ValidationObserver>
    </v-form>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';
export default {
    computed: mapGetters(["auth_user", "form_requests_status"]),
    components: {
        ValidationProvider,
        ValidationObserver
    },
    data() {
        return {
            name_form: {
                form_type: 'account_details',
                first_name: '',
                middle_name: '',
                last_name: '',
                name_suffix: '' ,
            },
            loader: null,
            loading_edit_details: false,
        }
    },
    methods: {
        ...mapActions(["editUserCredentials"]),
        updateAccountDetails() {
            this[this.loader] = !this[this.loader];
            const isValid = this.$refs.observer.validate();
            if(isValid) {
                this.editUserCredentials({
                    id: this.auth_user.id,
                    form: this.name_form
                }).then(() => {
                    if(this.form_requests_status.request_status == "SUCCESS") {
                        this.$store.dispatch('setSnackbar', {
                            showing: true,
                            text: this.form_requests_status.status_message,
                            color: '#43A047',
                            icon: 'mdi-check-bold',
                        });
                        this.$refs.form.reset();
                        this.$refs.observer.reset();
                    }else {
                        this.$store.dispatch('setSnackbar', {
                            showing: true,
                            text: this.form_requests_status.status_message,
                            color: '#D32F2F',
                            icon: 'mdi-close-thick',
                        });
                    }
                    this[this.loader] = false
                    this.loader = null;
                });
            }
        }
    }
}
</script>