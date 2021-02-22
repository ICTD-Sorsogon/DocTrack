<template>
  <div>
        <v-badge
          :content="notifs"
          :value="notifs"
          color="green"
          overlap
        >
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
    computed:{
        ...mapGetters(["auth_user"]),
        notifs() {
            return this.$store.state.users.notif.reduce((counter, notif)=>{ counter += notif.badge; return counter },0)
          },
    },
    methods:{
      showNotif(){
          this.$store.dispatch('seenBadge', {badge_data: this.$store.state.users.notifs});

          this.$emit('showNotif')
      },
    },
    mounted(){
        Echo.channel('documents'+this.auth_user.office_id)
        .listen('DocumentEvent', (e) => {
            this.$store.dispatch('getActiveDocuments');
            this.$store.dispatch('getNotifs');
        })

        this.$store.dispatch('getNotifs');
    }
  }
</script>

<style>

</style>