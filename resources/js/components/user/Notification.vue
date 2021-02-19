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
            let newNotif = JSON.parse(JSON.stringify(this.$store.state.users.notifs))
            let count = 0
            newNotif.forEach(notif => {
                if(notif.badge == 0){
                    count++
                }
            });
            return count
          },
    },
    methods:{
      showNotif(item){
          this.$store.dispatch('seenBadge', {user_id: this.$store.state.users.user.id, status: 1});

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