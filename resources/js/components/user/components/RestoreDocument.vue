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
                        <div class="mt-6">
                            <tr>
                                <td>Document Origin: <strong> {{ document.origin_office.name + ' (' + document.origin_office.office_code + ')' }} </strong>  </td>
                                <td> <v-btn width="100%" color="primary" style="background-color:#C0DFFD" class="ml-5" rounded text @click="confirmRestore('All', document, document.origin_office.office_code )">Restore All</v-btn> </td>
                            </tr>
                        </div>
                        <div class="mb-3">
                            <div class="">
                                <label for="">Subject: <strong> {{document.subject}} </strong> </label><br>
                                <label for="">Tracking Code: <strong> {{document.tracking_code}} </strong> </label>
                            </div>
                        </div>
                    </template>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th class="text-left"> Destination Office </th>
                                <th class="text-left"> Document Last Update </th>
                                <th class="text-center"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="docu in selectedDocument" :key="docu.recipient_id">
                                <td>{{ docu.office }}</td>
                                <td>{{ new Date(docu.updated_at).toISOString().substr(0, 10) }}</td>
                                <td> <v-btn width="100%" color="primary" rounded text @click="confirmRestore('', docu, docu.office_code)" :disabled="docu.deleted_at == null">Restore</v-btn> </td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>

                <v-dialog v-model="confirmDialog" max-width="500px" persistent>
                        <v-card>
                            <v-card-title> Confirm Restore</v-card-title>
                            <v-card-text>
                                <v-text-field v-model="confirmField" label="Office Code*" required/>
                                <span v-if="!valid" style="color:red">Please re-type <strong> {{confirmOfficeCode}} </strong> to confirm</span>
                                <span v-if="valid" style="color:green">Confirmation is valid, Please click submit button to proceed</span>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer/>
                                <v-btn color="primary" class="ma-1" text @click="confirmDialog = false"> CANCEL </v-btn>
                                <v-btn color="primary" class="ma-1" @click="restoreDocument" :dark="valid" :disabled="!valid"> SUBMIT </v-btn>
                            </v-card-actions>
                        </v-card>
                </v-dialog>

            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>

    import { mapGetters } from 'vuex';
    export default {
        props: ['param'],
        data() {
            return {
                valid: false,
                confirmDialog: false,
                restoreParam: {},
                confirmOfficeCode: '',
                confirmField: ''
            }
        },
        watch: {
            'selectedDocument'(newVal){
                try {
                    newVal.filter(d=>d.deleted_at != null).length ?? this.$emit('close-dialog')
                } catch (error) {
                    this.$emit('close-dialog')
                }
            },
            'confirmField'(newVal){
                this.valid = (this.confirmOfficeCode.toString().trim().toLowerCase() == newVal.toString().trim().toLowerCase())?true:false
            }
        },
        computed: {
            ...mapGetters(['request', 'offices', 'documentsArchive']),
            document(){
                return this.param.document
            },
            selectedDocument(){
                var arc = this.documentsArchive[0].selected
                try {
                    var documents = arc[arc.filter.toLowerCase()].data.find(d=>d.id==this.document.id).incoming_trashed
                    documents.forEach(document => {
                        var destination = this.offices.find(o=>o.id == document.destination_office)
                        if (destination) {
                            document.office_name = destination.name
                            document.office_code = destination.office_code
                            document.office = destination.name + ' (' + destination.office_code+ ')'
                        }
                    });
                    return documents
                } catch (error) {}
                return false
            }
        },
        methods: {
            restoreDocument() {
                if (this.valid) {
                    this.$store.dispatch('restoreDocument', this.restoreParam).then(() => {
                        if(this.request.status == 'success') {
                            this.$store.dispatch('setSnackbar', {
                                title: 'Restore: Success',
                                type: 'success',
                                message: this.request.message,
                            })
                            .then(() => {
                                this.$emit('reload-data', (this.param.filter=='Year')?true:false, (this.param.filter=='Date')?true:false)
                                this.confirmField = ''
                                this.confirmDialog = false
                                this.$store.dispatch("getActiveDocuments")
                            });
                        } else if(this.request.status == 'failed'){
                            this.$store.dispatch('setSnackbar', {
                                title: 'Restore: Failed',
                                type: 'error',
                                message: this.request.message,
                            })
                        }
                    })
                }
            },
            confirmRestore(type, data, office_code){
                this.confirmOfficeCode = office_code
                this.confirmField = ''
                this.confirmDialog = true
                if (type == 'All') {
                    this.restoreParam = {
                        isRoot: true,
                        dbId: data.id,
                        documentId: data.id
                    }
                } else {
                    this.restoreParam = {
                        isRoot: false,
                        dbId: data.recipient_id,
                        documentId: data.document_id
                    }
                }
            }
        }
    }

</script>

<style>

</style>
