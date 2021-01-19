<template>
    <v-dialog v-model="form_dialog" persistent scrollable max-width="1000px">
        <v-card v-if="selected_office">
            <v-container>
            <v-row>
                <v-col cols="6" sm="6">
                <v-card-title primary-title> {{ dialog_title}} </v-card-title>
                </v-col>
                <v-col cols="6" sm="6">
                    <v-card-actions class="mr-1">
                        <v-spacer></v-spacer>
                        <v-btn x-large color="gray" @click="$emit('close-dialog')" icon>
                        <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-col>
            </v-row>
            </v-container>
            <v-card-text>
                <ValidationObserver ref="observer" v-slot="{ valid }">
                    <v-form ref="form" lazy-validation>
                        <v-row>
                            <v-col cols="12" sm="8" md="8">
                                <ValidationProvider rules="required" v-slot="{ errors }">
                                    <v-text-field v-model="form.name" label="Office Name*" required />
                                    <span>{{ errors[0] }}</span>
                                </ValidationProvider>
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
                                <ValidationProvider rules="required" v-slot="{ errors }">
                                    <v-text-field v-model="form.office_code" label="Office Code*" required />
                                    <span>{{ errors[0] }}</span>
                                </ValidationProvider>
                            </v-col>
                            <v-col cols="12">
                                <ValidationProvider rules="required" v-slot="{ errors }">
                                    <v-text-field v-model="form.address" label="Office Address*" required />
                                    <span>{{ errors[0] }}</span>
                                </ValidationProvider>
                            </v-col>
                            <v-col cols="12" sm="6" md="6">
                                <v-text-field v-model="form.contact_number" label="Contact Number"/>
                            </v-col>
                            <v-col cols="12" sm="6" md="6">
                                <ValidationProvider rules="email" v-slot="{ errors }">
                                    <v-text-field v-model="form.contact_email" label="Email Address"/>
                                    <span>{{ errors[0] }}</span>
                                </ValidationProvider>
                            </v-col>
                        </v-row>
                        <v-row justify="end">
                            <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="form.form_mode == 'new_office'" @click="saveNewOffice"> SAVE </v-btn>
                            <v-btn color="primary" class="mb-5 mt-10 ma-5" :dark="valid" :loading="btnloading" :disabled="!valid" v-if="form.form_mode == 'edit_office'" @click="saveChangesToOffice"> SAVE CHANGES </v-btn>
                        </v-row>
                    </v-form>
                </ValidationObserver>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>

    import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';
    import { email, required } from '../../../validate'
    import { mapGetters } from 'vuex';

    export default {
        components: {
            ValidationProvider,
            ValidationObserver
        },
        props: [
            'selected_office',
            'form_dialog',
            'dialog_title'
        ],
        data() {
            return {
                form_old: {
                    id: '',
                    name: '',
                    address: '',
                    office_code: '',
                    contact_number: '',
                    contact_email: '',
                    form_mode: ''
                },
                form: {
                    id: '',
                    name: '',
                    address: '',
                    office_code: '',
                    contact_number: '',
                    contact_email: '',
                    form_mode: ''
                },
                valid: false,
                btnloading: false
            }
        },
        computed: {
            form_requests(){
                return this.$store.state.snackbars.form_requests;
            }
        },
        methods: {
            saveNewOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: 'No changes found',
                        color: '#1565C0',
                        icon: 'mdi-information-outline',
                    })
                }else{
                    this.btnloading = true;
                    this.$store.dispatch("createNewOffice", this.form).then(() => {
                        if(this.form_requests.request_status == 'SUCCESS') {
                            this.$store.dispatch('snackbars/setSnackbar', {
                                showing: true,
                                text: this.form_requests.status_message,
                                color: '#43A047',
                                icon: 'mdi-check-bold',
                            })
                            .then(() => {
                                this.btnloading = false;
                                this.$refs.form.reset();
                                this.$refs.observer.reset();
                                this.$store.dispatch('getOffices');
                            });
                        } else {
                            this.$store.dispatch('snackbars/setSnackbar', {
                                showing: true,
                                text: this.form_requests.status_message,
                                color: '#D32F2F',
                                icon: 'mdi-close-thick',
                            })
                            .then(() => {
                                this.btnloading = false;
                            });
                        }
                    });
                }
            },
            saveChangesToOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    this.$store.dispatch('snackbars/setSnackbar', {
                        showing: true,
                        text: 'No changes found',
                        color: '#1565C0',
                        icon: 'mdi-information-outline',
                    })
                }else{
                    this.btnloading = true;
                    this.$store.dispatch('updateExistingOffice', this.form).then(() => {
                        if(this.form_requests.request_status == 'SUCCESS') {
                            this.$store.dispatch('snackbars/setSnackbar', {
                                showing: true,
                                text: this.form_requests.status_message,
                                color: '#43A047',
                                icon: 'mdi-check-bold',
                            })
                            .then(() => {
                                Object.assign(this.form_old, this.form)
                                this.btnloading = false;
                                this.$store.dispatch('getOffices');
                            });
                        } else {
                            this.$store.dispatch('snackbars/setSnackbar', {
                                showing: true,
                                text: this.form_requests.status_message,
                                color: '#D32F2F',
                                icon: 'mdi-close-thick',
                            })
                            .then(() => {
                                this.btnloading = false;
                            });
                        }
                    });
                }
            }
        },
        mounted() {
            Object.assign(this.form_old, this.selected_office)
            Object.assign(this.form, this.selected_office)
        }
    }

</script>

<style>

</style>
