<template>
    <div>
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
                    <v-list-item-avatar class="elevation-3" >
                        <v-img :src="item.action == 'Reminder' ? baseURL + 'images/reminder.png' : (baseURL + item.avatar || baseURL + default_image)"></v-img>
                    </v-list-item-avatar>

                    <v-list-item-content>
                        <v-list-item-title style="color:black;" v-html="item.action == 'Reminder' ? '<span style=color:red>REMINDER</span>' : item.office_code"></v-list-item-title>
                        <v-list-item-subtitle style="color:black" v-html="item.message"></v-list-item-subtitle>
                        <h6 style="font-weight:50; color: gray" v-html="item.created_at"></h6>
                    </v-list-item-content>
                    </v-list-item>
                </div>
            </template>
            </v-list>
        </v-card>
    <table-modal @closeDialog="closeDialog" :dialog="dialog" v-if="Object.keys(selected_document).length" :selected_document="selected_document"></table-modal>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import TableModal from './components/TableModal'
export default {
    components:{
        TableModal,
    },
    data(){
        return{
            dialog: false,
            default_image:'images/defaultpic.jpg',
            items: [],
            selected_document: {},
            baseURL: window.location.origin + '/'
        }
    },
    computed:{
        ...mapGetters(['documents','auth_user']),
        notifs(){
            return JSON.parse(JSON.stringify(this.$store.state.users.notifs))
        }
    },
    mounted(){
        this.$store.dispatch('getNotifs');
        this.$store.dispatch('getAllUsers');
    },
    methods:{
        closeDialog(){
            this.dialog = false
        },
        highlight(isHighlighted){
            if(isHighlighted){
                return 'highlight'
            }
        },
        seen(index, item){
            if(item.status == 0){
                this.$store.dispatch('seenNotif', {id: item.id, status: 1});
            }

            this.selected_document = Object.values(JSON.parse(JSON.stringify(this.documents))).flat().find(document => {
                return document.id == item.document_id
            }) ?? {}

            this.dialog = !!this.selected_document
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
