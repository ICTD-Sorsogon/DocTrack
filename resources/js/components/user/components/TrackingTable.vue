<template>
    <v-row>
        <v-col cols="12">
            <v-data-table
                :search="search"
                :items="data"
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
import { mapState, mapGetters } from 'vuex';
import { groupBy, pluck, getRecordSpeed } from '../../../helpers';
import { formatDistanceStrict } from 'date-fns';

export default {
    data() {
        return {
            search: '',
            headers: [
                { text: 'Office', align: 'start',value: 'office'},
                { text: 'All Transaction', value: 'transaction', filterable:false},
                { text: 'Delayed Document', value: 'delayed', filterable:false},
                { text: 'Fastest Transaction', value: 'fast.fast', filterable:false},
                { text: 'Slowest Transaction', value: 'slow.slow', filterable:false},
                { text: 'Average Transaction Speed', value: 'average', filterable:false},
                { text: 'Efficiency Rating', value: 'efficiency', filterable:false},
            ],
        }
    },
    computed: {
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
        ...mapGetters(['offices']),
        data(){
            let offices = pluck(this.offices, 'name')
            let summary = [];
            let record = groupBy(JSON.parse(JSON.stringify(this.tracking_reports)), 'transaction_of')
            for(let i in record) {
                let transaction = record[i].length   
                let delayed = record[i].filter(r=>r.delayed).length
                let efficiency = ((transaction - delayed) / transaction * 100).toFixed(2) + '%'
                let average = formatDistanceStrict(0, record[i].reduce((counter,value,index)=>{return (counter*index+value.speed)/(index+1); debugger},0)* 100); 
                let slow =  getRecordSpeed(record[i], 'slow')
                let fast =  getRecordSpeed(record[i], 'fast')
                let office = offices[i-1]
                summary.push({transaction, delayed, efficiency, slow, fast, average, office})
            }
            debugger
            return summary
        }
    },
    methods: {
        filterOnlyOffice (value, search, item) {
            return value != null &&
            search != null &&
            typeof value === 'string' &&
            value.toString().toLowerCase().indexOf(search) !== -1
        },
        formatDistance(seconds){
          return formatDistanceStrict(0, seconds * 1000)
        },
        groupBy (xs, key) {
            return xs.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {})
        }
    },
    mounted(){
       this.data
    }
}
</script>