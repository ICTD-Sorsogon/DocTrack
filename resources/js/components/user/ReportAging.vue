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
            let documents = JSON.parse(JSON.stringify(
                this.$store.state.documents.tracking_reports
            ));

            documents.forEach(document => {
                var temp = 0;
                document.diff = [];
                document.senderDelayed = 0;
                document.receiverDelayed = 0;
                document.docketDelayed = 0;
                document.tracking_records.forEach(tracking => {
                    document.diff.push({action:tracking.action,last_touched:tracking.last_touched,office:tracking.user.office.name});
                });
                // console.log(document)
            });
            console.log(documents);
            // return documents;
        },
        // all_documents(){
        //     let offices = JSON.parse(JSON.stringify(
        //         this.$store.state.documents.tracking_reports
        //     ));
        //     offices.forEach(element => {
        //         var max = 0;
        //         var min = (element.transaction === 0 ? 0 : Number.POSITIVE_INFINITY);
        //         var total_average = 0;
        //         var temp=0;
        //         var delta = 0;
        //         var days = 0;
        //         element.delayed = 0;
        //         forEach(element.documents, function(element2) {
        //             element2.forEach(element3 => {
        //                 let created_at = new Date(element3.last_touched);
        //                 let now_date = new Date();
        //                 // console.log('past ',created_at ,"now ", now_date)
        //                 let difference = now_date.getDate() - created_at.getDate();
        //                 // console.log(difference)
        //                 // if (max < difference ) {
        //                 //     max = difference ;
        //                 //     element.longestDelay = this.getTimeHours(created_at,now_date);
        //                 // }
        //                 // if (min > difference ) {
        //                 //     min = difference;
        //                 //     element.fastestTransaction = this.getTimeHours(created_at, now_date)
        //                 // }
        //             });
        //             if(last(element2).action != 'received') {
        //                 var dates = map(element2, 'last_touched');
        //                 var start = 0;
        //                 var end = 0;
        //                 var difference = [];
        //                  if (dates.length > 1) {
        //                     for(var i = 0; i < dates.length - 1; i++) {
        //                         start = new Date(dates[i]);
        //                         end = new Date(dates[i+1]);
        //                         var diff = end.getDate() - start.getDate();
        //                         if (diff > 7) {
        //                             element.delayed += 1;
        //                         }
        //                         difference.push(end - start);
        //                     }
        //                     let average = mean(difference);
        //                     element2.average = average;
        //                     temp += average;
        //                 }
        //             }
        //         });
        //         element.average = temp;
        //         delta = Math.abs(element.average) / 1000;
        //         var days = Math.floor(delta / 86400);
        //         delta -= days * 86400;
        //         var hours = Math.floor(delta / 3600) % 24;
        //         delta -= hours * 3600;
        //         var minutes = Math.floor(delta / 60) % 60;
        //         delta -= minutes * 60;
        //         var seconds = delta % 60;
        //         element.avg_time = `${days}d${hours}h${minutes}m${seconds}s`
        //         // element.tracking_records.forEach(element => {
        //         //     console.log(_.meanBy(element, 'touched_by'));
        //         // });

        //         // element.tracking_records.forEach(element => {
        //         //     console.log(element);
        //         // })
        //         // var max = 0;
        //         // var min = (element.tracking_records.length === 0 ? 0 : Number.POSITIVE_INFINITY);
        //         // element.delayed = 0;
        //         // element.efficiency = 0;
        //         // element.longestDelay = '';
        //         // element.fastestTransaction  = '';
        //         // element.tracking_records.forEach(el => {
        //         //     let created_at = new Date(el.created_at);
        //         //     let now_date = new Date();
        //         //     let difference = now_date.getDate() - created_at.getDate();
        //         //     let total = element.tracking_records.length;
        //         //     // if (max < difference ) {
        //         //     //     max = difference ;
        //         //     //     element.longestDelay = this.getTimeHours(created_at, now_date);
        //         //     // }
        //         //     // if (min > difference ) {
        //         //     //     min = difference;
        //         //     //     element.fastestTransaction = this.getTimeHours(created_at, now_date)
        //         //     // }

        //         //     if (difference > 7) {
        //         //         element.delayed += 1;

        //         //     }
        //         //      element.efficiency = (((total - element.delayed )
        //         //      / total) * 100);
        //         // });
        //     });
        //     // console.log(offices);
        //     return offices;
        // }
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
        this.$store.dispatch('getDocument');
        this.$store.dispatch('getAllUsers');
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
    }
}
</script>