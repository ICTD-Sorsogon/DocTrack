<template>
    <v-container>
        <v-card flat>
            <v-card-title primary-title>
                Logs
                <v-row align="center" justify="end" class="pr-4">
                    <v-btn color="primary" @click="excel_dialog = true; dialog_title ='Logs'"
                    >
                    <v-icon
                        small
                        class="mr-2"
                    >
                        mdi-export
                    </v-icon>
                    Export</v-btn
                    >
                </v-row>
            </v-card-title>

            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
            ></v-text-field>

            <template>
                <v-data-table
                    :headers="headers"
                    :items="logs"
                    :search="search"
                    class="elevation-1"
                >
                    <template v-slot:top>

                        <v-dialog
                            :headers="headers2"
                            v-model="dialog"
                            max-width="80vw"
                        >
                            <v-card>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>

                                    <v-spacer></v-spacer>
                                    <v-icon
                                        @click="close"
                                            large
                                            class="ml-4"
                                        >
                                        mdi-close
                                    </v-icon>
                                </v-card-title>

                                <v-simple-table>
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                            <th class="text-left">
                                                Label
                                            </th>
                                            <th class="text-left">
                                                New
                                            </th>
                                            <th class="text-left">
                                                Old
                                            </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                            v-for="(item, index) in final_data"
                                            :key="index"
                                            >
                                            <td style="text-transform:capitalize" >{{item['key']}}</td>
                                            <td>{{ item['new'] }}</td>
                                            <td>{{ item['old'] }}</td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                </v-card-actions>

                            </v-card>
                        </v-dialog>

                        <v-dialog
                            :headers="headers2"
                            v-model="emptyLogDialog"
                            max-width="60vw"
                        >
                            <v-card>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>

                                    <v-spacer></v-spacer>
                                    <v-icon
                                        @click="close"
                                            large
                                            class="ml-4"
                                        >
                                        mdi-close
                                    </v-icon>
                                </v-card-title>

                                <v-simple-table>
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th class="text-left">
                                                    Label
                                                </th>
                                                <th class="text-left">
                                                    New
                                                </th>
                                                <th class="text-left">
                                                    Old
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><h4>No Data Found</h4></td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>

                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-icon
                            v-if="!item.original_values && !item.new_values"
                            medium
                            class="ml-3"
                            color="blue"
                            @click="emptyLogDialog = true"
                        >
                            mdi-more
                        </v-icon>
                        <v-icon
                            v-else
                            medium
                            class="ml-3"
                            color="blue"
                            @click="editItem(item)"
                        >
                            mdi-more
                        </v-icon>
                    </template>
                </v-data-table>
            </template>

        </v-card>
        <excel-dialog
            v-if="dialog_title && excel_dialog == true"
            :excel_dialog="excel_dialog"
            :dialog_title="dialog_title"
            dialog_type='export'
            :dialog_for="dialog_for"
            @close-dialog="closeDialog()"
        />
    </v-container>


</template>

