<template>
    <v-row>
        <v-col cols="12">
            <v-data-table
                :search="search"
                :items="tracking_reports"
                :items-per-page="10"
                :headers="headers"
                class="elevation-1"
                :custom-filter="filterOnlyOffice"
            >
            <template v-slot:top>
                <v-text-field
                    prepend-inner-icon="mdi-magnify"
                    v-model="search"
                    label="Search"
                    class="mx-4"
                />
            </template>
            </v-data-table>
        </v-col>
    </v-row>
</template>
<script>
import {mapState} from 'vuex';

export default {
    data() {
        return {
            search: '',
            headers: [
                { text: 'Office', align: 'start',value: 'office.name'},
                { text: 'All Transaction', value: 'transactions', filterable:false},
                { text: 'Delayed Document', value: 'delayed', filterable:false},
                { text: 'Fastest Transaction', value: 'fastest', filterable:false},
                { text: 'Slowest Transaction', value: 'slowest', filterable:false},
                { text: 'Average Transaction Speed', value: 'average', filterable:false},
                { text: 'Efficiency Rating', value: 'efficiency', filterable:false},
            ],
        }
    },
    computed: {
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('getOfficeNameList');
    }
}
</script>