<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
        <div v-if="auth_user.username == 'admin'">
            <tracking-table/>
        </div>
        <div v-else>
            <office-table/>
        </div>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';
import TrackingTable from './components/TrackingTable';
import OfficeTable from './components/OfficeTable';
import OfficeTableModal from './components/OfficeTableModal.vue';
export default {
    components:{
        TrackingTable,
        OfficeTable,
        OfficeTableModal
    },
    computed: {
        ...mapGetters(['auth_user']),
    },
    mounted() {
        console.log(this.auth_user)
        this.$store.dispatch('unsetLoader');
        this.$store.dispatch('documentReports');
        this.$store.dispatch('getOfficeNameList');
    }
}
</script>