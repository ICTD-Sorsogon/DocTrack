<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <div v-if="auth_user.username == 'admin'">
            <tracking-table/>
        </div>
        <div v-else>
            <office-table/>
        </div>
    </v-card>
</template>
<script>
const week_in_milliseconds = 604800000;
import { delay_filter } from '../../constants';
import { max, min, mean } from 'lodash';
export default {
    components:{
        TrackingTable,
        OfficeTable,
        OfficeTableModal
    },
    computed: {
        summaries(){
            let summaries = JSON.parse(JSON.stringify(this.$store.state.documents.tracking_reports));
            let offices = JSON.parse(JSON.stringify(this.$store.state.offices.office_names));
            let data = [];
            for (const [key, value] of Object.entries(summaries)) {
                let created = value.filter(value => value.action === 'created');
                let acknowledged = value.filter(value => value.action === 'acknowledged');
                let received = value.filter(value => value.action === 'received');
                if (value.length === 1) offices[value[0].office_id].transactions++
                else {
                    if (created.length !== 0) {
                        offices[created[0].office_id].transactions+= created[0].document.destination.length;
                        if (acknowledged.length !== 0) {
                            offices[acknowledged[0].office_id].transactions += acknowledged[0].document.destination.length;
                            offices[acknowledged[0].office_id].transaction_speed.push(new Date(acknowledged[0].created_at).getTime() - new Date(created[0].created_at).getTime())
                            if (new Date(acknowledged[0].created_at).getTime() -
                                    new Date(created[0].created_at).getTime() > delay_filter[acknowledged[0].document.priority_level - 1]) offices[value[0].office_id].delayed += acknowledged[0].document.destination.length
                            if (received.length > 0) {
                                received.forEach(element => {
                                    offices[element.office_id].transactions++
                                    offices[element.office_id].transaction_speed.push(new Date(element.created_at).getTime() - new Date(acknowledged[0].created_at).getTime())
                                    if (new Date(element.created_at).getTime() -
                                        new Date(acknowledged[0].created_at).getTime() > delay_filter[acknowledged[0].document.priority_level - 1]) offices[acknowledged[0].office_id].delayed++
                                });
                            }
                        }
                    } else {
                        for (var i = 0; i < value.length; i++) {
                            if (i !== value.length - 1) {
                                if (new Date(value[i + 1].created_at).getTime() -
                                    new Date(value[i].created_at).getTime() > delay_filter[value[i].document.priority_level - 1]) offices[value[i].office_id].delayed++
                                offices[value[i].office_id].transaction_speed.push(new Date(value[i + 1].created_at).getTime() - new Date(value[i].created_at).getTime())
                                if (forwarded.length !== 0) {
                                    offices[acknowledged[0].office_id].transactions += 1;
                                }
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
        console.log(this.auth_user)
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('getOfficeNameList');
    }
}
</script>