<template>
        <v-row dense>
            <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" dark outlined color="#03A9F4">
                <v-card-title class="headline">
                    <v-icon large left>mdi-swap-horizontal-circle-outline</v-icon>
                        All Transaction
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="this.office==''">
                    <v-row>
                        <v-col class="display-2">
                        No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row>
                        <v-col  class="display-2">
                        {{office.transaction ? office.transaction : 'No'}} Documents
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            </v-col>
            <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined dark color="#EF5350">
                <v-card-title class="headline">
                    <v-icon large left>mdi-clock-alert-outline</v-icon>
                        Delayed Documents
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="this.office==''">
                    <v-row>
                        <v-col class="display-2">
                        No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row>
                        <v-col class="display-2">
                        {{ office.delayed || 0}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            </v-col>
            <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined dark color="#43A047">
                <v-card-title class="headline">
                    <v-icon large left>mdi-clock-fast</v-icon>
                        Fastest Transaction
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="this.office==''">
                    <v-row>
                        <v-col class="display-2">
                        No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row>
                        <v-col class="display-2">
                        {{(office.fast || {}).fast || 0}}
                        </v-col>
                    </v-row>
                </v-card-text>
                <div>
                <v-card-text v-if="this.office==''">
                    <v-row >
                        <v-col>
                            No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row >
                        <v-col>
                            Action: {{office.fast ? office.fast.action.replace(/\w/, val=>val.toUpperCase()) : 'None' }} <br>
                            Subject: {{office.fast ? office.fast.document.subject : 'None'}} <br>
                            Tracking Code: {{office.fast? office.fast.document.tracking_code : 'None'}}
                        </v-col>
                    </v-row>
                </v-card-text>
                </div>
            </v-card>
            </v-col>
            <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined dark color="#FB8C00">
                <v-card-title class="headline">
                    <v-icon large left>mdi-snail</v-icon>
                        Slowest Transaction
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="this.office==''">
                    <v-row>
                        <v-col class="display-2">
                            No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row>
                        <v-col class="display-2">
                        {{(office.slow || {}).slow || 0}}
                        </v-col>
                    </v-row>
                </v-card-text>
                <div>
                <v-card-text v-if="this.office==''">
                    <v-row >
                        <v-col>
                            No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row >
                        <v-col>
                            Action: {{office.slow ? office.slow .action.replace(/\w/, val=>val.toUpperCase()) : 'None'}} <br>
                            Subject: {{office.slow ? office.slow.document.subject : 'None'}} <br>
                            Tracking Code: {{office.slow ? office.slow.document.tracking_code : 'None'}}
                        </v-col>
                    </v-row>
                </v-card-text>
                </div>
            </v-card>
            </v-col>
            <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined dark color="#AB47BC">
                <v-card-title class="headline">
                    <v-icon large left>mdi-speedometer-medium</v-icon>
                        Average Transaction Speed
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="this.office==''">
                    <v-row>
                        <v-col class="display-2">
                        No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row>
                        <v-col class="display-2">
                        {{office.average}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            </v-col>
            <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined dark color="#EC407A">
                <v-card-title class="headline">
                    <v-icon large left>mdi-cog-clockwise</v-icon>
                        Efficiency Rating
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="this.office==''">
                    <v-row>
                        <v-col class="display-2">
                        No Data Available
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-text v-else>
                    <v-row>
                        <v-col class="display-2">
                        {{office.efficiency}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            </v-col>
        </v-row>
</template>
<script>
import { mapGetters, mapState } from 'vuex';
export default {
    props: ['stats'],
    computed: {
        ...mapGetters(['auth_user']),
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
        office(){
            return this.stats.find(stat=> stat.office.id == this.auth_user.office_id ?? false);
        }
    },
    mounted() {
        console.log(this.office)
    }
}
</script>
