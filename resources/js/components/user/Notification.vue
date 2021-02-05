<template>
<div>

    <!-- <v-row style="margin-right: 20px"> -->
      <!-- <notification-drawer></notification-drawer> -->
    <!-- </v-row> -->
    <!-- <v-row style="margin-top:500px; margin-right:10px" v-if="notif"> -->
      <!-- <notification-item v-if="notif"></notification-item> -->
    <!-- </v-row> -->
      <v-badge
        :content="notifs"
        :value="notifs"
        color="green"
        overlap
      >
        <!-- <v-icon large @click="showNotif" style="cursor:pointer;">
            mdi-bell
        </v-icon> -->

        <v-icon large @click="showNotif" style="cursor:pointer;">
            mdi-bell
        </v-icon>
      </v-badge>

</div>



</template>

<script>
import { mapGetters, mapActions } from "vuex";
import NotificationItem from './NotificationItem'
  export default {
    components:{
        NotificationItem,
    },
    data () {
        return {
            // show: false,
            // notif: true,
        }
    },
    computed:{
        ...mapGetters(["auth_user"]),
        notifs(){
            let newNotif = JSON.parse(JSON.stringify(this.$store.state.users.notifs))
            let count = 0
            newNotif.forEach(notif => {
                if(notif.status == 0){
                    count++
                }
            });
            return count
        }
    },
    methods:{
        showNotif(event){
          this.$emit('showNotif')
        },
    },
    mounted(){
        Echo.channel('documents'+this.auth_user.office_id)
        .listen('DocumentEvent', (e) => {
            this.$store.dispatch('getNotifs');
            // this.$store.dispatch("setSnackbar", {type:'success', message:'Test Snackbar'});
        })

        this.$store.dispatch('getNotifs');
    }
  }
</script>

<style>

</style>