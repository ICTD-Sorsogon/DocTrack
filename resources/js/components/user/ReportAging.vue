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
                // console.table(value)
                var date_received = value.filter(value => value.action === 'received');
                var date_created = value.filter(value => value.action === 'created');
                var date_acknowledged = value.filter(value => value.action === 'acknowledged');
                for (var i = 0; i <= value.length - 1; i++) {
                    if (value[i].document.multiple) {
                        // offices[value[i].office_id].transactions++;
                        if (value[i].action == 'created') {
                            offices[value[i].office_id].transactions++;
                            // date_acknowledged.forEach(el => {
                            //     var d1 = new Date(el.created_at);
                            //     var d2 = new Date(offices[value[i].office_id].created_at);
                            //     var diff = d2.getDate() - d1.getDate();
                            //     if (diff > 7) {
                            //         offices[value[i].office_id].delayed++;
                            //     }
                                
                            // })
                        }
                        if (value[i].action == 'received') {
                            offices[value[i].office_id].transactions++;
                        }
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
                // for (var i = 0; i <= value.length - 1; i++) {
                //     if (value[i].document.multiple) {
                //         if (value[0].action == 'created') {
                //             // date_acknowledged.forEach(el => { 
                //                 var date1 = new Date(value[i].created_at);
                //                 var date2 = new Date(date_acknowledged[0].created_at);
                //                 diff = date2.getDate() - date1.getDate();
                //                 if (diff > 7) {
                //                     offices[value[i].office_id].delayed++;
                //                     // console.log('delay',diff)
                //                 }
                //                     offices[value[i].office_id].transactions++;
                //             // });
                //         }
                //     }
                // }
                
                for (var i = 0; i <= value.length - 1; i++) {
                    if (value[i].document.multiple) {
                        new Date(date_created[0].created_at).getDate()
                        if (value[i].action == 'acknowledged') {
                            date_received.forEach(el => { 
                                var date1 = new Date(value[i].created_at);
                                var date2 = new Date(el.created_at);
                                diff = date2.getDate() - date1.getDate();
                                if (diff > 7) {
                                    offices[value[i].office_id].delayed++;
                                    // console.log('delay',diff)
                                }
                                    offices[value[i].office_id].transactions++;
                            });
                        }
                        
                    }
                } 
                //better solution for this
                for (const [key, value] of Object.entries(offices)) {
                    value.efficiency = value.transactions !== 0 ?`${((value.transactions - value.delayed) /
                        value.transactions * 100).toFixed(2)}%` : 'None';
                    data.push(value);
                }
            }

            console.table(offices)
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