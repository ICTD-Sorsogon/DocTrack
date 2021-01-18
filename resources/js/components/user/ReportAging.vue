<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <v-row>
            <!-- <v-col cols="12">
                  <v-data-table
                    :headers="headers"
                    :items="all_documents"
                    :items-per-page="5"
                    class="elevation-1"
                ></v-data-table>
            </v-col> -->
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
                { text: 'Delayed Document Code', value: '' },
                { text: 'Efficiency Rating', value: 'carbs' },
            ],
        }
    },
    computed: {
        ...mapGetters(['getDocument']),
        all_documents(){
            let alldocuments = this.$store.state.documents.allDocuments;
            alldocuments.array.forEach(element => {
                element.delayed = 0;
                element.trackingrecords.forEach(el => {
                    console.log(el);
                })
            });
            return alldocuments;
        }
    },
    mounted() {
        this.all_documents;
        this.$store.dispatch('getDocument');
        this.$store.dispatch('unsetLoader');
    }
}
</script>