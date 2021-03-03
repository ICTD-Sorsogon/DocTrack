<template>
    <v-row>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined color="#42ADC7">
                <v-card-title>
                    <v-icon large left>mdi-swap-horizontal-circle-outline</v-icon>
                    <span class="title font-weight-light" >
                        All Transaction
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row align="center">
                        <v-col class="display-3" cols="6">
                        {{office.transactions}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined color="#FF5347">
                <v-card-title>
                    <v-icon large left>mdi-clock-alert-outline</v-icon>
                    <span class="title font-weight-light" >
                        Delayed Documents
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row align="center">
                        <v-col class="display-3" cols="6">
                        {{office.delayed}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined color="#81D152">
                <v-card-title>
                    <v-icon large left>mdi-clock-fast</v-icon>
                    <span class="title font-weight-light" >
                        Fastest Transaction
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row align="center">
                        <v-col class="display-3" cols="6">
                        {{office.fastest}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined color="#5B4DB7">
                <v-card-title>
                    <v-icon large left>mdi-snail</v-icon>
                    <span class="title font-weight-light" >
                        Slowest Transaction
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row align="center">
                        <v-col class="display-3" cols="6">
                        {{office.slowest}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined color="#FF9D4F">
                <v-card-title>
                    <v-icon large left>mdi-speedometer-medium</v-icon>
                    <span class="title font-weight-light" >
                        Average Transaction Speed
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row align="center">
                        <v-col class="display-3" cols="6">
                        {{office.average}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card elevation="2" outlined color="#F5F263">
                <v-card-title>
                     <v-icon large left>mdi-cog-clockwise</v-icon>
                    <span class="title font-weight-light" >
                        Efficiency Rating
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row align="center">
                        <v-col class="display-3" cols="6">
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
    computed: {
        ...mapGetters(['auth_user']),
        ...mapState({'tracking_reports': state => state.documents.tracking_reports}),
        office(){
            return this.tracking_reports
        }
    },
    mounted() {
        console.log(JSON.parse(JSON.stringify(this.$store.getters.office_reports_get)));
    }
}
</script>
