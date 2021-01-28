<template>
    <v-card class="mx-auto" flat>
        <v-card-title>
            <v-icon
            left
            >
                mdi-account-edit
            </v-icon>
            <span class="title">Edit Account Info</span>
        </v-card-title>

        <v-card-text class="headline font-weight-bold">
            <v-expansion-panels
                focusable
                flat
                v-model="panel"
                multiple
            >
                <v-expansion-panel>
                    <v-expansion-panel-header disable-icon-rotate>
                        Change Profile Picture
                        <template v-slot:actions>
                            <v-icon>
                                mdi-camera-account
                            </v-icon>
                        </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <change-profile-picture-form/>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel>
                    <v-expansion-panel-header disable-icon-rotate>
                        Edit Account details
                        <template v-slot:actions>
                            <v-icon>
                                mdi-account-details
                            </v-icon>
                        </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <change-account-details-form/>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel>
                    <v-expansion-panel-header disable-icon-rotate>
                        Edit Username
                        <template v-slot:actions>
                            <v-icon>
                                mdi-form-textbox
                            </v-icon>
                        </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <change-username-form/>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel>
                    <v-expansion-panel-header disable-icon-rotate>
                        Edit Password
                        <template v-slot:actions>
                            <v-icon>
                                mdi-form-textbox-password
                            </v-icon>
                        </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <change-password-form/>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapGetters } from 'vuex';
import ChangeProfilePictureForm from './components/ChangeProfilePictureForm';
import ChangeUsernameForm from './components/ChangeUsernameForm';
import ChangeAccountDetailsForm from './components/ChangeAccountDetailsForm';
import ChangePasswordForm from './components/ChangePasswordForm';
export default {
    components: {
        ChangeUsernameForm,
        ChangeAccountDetailsForm,
        ChangePasswordForm,
        ChangeProfilePictureForm
    },
    computed: mapGetters(['auth_user']),
    data () {
        return {
            panel: [],
        }
    },
    methods: {
        fillForm() {
            let user = this.$store.getters.auth_user;
            this.panel.push(user);
        }
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
    }
}
</script>