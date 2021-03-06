<template>
    <v-form ref="form">
        <ValidationObserver ref="observer" v-slot="{ invalid }">
            <v-row>
                <v-col cols="12">
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        vid="username"
                        name="New Username"
                    >
                        <v-text-field
                            outlined
                            v-model="username_form.new_username"
                            label="New Username"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <ValidationProvider
                        rules="required|confirmed:username"
                        v-slot="{ errors, valid }"
                        name="Confirm New Username"
                    >
                        <v-text-field
                            outlined
                            v-model="username_form.new_username_confirmation"
                            label="Confirm New Username"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
            </v-row>
            <v-row>
                <v-col align="center" justify="end">
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
                <v-dialog v-model="dialog" persistent max-width="450px">
                    <v-card color="grey lighten-2">
                        <v-card-title class="headline grey lighten-2">
                            <v-icon class="mr-2" size="30px"
                                >mdi-alert-octagon</v-icon
                            >
                            Edit Username
                        </v-card-title>
                        <v-card-text>
                            <v-spacer></v-spacer>
                            Are you sure you want to change your account
                            username?
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
                                @click.prevent="editUsernameHandler"
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
import { mapGetters } from "vuex";
import { ValidationObserver, ValidationProvider } from "vee-validate";
export default {
    components: {
        ValidationProvider,
        ValidationObserver
    },
    computed: mapGetters(["auth_user", "request"]),
    data() {
        return {
            dialog: false,
            username_form: {
                form_type: "account_username",
                new_username: "",
                new_username_confirmation: ""
            },
            loader: null,
            loading_edit_username: false
        };
    },
    methods: {
        editUsernameHandler() {
            const isValid = this.$refs.observer.validate();
            if (isValid) {
                this.$store
                    .dispatch("updateUsername", this.username_form)
                    .then(() => {
                        this.$store.dispatch("setSnackbar", this.request);
                        if (this.request.type != "error") {
                            this.$refs.form.reset();
                            this.$refs.observer.reset();
                        }
                        this.dialog = false;
                    });
            }
        }
    }
};
</script>
