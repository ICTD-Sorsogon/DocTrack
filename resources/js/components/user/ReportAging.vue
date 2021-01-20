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
                    :items-per-page="5"
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
                { text: 'Efficiency Rating', value: 'efficiency' },
            ],
        }
    },
    computed: {
        all_documents(){
            let alldocuments = JSON.parse(JSON.stringify(
                this.$store.state.documents.allDocuments));
            alldocuments.forEach(element => {
                element.delayed = 0;
                element.efficiency = 0;
                element.tracking_records.forEach(el => {
                    let created_at = new Date(el.created_at);
                    let now_date = new Date();
                    let difference = now_date.getDate() - created_at.getDate();
                    let total = element.tracking_records.length;
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
    mounted() {
        this.$store.dispatch('getDocument');
        console.log(this.all_documents)
        this.$store.dispatch('getAllUsers');
        this.$store.dispatch('unsetLoader');
    }
}
</script>