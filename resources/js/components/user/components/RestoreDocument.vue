<template>
    <v-dialog v-model="param.dialog" persistent scrollable max-width="1000px">
        <v-card v-if="param">
            <v-container>
            <v-row>
                <v-col cols="6" sm="6">
                <v-card-title primary-title> {{ param.title}} </v-card-title>
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
                <v-list-item two-line v-for="docu in param.document.incoming_trashed" :key="docu.recipient_id">
                    <v-list-item-content>
                        <v-list-item-title>Created Date:| {{ docu.created_at }}</v-list-item-title>
                        <v-list-item-subtitle>Office: | {{ docu.destination_office }}</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>


                <v-list two-line>
                    <v-list-item-group
                        v-model="selected"
                        active-class="pink--text"
                        multiple
                    >
                        <template v-for="(item, index) in items">
                        <v-list-item :key="item.title" @click="gg">
                            <template v-slot:default="{ active }">
                            <v-list-item-content>
                                <v-list-item-title v-text="item.title"></v-list-item-title>

                                <v-list-item-subtitle
                                class="text--primary"
                                v-text="item.headline"
                                ></v-list-item-subtitle>

                                <v-list-item-subtitle v-text="item.subtitle"></v-list-item-subtitle>
                            </v-list-item-content>

                            <v-list-item-action>
                                <v-list-item-action-text v-text="item.action"></v-list-item-action-text>

                                <v-icon
                                v-if="!active"
                                color="grey lighten-1"
                                >
                                mdi-star-outline
                                </v-icon>

                                <v-icon
                                v-else
                                color="yellow darken-3"
                                >
                                mdi-star
                                </v-icon>
                            </v-list-item-action>
                            </template>
                        </v-list-item>

                        <v-divider
                            v-if="index < items.length - 1"
                            :key="index"
                        ></v-divider>
                        </template>
                    </v-list-item-group>
                </v-list>

                <!--<ValidationObserver ref="observer" v-slot="{ valid }">
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
                </ValidationObserver>-->
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
        props: ['param'],
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
                btnloading: false,
                selected: [2],
                items: [
                    {
                    action: '15 min',
                    headline: 'Brunch this weekend?',
                    subtitle: `I'll be in your neighborhood doing errands this weekend. Do you want to hang out?`,
                    title: 'Ali Connors',
                    },
                    {
                    action: '2 hr',
                    headline: 'Summer BBQ',
                    subtitle: `Wish I could come, but I'm out of town this weekend.`,
                    title: 'me, Scrott, Jennifer',
                    },
                    {
                    action: '6 hr',
                    headline: 'Oui oui',
                    subtitle: 'Do you have Paris recommendations? Have you ever been?',
                    title: 'Sandra Adams',
                    },
                    {
                    action: '12 hr',
                    headline: 'Birthday gift',
                    subtitle: 'Have any ideas about what we should get Heidi for her birthday?',
                    title: 'Trevor Hansen',
                    },
                    {
                    action: '18hr',
                    headline: 'Recipe to try',
                    subtitle: 'We should eat this: Grate, Squash, Corn, and tomatillo Tacos.',
                    title: 'Britta Holt',
                    },
                ],
            }
        },
        computed: {
            request(){
                return this.$store.state.snackbars.request;
            }
        },
        methods: {
            gg() {
                console.log('clicked')
                //this.selected.pop()
            }
            /*saveNewOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    this.$store.dispatch('setSnackbar', {
                        type: 'info',
                        message: 'No changes found',
                    })
                }else{
                    this.btnloading = true;
                    this.$store.dispatch("createNewOffice", this.form).then(() => {
                        if(this.request.status == 'success') {
                            this.$store.dispatch('setSnackbar', {
                                type: 'success',
                                message: this.request.message,
                            })
                            .then(() => {
                                this.btnloading = false;
                                this.$refs.form.reset();
                                this.$refs.observer.reset();
                                this.$store.dispatch('getOffices');
                            });
                        } else if(this.request.status == 'failed'){
                            this.$store.dispatch('setSnackbar', {
                                type: 'error',
                                message: this.request.message,
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
                    this.$store.dispatch('setSnackbar', {
                        type: 'info',
                        message: 'No changes found'
                    })
                }else{
                    this.btnloading = true;
                    this.$store.dispatch('updateExistingOffice', this.form).then(() => {
                        if(this.request.status == 'success') {
                            this.$store.dispatch('setSnackbar', {
                                type: 'success',
                                message: this.request.message
                            })
                            .then(() => {
                                Object.assign(this.form_old, this.form)
                                this.btnloading = false;
                                this.$store.dispatch('getOffices');
                            });
                        } else if(this.request.status == 'failed'){
                            this.$store.dispatch('setSnackbar', {
                                type: 'error',
                                message: this.request.message
                            })
                            .then(() => {
                                this.btnloading = false;
                            });
                        }
                    });
                }
            }*/
        },
        mounted() {
            //Object.assign(this.form_old, this.selected_office)
            //Object.assign(this.form, this.selected_office)
            console.log('restore this:')
            console.log(this.param.document.incoming_trashed)
        }
    }

</script>

<style>

</style>