<script>
import { mapGetters, mapActions } from "vuex";
import ExcelDialog from './components/ExcelDialog'

    export default {
        components:{
            ExcelDialog,
        },
        data() {
            return {
                emptyLogDialog: false,
                excel_dialog: false,
                dialog_for: 'exportLogs',
                dialog_title: null,
                dialog: false,
                dialogDelete: false,

                search: '',
                editedIndex: -1,
                editedItem: {
                    new_values: '',
                    action: 0,
                    table_name: 0,
                    remarks: '',
                },
                final_data: [],
                defaultItem: {
                    action: 0,
                    table_name: 0,
                    remarks: 0,
                },
                headers: [
                    { text: 'Username', value: 'user.username' },
                    { text: 'Action', value: 'action' },
                    { text: 'Remarks', value: 'remarks' },
                    { text: 'View More', value: 'actions', sortable: false },
                ],
                headers2: [
                    { text: 'New Values ', value: 'new_values' },
                    { text: 'Original Values ', value: 'original_values' },
                ],
                export_excel: [],
                data: []
            }
        },
        methods:{
            closeDialog(){
                this.excel_dialog = false;
            },

            initialize () {this.logs},
            editItem (item) {
                this.editedIndex = this.final_data.indexOf(item)
                this.dialog = true

                var log_new_key = [];
                var log_view_new = [];

                var log_view_old = [];
                var log_old_key = [];

                this.final_data = [];

                log_new_key = Object.keys(item.new_values);
                log_view_new = Object.values(item.new_values);

                if(item.original_values != null){
                    log_old_key = Object.keys(item.original_values);
                    log_view_old = Object.values(item.original_values);
                    for (var i = 0 ; i < log_old_key.length; i++){
                        this.final_data.push({
                            'key': log_new_key[i].split('_').join(' '),
                            'new': log_view_new[i],
                            'old': log_view_old[i]
                        })
                    }
                }else{
                    for (var i = 0 ; i < log_new_key.length; i++){
                        this.final_data.push({
                            'key': log_new_key[i].split('_').join(' '),
                            'new': log_view_new[i]
                        })
                    }
                }
            },
            close() {
                this.dialog = false
                this.emptyLogDialog = false
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                })
            },

            closeDialog(){
                this.excel_dialog = false;
            }
        },

        computed: {
        ...mapGetters(['all_users', 'offices', 'document_types']),
            logs(){
                var getAll = JSON.parse(JSON.stringify(this.$store.state.users.all_users))
                var new_logs = JSON.parse(JSON.stringify(this.$store.state.users.logs))
                if(new_logs.length > 0){
                    new_logs.forEach(log => {
                        // Rename Keys from Logs
                        const clone = (obj) => Object.assign({}, obj);

                        const renameKey = (object, key, newKey) => {
                            const clonedObj = clone(object);
                            const targetKey = clonedObj[key];
                            delete clonedObj[key];
                            clonedObj[newKey] = targetKey;
                            return clonedObj;
                        };

                        log.new_values = JSON.parse(log.new_values)
                        log.new_values = renameKey(log.new_values, 'document_type_id', 'document_type');
                        log.new_values = renameKey(log.new_values, 'destination_office_id', 'destination_office');

                        if(log.original_values){
                            log.original_values = JSON.parse(log.original_values)
                            log.original_values = renameKey(log.original_values, 'document_type_id', 'document_type');
                            log.original_values = renameKey(log.original_values, 'destination_office_id', 'destination_office');
                        }

                        // Loop all Users and Change Sender name to Users Name
                        getAll.forEach(user => {
                            if(user.id == log.new_values.sender_name) {
                                log.new_values.sender_name = user.last_name + ',' + user.first_name + ' ' +  user.middle_name + ' ' + user.suffix;
                            }

                        });

                        if(log.original_values){
                            getAll.forEach(user => {
                                if(user.id == log.original_values.sender_name) {
                                    log.original_values.sender_name = user.last_name + ',' + user.first_name + ' ' +  user.middle_name + ' ' + user.suffix;
                                }
                            });
                        }

                        // Loop all Document Type and Change Doc Type ID to Doc Type Name
                        this.document_types.forEach(doc_type => {
                            if(doc_type.id == log.new_values.document_type) {
                                log.new_values.document_type = doc_type.name
                            }

                        });

                        if(log.original_values){
                            this.document_types.forEach(doc_type => {
                                if(doc_type.id == log.original_values.document_type) {
                                    log.original_values.document_type = doc_type.name
                                }
                            });
                        }

                        // Loop all Office and Change Office ID to Office Name
                        this.offices.forEach(office => {
                            if(office.id == log.new_values.destination_office) {
                                log.new_values.destination_office = office.name
                            }

                        });

                        if(log.original_values){
                            this.offices.forEach(office => {
                                if(office.id == log.original_values.destination_office) {
                                    log.original_values.destination_office = office.name
                                }
                            });
                        }

                    });

                }

                return new_logs
            },
            formTitle () {
                return this.editedIndex !== -1 ? 'Logs Value' : 'Logs Value'
            },
        },
        watch: {
            dialog (val) {
                val || this.close()
            },
            dialogDelete (val) {
                val || this.closeDelete()
            },
        },

        created () {
            this.initialize();
        },
        mounted() {
           Echo.channel('office')
           .listen('Hello', (e) => {
                console.log(e)
                console.log('Helloooo')
            })
            this.$store.dispatch('unsetLoader');
            this.$store.dispatch('getLogs');
        }

}
</script>