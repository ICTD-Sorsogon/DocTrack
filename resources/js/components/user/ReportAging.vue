<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <div v-if="auth_user.role_id == 1">
            <tracking-table :stats="data"/>
        </div>
        <div v-else>
            <office-table :stats="data"/>
        </div>
    </v-card>
</template>
<script>
import TrackingTable from './components/TrackingTable';
import OfficeTable from './components/OfficeTable';
import OfficeTableModal from './components/OfficeTableModal.vue';
import { mapState, mapGetters } from 'vuex';
import { groupBy, pluck, getRecordSpeed } from '../../helpers';
import { formatDistanceStrict } from 'date-fns';

export default {
    components:{
        TrackingTable,
        OfficeTable,
        OfficeTableModal
    },
    computed: {
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
        ...mapGetters(['offices','auth_user']),
        data(){
            let offices = pluck(this.offices, 'name')
            let summary = [];
            let record = groupBy(JSON.parse(JSON.stringify(Object.values(this.tracking_reports))), 'transaction_of')
            for(let i in record) {
                let transaction = record[i].length   
                let delayed = record[i].filter(r=>r.delayed).length
                let efficiency = ((transaction - delayed) / transaction * 100).toFixed(2) + '%'
                let average = formatDistanceStrict(0, record[i].reduce((counter,value,index)=>{return (counter*index+value.speed)/(index+1)},0)* 100); 
                let slow =  getRecordSpeed(record[i], 'slow')
                let fast =  getRecordSpeed(record[i], 'fast')
                let office = offices[i-1]
                summary.push({transaction, delayed, efficiency, slow, fast, average, office})
            }
            return summary
        }
    },
    mounted() {
        // console.log(this.auth_user);
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
    }
}
</script>