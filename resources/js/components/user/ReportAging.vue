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
const week_in_milliseconds = 604800000
import {mean} from 'lodash';
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
                { text: 'Fastest Transaction', value: 'min' },
                { text: 'Slowest Transaction', value: 'max' },
                { text: 'Average Transaction Speed', value: 'mean' },
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
                            offices[value[0].office_id].transactions+= acknowledged.length;
                            offices[value[0].office_id].transaction_speed.push( new Date(acknowledged[0].created_at).getTime() - new Date(created[0].created_at).getTime() )
                            if (new Date(acknowledged[0].created_at).getMilliseconds() -
                                    new Date(created[0].created_at).getMilliseconds() > week_in_milliseconds) offices[value[i].office_id].delayed++
                            let received = value.filter(value => value.action === 'received');
                            if (received.length > 0) {
                                received.forEach(element => {
                                    offices[element.office_id].transactions++
                                    offices[element.office_id].transaction_speed.push( new Date(element.created_at).getTime() - new Date(acknowledged[0].created_at).getTime())
                                    if (new Date(element.created_at).getMilliseconds() -
                                        new Date(acknowledged[0].created_at).getMilliseconds() > week_in_milliseconds) offices[value[i].office_id].delayed++
                                });
                            }
                        }
                    } else {
                        for (var i = 0; i < value.length -1; i++) {
                            if (i !== value.length - 1) {
                                if (new Date(value[i + 1].created_at).getMilliseconds() -
                                    new Date(value[i].created_at).getMilliseconds() > week_in_milliseconds) offices[value[i].office_id].delayed++
                            }
                            offices[value[i].office_id].transaction_speed.push( new Date(value[i + 1].created_at).getTime() - new Date(value[i].created_at).getTime())
                            offices[value[i].office_id].transactions++;
                        }
                    }
                }
            }
            for (const [key, value] of Object.entries(offices)) {
                if(value.transaction_speed.length > 0){
                    let transaction_speed_list = value.transaction_speed
                    let transaction_min = Math.min.apply(Math, transaction_speed_list)
                    let transaction_max = Math.max.apply(Math, transaction_speed_list)

                    value.min = this.timeConversion(transaction_min)
                    value.max = this.timeConversion(transaction_max)
                    value.mean = this.timeConversion(mean( transaction_speed_list ))

                } else {
                    value.min = 'None'
                    value.max = 'None'
                    value.mean = 'None'
                }

                value.efficiency = value.transactions !== 0 ?`${((value.transactions - value.delayed) /
                    value.transactions * 100).toFixed(2)}%` : 'None';
                data.push(value);
            }

            return data;
        },
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('getOfficeNameList');
    },
    methods: {
        timeConversion(millisec) {

            var seconds = (millisec / 1000).toFixed(1);

            var minutes = (millisec / (1000 * 60)).toFixed(1);

            var hours = (millisec / (1000 * 60 * 60)).toFixed(1);

            var days = (millisec / (1000 * 60 * 60 * 24)).toFixed(1);

            if (seconds < 60) {
                return seconds + " Sec";
            } else if (minutes < 60) {
                return minutes + " Min";
            } else if (hours < 24) {
                return hours + " Hrs";
            } else {
                return days + " Days"
            }
        }
    }
}
</script>