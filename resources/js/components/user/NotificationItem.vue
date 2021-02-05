<template>
  <v-card
        class="elevation-5"
        max-width="550"
        style="height:500px; overflow-y: auto;"
        color="#F2F4F8"
    >
        <v-list three-line color="#E1F3FC">
        <template v-for="(item, index) in notifs">
            <div :key="index" @click="seen(index, item)" style="cursor:pointer;" class="notif_item"> 
                <v-divider
                style="background-color:#E1E1E1"
                :inset="item.inset"
                ></v-divider>

                <v-list-item
                :class="highlight(item.status)"
                ripple
                >
                <v-list-item-avatar class="elevation-3" v-if="item.avatar != null">
                    <v-img :src="item.avatar"></v-img>
                </v-list-item-avatar>
                <v-list-item-avatar class="elevation-5" v-else>
                    <v-img :src="default_image"></v-img>
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title style="color:black;" v-html="item.sender_name"></v-list-item-title>
                    <v-list-item-subtitle style="color:black" v-html="item.message"></v-list-item-subtitle>
                    <h6 style="font-weight:50; color: gray" v-html="item.created_at"></h6>
                </v-list-item-content>
                </v-list-item>
            </div>
        </template>
        </v-list>
    </v-card>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    data(){
        return{
            default_image:'/images/defaultpic.jpg',
            items: [
                // { created_at: 'Today' },
                // {
                // avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg',
                // name: 'Brunch this weekend?',
                // message: `<span class="text--primary">Ali Connors</span> &mdash; I'll be in your neighborhood doing errands this weekend. Do you want to hang out?`,
                // },
            ],
        }
    },
    computed:{
        notifs(){
            let newNotif = JSON.parse(JSON.stringify(this.$store.state.users.notifs))
            let allUsers = JSON.parse(JSON.stringify(this.$store.state.users.all_users))
            newNotif.forEach(notif => {
                allUsers.forEach(user => {
                    if(notif.user_id == user.id){
                        // notif.name  = (user.first_name + ' ' + user.middle_name + ' ' + user.last_name + ' ' + (user.suffix ?? '')).trim()
                        notif.avatar  =  user.avatar
                    }
                });
                
            });
            return  newNotif
        }
    },
    mounted(){
        this.$store.dispatch('getNotifs');
        this.$store.dispatch('getAllUsers');
    },
    methods:{
        highlight(isHighlighted){
            if(isHighlighted){
                return 'highlight'
            }
        },
        seen(index, item){
            if(item.status == 0){
                this.$store.dispatch('seenNotif', {id: item.id, status: 1});
            }
        },
    }
}
</script>

<style scoped>
    .highlight{
        background-color: #F2F4F8;
    }
    .notif_item:hover{
        opacity: 0.7;
        background-color: #E1F3FC;
    }
</style>