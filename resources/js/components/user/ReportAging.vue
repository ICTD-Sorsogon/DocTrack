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
    </v-card>
</template>

<script>
const week_in_milliseconds = 604800000;
import {max, min, mean} from 'lodash';
export default {
    data() {
        return {
            search: '',
            headers: [
                { text: 'Office', align: 'start',value: 'name'},
                { text: 'All Transaction', value: 'transactions', filterable:false},
                { text: 'Delayed Document', value: 'delayed', filterable:false},
                { text: 'Fastest Transaction', value: 'min', filterable:false},
                { text: 'Slowest Transaction', value: 'max', filterable:false},
                { text: 'Average Transaction Speed', value: 'mean', filterable:false},
                { text: 'Efficiency Rating', value: 'efficiency', filterable:false},
            ],
        }
    },
    computed: {
        summaries(){
            let summaries = JSON.parse(JSON.stringify(this.$store.state.documents.tracking_reports));
            let offices = JSON.parse(JSON.stringify(this.$store.state.offices.office_names));
            let data = [];
            for (const [key, value] of Object.entries(summaries)) {
                // console.log(value)
                if (value.length === 1) offices[value[0].office_id].transactions++
                else {
                    let created = value.filter(value => value.action === 'created');
                    if (created.length !== 0) {
                        // console.log(created[0].document.destination.length);
                        offices[created[0].office_id].transactions+= created[0].document.destination.length;
                        let acknowledged = value.filter(value => value.action === 'acknowledged');
                        if (acknowledged.length !== 0) {
                            let forwarded = value.filter(value => value.action === 'forwarded');
                            offices[acknowledged[0].office_id].transactions += acknowledged[0].document.destination.length;
                            offices[acknowledged[0].office_id].transaction_speed.push(new Date(acknowledged[0].created_at).getTime() - new Date(created[0].created_at).getTime())
                            if (forwarded.length !== 0) {
                                offices[acknowledged[0].office_id].transactions += 1;
                            }
                            if (new Date(acknowledged[0].created_at).getTime() -
                                    new Date(created[0].created_at).getTime() > week_in_milliseconds) offices[value[0].office_id].delayed += acknowledged[0].document.destination.length
                            let received = value.filter(value => value.action === 'received');
                            if (received.length > 0) {
                                received.forEach(element => {
                                    offices[element.office_id].transactions++
                                    offices[element.office_id].transaction_speed.push(new Date(element.created_at).getTime() - new Date(acknowledged[0].created_at).getTime())
                                    if (new Date(element.created_at).getTime() -
                                        new Date(acknowledged[0].created_at).getTime() > week_in_milliseconds) offices[acknowledged[0].office_id].delayed++
                                });
                            }
                        }
                    } else {
                        for (var i = 0; i < value.length; i++) {
                            if (i !== value.length - 1) {
                                if (new Date(value[i + 1].created_at).getTime() -
                                    new Date(value[i].created_at).getTime() > week_in_milliseconds) offices[value[i].office_id].delayed++
                                offices[value[i].office_id].transaction_speed.push(new Date(value[i + 1].created_at).getTime() - new Date(value[i].created_at).getTime())
                            }
                            offices[value[i].office_id].transactions++;
                        }
                    }
                }
            }
            for (const [key, value] of Object.entries(offices)) {
                if(value.transaction_speed.length > 0){
                    let transaction_speed_list = value.transaction_speed
                    value.min = this.timeConversion(min(transaction_speed_list))
                    value.max = this.timeConversion(max(transaction_speed_list))
                    value.mean = this.timeConversion(mean( transaction_speed_list ))
                } else {
                    value.min = value.max = value.mean = 'Not available'
                }
                value.efficiency = value.transactions !== 0 ?`${((value.transactions - value.delayed) /
                    value.transactions * 100).toFixed(2)}%` : 'Not available';
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
        filterOnlyOffice (value, search, item) {
            return value != null &&
            search != null &&
            typeof value === 'string' &&
            value.toString().toLowerCase().indexOf(search) !== -1
        },
        timeConversion(millisec) {
            var seconds = Math.trunc(millisec / 1000);
            var minutes = Math.trunc(millisec / (1000 * 60));
            var hours = Math.trunc(millisec / (1000 * 60 * 60));
            var days = Math.trunc(millisec / (1000 * 60 * 60 * 24));
            if (seconds < 60) return `${seconds}s`;
            else if (minutes < 60) return `${minutes}m`;
            else if (hours < 24) return `${hours}h`;
            else return `${days}d`
        }
    }
}
</script>