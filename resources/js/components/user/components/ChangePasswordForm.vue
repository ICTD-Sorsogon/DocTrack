<template>
<v-form ref="form">
    <ValidationObserver ref="observer" v-slot="{ invalid }">
        <v-row>
            <v-col cols="12">
                <ValidationProvider rules="required|min:6" v-slot="{ errors, valid }">
                    <v-text-field
                        outlined
                        v-model="password_form.old_password"
                        label="Old Password"
                        :append-icon="show_old_password ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="show_old_password ? 'text' : 'password'"
                        @click:append="show_old_password = !show_old_password"
                        :error-messages="errors"
                        :success="valid"
                    >
                    </v-text-field>
                </ValidationProvider>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <ValidationProvider
                    name="confirm"
                    rules="required|min:6"
                    v-slot="{ errors, valid }"
                >
                    <v-text-field
                        outlined
                        v-model="password_form.new_password"
                        label="New Password"
                        :append-icon="show_new_password ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="show_new_password ? 'text' : 'password'"
                        @click:append="show_new_password = !show_new_password"
                        :error-messages="errors"
                        :success="valid"
                    >
                    </v-text-field>
                </ValidationProvider>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <ValidationProvider
                    rules="required|min:6|password:@confirm"
                    v-slot="{ errors, valid }"
                >
                    <v-text-field
                        outlined
                        v-model="password_form.confirm_password"
                        label="Confirm New Password"
                        :append-icon="show_confirm_password ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="show_confirm_password ? 'text' : 'password'"
                        @click:append="show_confirm_password = !show_confirm_password"
                        :error-messages="errors"
                        :success="valid"
                    >
                    </v-text-field>
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
                    :dark="!invalid"
                    :disabled="invalid"
                    @click="dialog = true"
                >
                    <v-icon left dark>
                        mdi-send-circle-outline
                    </v-icon>
                    Submit
                </v-btn>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog
                v-model="dialog"
                persistent
                max-width="450px"
            >
                <v-card color="grey lighten-2">
                    <v-card-title class="headline">
                        <v-icon class="mr-2" size="30px">mdi-alert-octagon</v-icon> Edit Password
                    </v-card-title>
                    <v-card-text>
                        Are you sure you want to change your password?
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary darken-1"
                            text
                            @click="dialog = false"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="primary darken-1"
                            text
                            @click.prevent="handleEditPassword"
                        >
                            Confirm
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </ValidationObserver>
</v-form>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';
export default {
    props: ['user'],
    components: {
        ValidationProvider,
        ValidationObserver
    },
    computed: mapGetters(["auth_user", "form_requests_status"]),
    data() {
        return {
            dialog: false,
            password_form: {
                form_type: 'account_password',
                old_password: '',
                new_password: '',
                confirm_password: '',
            },
            show_old_password: false,
            show_new_password: false,
            show_confirm_password: false,
        }
    },
    methods: {
        ...mapActions(["editUserCredentials"]),
        handleEditPassword() {
            const isValid = this.$refs.observer.validate();
            if(isValid) {
                this.editUserCredentials({
                    id: this.auth_user.id,
                    form: this.password_form
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
                    } else {
                        this.$store.dispatch('setSnackbar', {
                            showing: true,
                            text: this.form_requests_status.status_message,
                            color: '#D32F2F',
                            icon: 'mdi-close-thick',
                        });
                    }
                    this.dialog = false;
                });
            }
        }
    }
}
</script>