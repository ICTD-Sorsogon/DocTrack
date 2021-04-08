<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <div v-if="auth_user.role_id == 1">
            <v-row
            >
                <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="12" >
                    <v-dialog
                        ref="dialog"
                        v-model="filterDateDialogFrom"
                        :return-value.sync="filterDateFrom"
                        persistent
                        width="290px"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="filterDateFrom"
                                label="FROM"
                                prepend-icon=""
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                outlined
                                dense
                            />
                        </template>
                        <v-date-picker
                            v-model="filterDateFrom"
                            scrollable
                            
                        >
                            <v-spacer/>
                            <v-btn text color="primary" @click="filterDateDialogFrom = false"> Cancel </v-btn>
                            <v-btn text color="primary" @click="$refs.dialog.save(filterDateFrom);"> OK </v-btn>
                        </v-date-picker>
                    </v-dialog>
                </v-col>
                <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="12">
                    <v-dialog
                        ref="dialog1"
                        v-model="filterDateDialogTo"
                        :return-value.sync="filterDateTo"
                        persistent
                        width="290px"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="filterDateTo"
                                label="TO"
                                prepend-icon=""
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                
                                outlined
                                dense
                            />
                        </template>
                        <v-date-picker
                            v-model="filterDateTo"
                            scrollable
                            
                        >
                            <v-spacer/>
                            <v-btn text color="primary" @click="filterDateDialogTo = false"> Cancel </v-btn>
                            <v-btn
                                text
                                color="primary"
                                @click="$refs.dialog1.save(filterDateTo);"
                            > OK </v-btn>
                        </v-date-picker>
                    </v-dialog>
                </v-col>
                <v-col cols="12" xl="12" lg="4" md="4" sm="12" xs="12">
                    <v-row>
                        <v-col  cols="12" xl="6" lg="6" md="6" sm="6" xs="6">
                            <v-btn
                                block
                                color="primary"
                                outlined
                                v-if="filterDateFrom != '' && filterDateTo != ''"
                                @click="filterTrackingReport()"
                            >Filter</v-btn>
                        </v-col>
                        <v-col cols="12" xl="6" lg="6" md="6" sm="6" xs="6">
                            <v-btn
                                block
                                color="primary"
                                outlined
                                v-if="filterDateFrom != '' && filterDateTo != ''"
                                @click="clearFilter()"
                            >Reset</v-btn>
                        </v-col>
                    </v-row>
                </v-col>
                <tracking-table :stats="data"/>
            </v-row>
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
    data() {
        return {
            filterDateDialogFrom: false,
            filterDateFrom: '',
            filterDateDialogTo: false,
            filterDateTo: '',
            filterData: [],
            btnFilter : false,
        }
    },
    computed: {
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
        ...mapGetters(['offices','auth_user']),
        data(){
            let offices = pluck(this.offices, 'name')
            let summary = [];
            let record = groupBy(this.tracking_reports_data, 'transaction_of')
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
        },
        tracking_reports_data: {
                get: function () {
                    return JSON.parse(JSON.stringify(Object.values(this.filterData)))
                },
                set : function (val) {
                    this.filterData = val;
                }
        }
    },
    mounted() {
        this.filterData = this.tracking_reports;
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
    },
    methods: {
        clearFilter() {
            this.filterDateFrom = this.filterDateTo = '';
            this.tracking_reports_data = this.tracking_reports;
        },
        filterTrackingReport(){
            if (this.filterDateFrom != '' &&  this.filterDateTo != '') {
                let start = new Date(this.filterDateFrom + " 00:00:00").getTime();
                let end = new Date(this.filterDateTo + " 23:59:59").getTime();
                let filter = this.tracking_reports_data.filter(item => {
                    return new Date(item.last_touched).getTime() >= start &&
                    new Date(item.last_touched).getTime() <= end;
                });
                this.tracking_reports_data = filter;
            } else {
                this.$store.dispatch('setSnackbar', {
                        type: 'error',
                        message: 'Invalid date'
                    })
            }
        },
        bp(col){
            return breakpoint(col)
        },
    }
}
</script>