<template>
    <v-dialog v-model="param.dialog" persistent scrollable max-width="1000px">
        <v-card v-if="param">
            <v-toolbar color="primary" dark>
                <v-row>
                    <v-col cols="6" sm="6">
                        <v-card-title primary-title> {{ param.title}} </v-card-title>
                    </v-col>
                    <v-col cols="6" sm="6">
                        <v-card-actions class="mr-0">
                            <v-spacer/>
                            <v-btn x-large color="gray" @click="$emit('close-dialog')" icon> <v-icon>mdi-close</v-icon> </v-btn>
                        </v-card-actions>
                    </v-col>
                </v-row>
            </v-toolbar>
            <v-card-text>
                <v-simple-table>
                    <template v-slot:top>
                        <!--<v-list-item>rey
                        <v-list-item-content>
                            <v-list-item-title>Single-line item</v-list-item-title>
                        </v-list-item-content>
                        </v-list-item>-->
                       <div class="mt-6">
                            <tr>
                                <td>Document Origin: <strong> {{ param.document.origin_office.name + ' (' + param.document.origin_office.office_code + ')' }} </strong>  </td>
                                <td> <v-btn width="100%" color="primary" style="background-color:#C0DFFD" class="ml-5" rounded text @click="confirmRestore('All', param.document)">Restore All</v-btn> </td>
                            </tr>
                       </div>
                    </template>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th class="text-left"> Nameu </th>
                                <th class="text-left"> Calories </th>
                                <th class="text-center"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="docu in param.document.incoming_trashed" :key="docu.recipient_id">
                                <td>Created Date:| {{ docu.created_at }}</td>
                                <td>Office: | {{ docu.destination_office }}</td>
                                <td> <v-btn width="100%" color="primary" rounded text @click="confirmRestore('', docu)" :disabled="docu.deleted_at == null">Restore</v-btn> </td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>


                <!--<v-btn color="primary" class="ma-2" dark @click="dialog2 = true"> Open Dialog 2 </v-btn>-->
                <v-dialog v-model="dialog2" max-width="500px" persistent>
                    <v-card>
                        <v-card-title> Confirm Restore</v-card-title>
                        <v-card-text>
                            <v-text-field label="Office Code*" required>eryreyery</v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer/>
                                <v-btn color="primary" class="ma-1" text @click="dialog2 = false"> CANCEL </v-btn>
                                <v-btn color="primary" class="ma-1" dark @click="restoreDocument"> SUBMIT </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>





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
                dialog2: false,
                restoreParam: {
                    isRoot: false,
                    dbId: '',
                    documentId: ''
                }
            }
        },
        computed: {
            request(){
                return this.$store.state.snackbars.request;
            }
        },
        methods: {
            restoreDocument() {
                console.log('clicked')

                setTimeout(function() {
                    this.dialog2 = false
                }.bind(this), 2000);
            },
            confirmRestore(type, data){
                this.dialog2 = true
                if (type == 'All') {
                    console.log('all')
                    this.restoreParam = {
                        isRoot: true,
                        dbId: data.id,
                        documentId: data.id
                    }

                } else {
                    console.log('each')
                    this.restoreParam = {
                        isRoot: false,
                        dbId: data.recipient_id,
                        documentId: data.document_id
                    }
                }
                console.log(data)
                console.log('par:', this.restoreParam)

                this.$store.dispatch('restoreDocument', this.restoreParam).then(() => {
                    console.log('DONE RESTORED...')
                })
            }
        },
        mounted() {
            //console.log('restore this:')
           // console.log(this.param.document.incoming_trashed)
           // console.log('all item:')
            //console.log(this.param.document)
        }
    }

</script>

<style>

</style>
