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
                <template v-slot:[`item.efficiency`] = "{ item }">
                    {{item.efficiency}}%
                </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
import {map, forEach, last, mean} from 'lodash';
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
                // ,
                // { text: 'All Transaction', value: 'transaction_count' },
                // { text: 'Delayed Document', value: 'delayed' },
                // { text: 'Fastest Transaction', value: 'fastestTransaction' },
                // { text: 'Slowest Transaction', value: 'longestDelay' },
                // { text: 'Average Transaction Speed', value: 'speed' },
                // { text: 'Efficiency Rating', value: 'efficiency' },
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
            for (const [key, value] of Object.entries(summaries)) {
                if (value.length === 1) {
                    console.log('single entry');
                }
                for(var i = 0; i < value.length; i++) {
                    console.log(value[i]);
                    // value.forEach(element =>{
                    //     offices[element.office_id].transactions++;
                    // })
                    // console.log(new Date(value[i].created_at).getDate()- new Date(value[i-1].created_at).getDate());

                    // var date1 = new Date(value[i].created_at).getDate();
                    // var date2 = new Date(value[i-1].created_at).getDate();
                    // var diff = (date2 - date1);
                    // if(diff > 7) {
                        // offices[value.office_id].delayed++;
                    // }
                    // offices[value.office_id].transactions++;
                }
                // value.forEach(element => {
                //     if(element.multiple) {
                //         console.log('multiple entry');
                //     }else {

                //     }
                // });
            }
            // console.log(offices);
            // console.timeEnd("Execution Time");
        },
    },
    methods: {
        getTimeHours(past, present) {
            var present = new Date(present);
            var past = new Date(past);
            var delta = Math.abs(present - past) / 1000;
            var days = Math.floor(delta / 86400);
            delta -= days * 86400;
            var hours = Math.floor(delta / 3600) % 24;
            delta -= hours * 3600;
            var minutes = Math.floor(delta / 60) % 60;
            delta -= minutes * 60;
            var seconds = delta % 60;
            return days +"d " + hours + "h " + minutes + "m";
        },
        averageElapsedTime(time_array) {
            if (time_array.length < 2) {
                return;
            }
            var start = 0;
            var end = 0;
            var difference = [];
            for(i = 0; i < time_array.length; i++) {
                start = new Date(time_array[i]);
                end = new Date(time_array[i+1]);
                difference.push(start.getTime() - end.getTime());
            }
            return _.mean(difference);
        }
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('getOfficeNameList');
    }
}
</script>