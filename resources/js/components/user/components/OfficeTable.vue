<template>
    <v-row>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Created</v-card-title>
            <v-card-text>
                {{office.created}}
            </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Received</v-card-title>
            <v-card-text>
                {{office.received}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Forwarded</v-card-title>
            <v-card-text>
                {{office.forwarded}}
            </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" xl="6" lg="6" md="6" sm="12" xs="12">
            <v-card
                elevation="2"
                outlined
            >
            <v-card-title>Transaction</v-card-title>
            <v-card-text>
                {{office.transaction}}
            </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
    computed: {
        ...mapGetters(['auth_user']),
        office(){
            let data = [];
            let reports = JSON.parse(JSON.stringify(this.$store.getters.office_reports_get));
            let created = 0, received = 0, forwarded = 0, transaction = 0;
            for (const [key, value] of Object.entries(reports)) {
                value.action === 'created' ? created++ : 0;
                value.action === 'received' ? received++ : 0;
                value.action === 'forwarded' ? forwarded++ : 0;
            }
            return {
                created:created,
                received:received,
                forwarded:forwarded,
                transaction: created + received + forwarded
            };
        }
    },
    mounted() {
        console.log(JSON.parse(JSON.stringify(this.$store.getters.office_reports_get)));
    }
}
</script>
