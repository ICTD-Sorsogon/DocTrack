<template>
    <v-card>
        <v-card-title primary-title>
            <v-toolbar flat>
                <v-toolbar-title>Office List Report</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
            <v-btn color="primary" dark class="mb-2 ma-1" @click.stop="dialog = true">
              <v-icon>mdi-plus</v-icon>ADD
            </v-btn>

            <v-menu left bottom>
                <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="primary"
                    dark
                    class="mb-2 ma-1"
                    v-bind="attrs"
                    v-on="on"
                    >
                    <v-icon>mdi-dots-vertical</v-icon> EXCEL
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item :key="1" @click="() => {}"> <v-icon class="ma-1">mdi-file-upload-outline</v-icon> Import </v-list-item>
                    <v-list-item :key="2" @click="() => {}"> <v-icon  class="ma-1">mdi-file-export-outline</v-icon> Export </v-list-item>
                </v-list>
            </v-menu>

            </v-toolbar>
        </v-card-title>
        <v-data-table
            :headers="headers"
            :items="offices"
            :items-per-page="10"
            :search="search"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-text-field v-model="search" label="Search" class="mx-4"/>
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="editOffice(item)">mdi-pencil </v-icon>
                <v-icon small @click="deleteOffice(item)"> mdi-delete </v-icon>
            </template>

        </v-data-table>
        <office-table-modal
            @close-dialog="closeDialog"
            :dialog="dialog"
            v-if="items && dialog == true"
            :selected_office="items"
        />
    </v-card>

</template>

<script>

import OfficeTableModal from './components/OfficeTableModal';
import { mapGetters, mapActions } from "vuex";
import { colors } from '../../constants';

export default {
    components: {OfficeTableModal},
    data() {
        return {
            headers: [
                { text: 'Office', value: 'name' },
                { text: 'Office Code', value: 'office_code' },
                { text: 'Contact Number', value: 'contact_number' },
                { text: 'Contact Email', value: 'contact_email' },
                { text: 'Action', value: 'actions' },
            ],
            search: '',
            dialog: false,
            items: ''
        }
    },
    computed: {
        ...mapGetters(['datatable_loader']),
        offices() {
            return this.$store.state.offices.offices;
        },
    },
    methods: {
        editOffice(item){
            //console.log("gg", item);
            //this.dialog = true;
            this.items = item;
            this.dialog = true;
            console.log(this.items);
        },
        deleteOffice(item){
            console.log("gg", item);
        },
        closeDialog(){
            this.dialog = false;
        }
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
    }
}
</script>