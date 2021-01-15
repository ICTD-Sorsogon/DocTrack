<template>
<v-dialog v-model="dialog" persistent scrollable max-width="1000px">
      <v-card v-if="selected_office">
        <v-container>
          <v-row>
            <v-col cols="6" sm="6">
              <v-card-title primary-title> Office Details </v-card-title>
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

          <!--<v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Subject</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_office.name
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>-->
          <v-row>
              <v-col cols="12" sm="8" md="8">
                <v-text-field v-model="form.name" label="Office Name*" required ></v-text-field>
              </v-col>
              <v-col cols="12" sm="4" md="4">
                <v-text-field v-model="form.office_code" label="Office Code*" required ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field v-model="form.address" label="Office Address*" required ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-text-field v-model="form.contact_number" label="Contact Number"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-text-field v-model="form.contact_email" label="Email Address"></v-text-field>
              </v-col>
            </v-row>
            <v-row justify="end">
                <v-btn color="primary" dark class="mb-5 mt-10 ma-5" v-if="form.form_mode == 'new_office'" @click="saveNewOffice"> SAVE </v-btn>
                <v-btn color="primary" dark class="mb-5 mt-10 ma-5" v-if="form.form_mode == 'edit_office'" @click="saveChangesToOffice"> SAVE CHANGES </v-btn>
            </v-row>

        </v-card-text>
      </v-card>
    </v-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
    export default {
        props: ['selected_office', 'dialog'],
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
                }
            }
        },
        computed: {
            //...mapGetters(['form_requests']),
            form_requests(){
                return this.$store.state.offices.form_requests;
            }

        },
        methods: {
            saveNewOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    //console.log("no changes found");
                    this.$store.dispatch('setSnackbar', {
                        showing: true,
                        text: 'No changes found',
                        color: '#1565C0',
                        icon: 'mdi-information-outline',
                    })
                }else{
                    this.$store.dispatch("createNewOffice", this.form).then(() => {

                        /*if (this.offices) {
                            this.$store.dispatch('setSnackbar', {
                                showing: true,
                                text: 'Successfully edited',
                                color: '#43A047',
                                icon: 'mdi-check-bold',
                            })
                            this.$store.dispatch("unsetDataTableLoader");
                            console.log(this.form.name);
                        }*/

                        console.log('STATUS:', this.form_requests.request_status)


                        if(this.form_requests.request_status == 'SUCCESS') {
                            this.$store.dispatch('setSnackbar', {
                                showing: true,
                                text: this.form_requests.status_message,
                                color: '#43A047',
                                icon: 'mdi-check-bold',
                            })
                            .then(() => {
                                //this[this.button_loader] = false
                                //this.button_loader = null;
                                //this.$refs.form.reset();
                                //this.$refs.observer.reset();

                             this.$store.dispatch('getOffices');


                            });

                        } else {
                            this.$store.dispatch('setSnackbar', {
                                showing: true,
                                text: this.form_requests.status_message,
                                color: '#D32F2F',
                                icon: 'mdi-close-thick',
                            })
                            .then(() => {
                                //this[this.button_loader] = false
                                //this.button_loader = null;

                            });
                        }
                    });
                    //console.log('yes you have changes');

                }


                console.log("form_request:", this.form_requests);

            },
            saveChangesToOffice(){
                if(JSON.stringify(this.form) === JSON.stringify(this.form_old)){
                    //console.log("no changes found");
                    this.$store.dispatch('setSnackbar', {
                        showing: true,
                        text: 'No changes found',
                        color: '#1565C0',
                        icon: 'mdi-information-outline',
                    })
                }else{
                    //console.log('yes you have changes');
                    this.$store.dispatch('setSnackbar', {
                        showing: true,
                        text: 'Successfully edited',
                        color: '#43A047',
                        icon: 'mdi-check-bold',
                    })
                }
            }
        },
        mounted() {
            Object.assign(this.form_old, this.selected_office)
            Object.assign(this.form, this.selected_office)
            console.log('ff',this.selected_office);
        }
    }

</script>

<style>

</style>
