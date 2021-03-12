<template>
    <div>
        <v-row>
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
                            :max="filterDateTo"
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
                            :max="new Date().toISOString().slice(0,10)"
                            :min="filterDateFrom"
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
        <v-tabs
            full-width
            grow
            centered
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
                    <tracking-table :stats="data"/>
                    <v-card-subtitle>
                        * exclude created/received
                    </v-card-subtitle>
                </div>
            </v-card>
        </v-tab-item>
        </v-tabs>
    </div>
</template>
<script>
import TrackingTable from './components/TrackingTable';
import OfficeTable from './components/OfficeTable';
import OfficeTableModal from './components/OfficeTableModal.vue';
import { mapState, mapGetters, mapActions } from 'vuex';

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
            let summary = {}
            for(let record of this.office_reports_get){
                let i = record.transaction_of ?? record.touched_by
                summary[i] = summary[i] ?? {}
                summary[i]['transaction'] = (summary[i]['transaction'] || 0) + !!record.transaction_of
                summary[i]['delayed'] = (summary[i]['delayed'] || 0) + record.delayed
                summary[i]['efficiency'] =  ((summary[i]['transaction'] - summary[i]['delayed']) / summary[i]['transaction'] * 100).toFixed(2) + '%'
                summary[i]['created'] = (summary[i]['created'] || 0) + !!(record.action == 'created')
                summary[i]['acknowledged'] = (summary[i]['acknowledged'] || 0) + !!(record.action == 'acknowledged')
                summary[i]['received'] = (summary[i]['received'] || 0) + !!(record.action == 'received')
                summary[i]['forwarded'] = (summary[i]['forwarded'] || 0) + !!(record.action == 'forwarded')
                if(record.transaction_of){
                    summary[i]['fast'] = summary[i]['fast']?.speed < record.speed ? summary[i]['fast'] : record
                    summary[i]['slow'] = summary[i]['slow']?.speed < record.speed ? summary[i]['slow'] : record
                    summary[i]['sum'] = ((summary[i]['sum'] || 0) + record.speed) 
                    summary[i]['average'] = summary[i]['sum'] / summary[i]['transaction'] 
                }
                summary[i]['office'] = this.offices.find(office => office.id == i)
            }
            return Object.values(summary)
        },
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.clearFilter()
    },
    methods: {
        clearFilter() {
            this.filterDateTo = new Date().toISOString().slice(0,10)
            this.filterDateFrom = (new Date(new Date().setDate(new Date().getDate() - 30))).toISOString().slice(0,10)
            this.$store.dispatch('officeReports', {now : this.filterDateTo, from: null});
        },
        filterTrackingReport(){
            if (this.filterDateFrom != '' &&  this.filterDateTo != '') {
                let start = new Date(this.filterDateFrom + " 00:00:00").getTime();
                let end = new Date(this.filterDateTo + " 23:59:59").getTime();
                this.$store.dispatch('officeReports', {now : start, from: end});
            } else {
                this.$store.dispatch('setSnackbar', {
                        type: 'error',
                        message: 'Please input valid date!'
                    })
            }
        },
    }
}
</script>