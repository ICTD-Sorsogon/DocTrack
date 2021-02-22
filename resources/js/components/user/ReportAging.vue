<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <v-row>
            <v-col cols="12">
                  <v-data-table
                    :search="search"
                    :items="summaries"
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
        // TODO: Check the output against different cases
        summaries(){
            let summaries = JSON.parse(JSON.stringify(this.$store.state.documents.tracking_reports));
            let offices = JSON.parse(JSON.stringify(this.$store.state.offices.office_names));
            let data = [];
            for (const [key, value] of Object.entries(summaries)) {
                if (value.length === 1) offices[value[0].office_id].transactions++
                else {
                    let created = value.filter(value => value.action === 'created');
                    if (created.length > 1) {
                        offices[value[0].office_id].transactions+= created.length;
                        let acknowledged = value.filter(value => value.action === 'acknowledged');
                        if (acknowledged.length > 0) {
                            if (new Date(acknowledged[0].created_at).getDate() -
                                    new Date(created[0].created_at).getDate() > 7) offices[value[i].office_id].delayed++
                            let received = value.filter(value => value.action === 'received');
                            if (received.length > 0) {
                                received.forEach(element => {
                                    if (new Date(element.created_at).getDate() -
                                        new Date(acknowledged[0].created_at).getDate() > 7) offices[value[i].office_id].delayed++
                                });
                            }
                        }
                    } else {
                        for (var i = 0; i < value.length -1; i++) {
                            if (i !== value.length - 1) {
                                if (new Date(value[i + 1].created_at).getDate() -
                                    new Date(value[i].created_at).getDate() > 7) offices[value[i].office_id].delayed++
                            }
                            offices[value[i].office_id].transactions++;
                        }
                    }
                }
            }
            for (const [key, value] of Object.entries(offices)) {
                value.efficiency = value.transactions !== 0 ?`${((value.transactions - value.delayed) /
                    value.transactions * 100).toFixed(2)}%` : 'None';
                data.push(value);
            }
            console.log(data);
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