<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <v-row>
            <v-col cols="12">
                  <v-data-table
                    :search="search"
                    :items="all_documents"
                    :items-per-page="10"
                    :headers="headers"
                    class="elevation-1"
                >
                <template v-slot:top>
                    <v-text-field
                        prepend-inner-icon="mdi-magnify"
                        v-model="search"
                        label="Search"
                        class="mx-4"
                    />
                </template>
                <!-- <template v-slot:[`item.efficiency`] = "{ item }">
                    {{item.efficiency}}
                </template> -->
                </v-data-table>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
import {map, forEach, last, mean , castArray} from 'lodash';
export default {
    data() {
        return {
            search: '',
            headers: [
                {
                    text: 'Office',
                    align: 'start',
                    value: 'name',
                }
                ,
                { text: 'All Transaction', value: 'transactions' },
                { text: 'Delayed Document', value: 'delayed' },
                // { text: 'Fastest Transaction', value: 'fastestTransaction' },
                // { text: 'Slowest Transaction', value: 'longestDelay' },
                // { text: 'Average Transaction Speed', value: 'speed' },
                { text: 'Efficiency Rating', value: 'efficiency' },
            ],
        }
    },
    computed: {
        //TOFIX elapse time for fastest and longest transaction should check
        // in b/w transaction by checking previous last_touched
        all_documents(){
            // console.time("Execution Time")
            let summaries = JSON.parse(JSON.stringify(this.$store.state.documents.tracking_reports));
            let offices = JSON.parse(JSON.stringify(this.$store.state.offices.office_names));
            let data = [];
            for (const [key, value] of Object.entries(summaries)) {
                if (value.length === 0) {
                    offices[value[i].office_id].slowest = 0;
                    offices[value[i].office_id].fastest = 0;
                    offices[value[i].office_id].transactions = 0;
                }
                
                if (value.length === 1) {
                    console.log('single entry');
                }
                for (var i = 0; i <= value.length - 1; i++) {
                        if (value[i].document.multiple) {
                            console.log('multiple entry');
                        } else {
                            var diff = 0;
                            if (i != value.length - 1) {
                                offices[value[i].office_id].transactions++;
                                var date1 = new Date(value[i].created_at);
                                var date2 = new Date(value[i+1].created_at);
                                diff = date2.getDate() - date1.getDate();
                                if (diff > 7) {
                                    offices[value[i].office_id].delayed++;
                                }
                            } else {
                                offices[value[i].office_id].transactions++;
                            }
                        }
                        offices[value[i].office_id].efficiency = 
                            (offices[value[i].office_id].transactions -
                            offices[value[i].office_id].delayed)
                }
                //better solution for this
                for (const [key, value] of Object.entries(offices)) {
                    value.efficiency = value.transactions !== 0 ?`${(value.transactions - value.delayed) /
                        value.transactions * 100}%` : 'None';
                    data.push(value);
                }
            }
            return data;
        },
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('getOfficeNameList');
    }
}
</script>