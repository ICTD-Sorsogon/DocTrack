<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <v-row>
            <v-col cols="12">
                  <v-data-table
                    :search="search"
                    :items="all_documents"
                    :items-per-page="10"
                    :headers="headers"
                    class="elevation-1"
                >
                <template v-slot:top>
                    <v-text-field
                        v-model="search"
                        label="Search"
                        class="mx-4"
                    />
                </template>
                <template v-slot:[`item.efficiency`] = "{ item }">
                    {{item.efficiency}}%
                </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    data() {
        return {
            search: '',
            headers: [
                {
                    text: 'Office',
                    align: 'start',
                    value: 'name',
                },
                { text: 'All Transaction', value: 'tracking_records.length' },
                { text: 'Delayed Document', value: 'delayed' },
                { text: 'Fastest Transaction', value: 'fastestTransaction' },
                { text: 'Slowest Transaction', value: 'longestDelay' },
                { text: 'Efficiency Rating', value: 'efficiency' },
            ],
        }
    },
    computed: {
        ...mapGetters(['getDocument']),
        //TOFIX elapse time for fastest and longest transaction should check
        // in b/w transaction by checking previous last_touched
        all_documents(){
            let alldocuments = JSON.parse(JSON.stringify(
                this.$store.state.documents.allDocuments));
            alldocuments.forEach(element => {
                var max = 0;
                var min = (element.tracking_records.length === 0 ? 0 : Number.POSITIVE_INFINITY);
                element.delayed = 0;
                element.efficiency = 0;
                element.longestDelay = '';
                element.fastestTransaction  = '';
                element.tracking_records.forEach(el => {
                    let created_at = new Date(el.created_at);
                    let now_date = new Date();
                    let difference = now_date.getDate() - created_at.getDate();
                    let total = element.tracking_records.length;
                    if (max < difference ) {
                        max = difference ;
                        element.longestDelay = this.getTimeHours(created_at, now_date);
                    }
                    if (min > difference ) {
                        min = difference;
                        element.fastestTransaction = this.getTimeHours(created_at, now_date)
                    }

                    if (difference > 7) {
                        element.delayed += 1;

                    }
                     element.efficiency = (((total - element.delayed )
                     / total) * 100);
                });
            });
            return alldocuments;
        }
    },
    methods: {
        getTimeHours(past, present) {
            var present = new Date(present);
            var past = new Date(past);
            var delta = Math.abs(present - past) / 1000;
            var days = Math.floor(delta / 86400);
            delta -= days * 86400;
            var hours = Math.floor(delta / 3600) % 24;
            delta -= hours * 3600;
            var minutes = Math.floor(delta / 60) % 60;
            delta -= minutes * 60;
            var seconds = delta % 60;
            return days +"d " + hours + "h " + minutes + "m";
        }
    },
    mounted() {
        this.$store.dispatch('getDocument');
        console.log(this.all_documents)
        this.$store.dispatch('getAllUsers');
        this.$store.dispatch('unsetLoader');
    }
}
</script>