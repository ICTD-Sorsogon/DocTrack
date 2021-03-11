<template>
    <v-tabs
        fixed-tabs
    >
    <v-tab>
        Office Reports
    </v-tab>
    <v-tab v-if="auth_user.role_id == 1">
        Other Offices
    </v-tab>
    <v-tab-item>
        <v-card flat>
        <v-card-title primary-title>
            Tracking Summary
        </v-card-title>
        <office-table :stats="data"/>
        </v-card>
    </v-tab-item>
    <v-tab-item>
        <v-card flat>
        <div v-if="auth_user.role_id == 1">
            <v-card-title primary-title>
                Tracking Table
            </v-card-title>
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
                                label="From"
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
                                label="To"
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
                                @click="filterTrackingReport()"
                            >Filter</v-btn>
                        </v-col>
                        <v-col cols="12" xl="6" lg="6" md="6" sm="6" xs="6">
                            <v-btn
                                block
                                color="primary"
                                outlined
                                @click="clearFilter()"
                            >Reset</v-btn>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <tracking-table :stats="data"/>
            <v-card-subtitle>
                * exclude created/received
            </v-card-subtitle>
        </div>
    </v-card>
    </v-tab-item>
    </v-tabs>
</template>
<script>
import TrackingTable from './components/TrackingTable';
import OfficeTable from './components/OfficeTable';
import OfficeTableModal from './components/OfficeTableModal.vue';
import { mapState, mapGetters, mapActions } from 'vuex';
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
        }
    },
    computed: {
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
        ...mapGetters(['offices','auth_user', 'office_reports_get']),
        ...mapActions(['officeReports']),
        data(){
            let offices = pluck(this.offices, 'name')
            let summary = {};
            let record = this.tracking_reports_data.length ? groupBy(this.tracking_reports_data, 'transaction_of') : false
            if (record) {
                for(let i in record) {
                    let transaction = record[i].length   
                    let delayed = record[i].filter(r=>r.delayed).length
                    console.log('delayed ',record[i].filter(r=>r.delayed))
                    let efficiency = ((transaction - delayed) / transaction * 100).toFixed(2) + '%'
                    let average = formatDistanceStrict(0, record[i].reduce((counter,value,index)=>{return (counter*index+value.speed)/(index+1)},0)* 1000); 
                    let slow =  getRecordSpeed(record[i], 'slow')
                    let fast =  getRecordSpeed(record[i], 'fast')
                    let office = {name: offices[i-1], id: i} //change this
                    summary[i] = {transaction, delayed, efficiency, slow, fast, average, office}
                } 
            }
            let officeReport = this.auth_user.office.office_code == "DO" ? groupBy(this.office_reports_get, 'touched_by') : { [this.auth_user.id]: [this.office_reports_get]}
            for (let n in officeReport) {
                summary[n] = {...summary[n], ...officeReport[n].reduce((accumulator , value) => {
                    accumulator.office = {name: offices[n-1], id: n}
                    if (value.action == 'created') {
                        accumulator.created +=1
                    } else if (value.action == 'acknowledged') {
                        accumulator.acknowledged +=1
                    } else if (value.action == 'forwarded') {
                        accumulator.forwarded +=1
                    } else if (value.action == 'received') {
                        accumulator.received +=1
                    }
                    return accumulator
                }, {'created': 0 , 'acknowledged': 0, 'forwarded': 0, 'received': 0, office: {}})}
            }
            return Object.values(summary)
        },
        tracking_reports_data: {
                get: function () {
                    return JSON.parse(JSON.stringify(Object.values(this.filterData)))
                },
                set : function (val) {
                    this.filterData = val;
                }
        },
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('officeReports');
        this.tracking_reports_data = this.tracking_reports;
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
                        message: 'Please input valid date!'
                    })
            }
        },
        bp(col){
            return breakpoint(col)
        },
    }
}
</script>