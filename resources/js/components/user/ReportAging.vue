<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <v-row>
            <v-col cols="12">
                  <v-data-table
                    :items="all_documents"
                    :items-per-page="5"
                    class="elevation-1"
                ></v-data-table>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    data() {
        return {
            headers: [
                {
                    text: 'Office',
                    align: 'start',
                    value: 'name',
                },
                { text: 'All Transaction', value: 'trackingrecords.length' },
                { text: 'Delayed Document Code', value: 'delayed' },
                { text: 'Efficiency Rating', value: 'effiency' },
            ],
        }
    },
    computed: {
        ...mapGetters(['getDocument']),
        all_documents(){
            let alldocuments = this.$store.state.documents.allDocuments;
            alldocuments.forEach(element => {
                element.delayed = 0;
                element.effiency = 0;
                element.tracklength = 0;
                element.trackingrecords.forEach(el => {
                    var last_touched = new Date(el.last_touched);
                    var now = new Date(el.created_at);
                    var delay = now.getDate() - last_touched.getDate();
                    if (delay > 7) element.delayed +=1;
                    element.tracklength = element.trackingrecords.length;
                    element.effiency = ((element.tracklength - element.delayed)
                    / element.tracklength) * 100;
                })
            });
            return alldocuments;
        }
    },
    mounted() {
        this.$store.dispatch('getDocument');
        this.$store.dispatch('unsetLoader');
    }
}
</script>