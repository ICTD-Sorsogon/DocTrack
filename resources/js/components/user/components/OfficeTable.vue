<template>
    <v-row>

        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>All Transaction</v-card-title>
            <v-card-text>
                {{office.transactions}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Delayed Documents</v-card-title>
            <v-card-text>
                {{office.delayed}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Fastest Transaction</v-card-title>
            <v-card-text>
                {{office.fastest}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Slowest Transaction</v-card-title>
            <v-card-text>
                {{office.slowest}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Average Transaction Speed</v-card-title>
            <v-card-text>
                {{office.average}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Efficiency Rating</v-card-title>
            <v-card-text>
                {{office.efficiency}}
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
