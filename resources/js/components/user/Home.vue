<template>
<div v-if="auth_user.first_name">
    <v-navigation-drawer app dark v-model="drawer" id="sidebar" class="pt-2">
        <template v-slot:prepend>
        <v-img
            contain
            src="/images/provincial_logo.png"
            height="200px"
        />
        <v-list class="pb-0">
            <v-list-item class="namespace">
                <v-list-item-content
                    align="center"
                >
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-list-item-title
                                v-bind="attrs"
                                v-on="on"
                            >
                                {{userFullName}}
                            </v-list-item-title>
                        </template>
                        {{userFullName}}
                    </v-tooltip>
                    <v-list-item-subtitle> {{auth_user.username}} </v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        </template>
        <v-divider/>
        <v-list>
            <v-list-item
                :input-value="$route.name === 'All Active Documents' ? true:false"
                link
                @click.prevent="getAllDocuments"
            >
                <v-list-item-icon>
                    <v-icon>mdi-file-document-multiple-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Document</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-group
                prepend-icon="mdi-timeline-check-outline"
                no-action
                color="white"
                :value="submenuToggle"
                mandatory
            >
                <template v-slot:activator>
                    <v-list-item-content>
                        <v-list-item-title>Reports</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item
                    :input-value="$route.name === 'Document Aging Report' ? true:false"
                    link
                    @click.prevent="getAgingReport"
                    v-ripple="{ class: 'white--text' }"
                >
                    <v-list-item-icon>
                        <v-icon>mdi-timeline-clock-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Tracking</v-list-item-title>
                </v-list-item>
                <v-list-item
                    :input-value="$route.name === 'Document Master List' ? true:false"
                    link @click.prevent="getMasterListReport"
                    v-ripple="{ class: 'white--text' }"
                >
                    <v-list-item-icon>
                        <v-icon>mdi-timeline-text</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Master List</v-list-item-title>
                </v-list-item>
                <v-list-item
                    :input-value="$route.name === 'Office List' ? true:false"
                    link
                    @click.prevent="getOfficeListReport"
                    v-ripple="{ class: 'white--text' }"
                >
                    <v-list-item-icon>
                    <v-icon>mdi-office-building</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Offices</v-list-item-title>
                </v-list-item>
                <v-list-item
                    v-if="auth_user.role_id === 1"
                    :input-value="$route.name === 'Log Report' ? true:false"
                    link
                    @click.prevent="getLogs"
                    v-ripple="{ class: 'white--text' }"
                >
                    <v-list-item-icon>
                    <v-icon>mdi-timeline-clock-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Logs</v-list-item-title>
                </v-list-item>
            </v-list-group>
            <v-list-item
                :input-value="$route.name === 'Account Settings' ? true:false"
                link @click.prevent="getAccountSettings">
                <v-list-item-icon>
                    <v-icon>mdi-account-cog-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Account Settings</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item
                :input-value="$route.name === 'User Management' ? true:false"
                v-if="auth_user.role_id === 1"
                link
                @click.prevent="getUserManagement"
            >
                <v-list-item-icon>
                    <v-icon>mdi-account-supervisor-circle</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>User Management</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <template v-slot:append>
            <div class="pa-2">
                <!-- <v-btn
                    block
                    @click.prevent="logout"
                    dark
                    outlined
                >
                    <v-icon left>
                        mdi-logout-variant
                    </v-icon>
                    Logout
                </v-btn> -->
                <logout-dialog @trigger-logout="logout"></logout-dialog>
            </div>
        </template>
    </v-navigation-drawer>
    <v-app-bar
        app
        color="#0675BB"
        dark
    >
        <v-app-bar-nav-icon class=".d-none .d-sm-flex .d-md-none" @click.stop="drawer = !drawer">
            <v-icon>mdi-menu</v-icon>
        </v-app-bar-nav-icon>
        <v-toolbar-title>{{currentRouteName}}</v-toolbar-title>
        <v-spacer></v-spacer>

        <!-- NOTIFICATION!! -->
        <div v-if="notif" class="outside" v-on:click="away()"></div>
        <notification-item style="margin-top:580px; margin-left:200px" v-if="notif"></notification-item>
        <notification v-on:showNotif="showNotif" style="margin-right:15px"></notification>

        <router-link to="/account_settings">
            <v-avatar v-if="image_source === '/storage/null'" color="indigo">
                <img src="/images/defaultpic.jpg" alt="default_picture">
            </v-avatar>

            <v-avatar v-else>
                <img :src="image_source" alt="profile_picture">
            </v-avatar>
        </router-link>

        <v-progress-linear
            :active="page_loader"
            color="#A83F39"
            height="8"
            indeterminate
            striped
            absolute
            bottom
        />
    </v-app-bar>
    <v-main fluid>
        <v-container>
            <v-scroll-x-transition mode="out-in" :hide-on-leave="Boolean(true)">
                <router-view/>
            </v-scroll-x-transition>
        </v-container>
    </v-main>
</div>
</template>

<script>
import Notification from './Notification'
import NotificationItem from './NotificationItem'
// TODO: Directly modify State through Mutation in Setting and Unsetting loaders instead of adding Actions
import { mapGetters, mapActions } from "vuex";
import LogoutDialog from './components/LogoutDialog';
export default {
    components:{
        Notification,
        NotificationItem,
        LogoutDialog
    },
    computed: {
        ...mapGetters(['auth_user', 'page_loader']),
        currentRouteName() {
            if (this.$route.params.type){
                let docTypes = this.$route.params?.type.replace(/\w/, val=>val.toUpperCase())
                return `${docTypes} Documents ${docTypes =='Terminal' ? 'Track' : ''}`
            }

            return this.$route.name;
        },
        userFullName(){
            return this.$store.getters.auth_user_full_name;
        },
        submenuToggle() {
            return this.$store.state.loader.submenu_opened;
        },
        image_source(){
            return "/" + this.auth_user.avatar || ''
        }

    },
    data() {
        return {
            drawer: null,
            group: null,
            messages: 5,
            notif: false,
        }
    },
    methods: {
        ...mapActions(['removeAuthUser', 'unsetLoader']),

        showNotif(){
            this.notif = !this.notif
        },

        away() {
            this.notif = !this.notif;
        },

        logout(){
            var path = this.$router.currentRoute.path.split('/');
            if (path.length >= 3) {
                this.$router.push({ name: "All Active Documents"});
                this.removeAuthUser()
                this.$store.dispatch('unsetSnackbar');
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', false);
                sessionStorage.clear();
                this.$router.push({ name: "Login"});
            } else {
                this.removeAuthUser()
                this.$store.dispatch('unsetSnackbar');
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', false);
                sessionStorage.clear();
                this.$router.push({ name: "Login"});
            }
        },
        getAllDocuments() {
            if(this.$route.name !== 'All Active Documents') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', false);
                this.$router.push({ name: "All Active Documents"});
            }
        },
        getAgingReport() {
            if(this.$route.name !== 'Document Aging Report') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', true);
                this.$router.push({ name: "Document Aging Report"});
            }
        },
        getLogs() {
            if(this.$route.name !== 'Log Report') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', true);
                this.$router.push({ name: "Log Report"});
            }
        },
        getMasterListReport() {
            if(this.$route.name !== 'Document Master List') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', true);
                this.$router.push({ name: "Document Master List"});
            }
        },
        getOfficeListReport() {
            if(this.$route.name !== 'Office List') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', true);
                this.$router.push({ name: "Office List"});
            }
        },
        getAccountSettings() {
            if(this.$route.name !== 'Account Settings') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', false);
                this.$router.push({ name: "Account Settings",  params: { user: this.user }});
            }
        },
        getUserManagement() {
            if(this.$route.name !== 'User Management') {
                this.$store.dispatch('setLoader');
                this.$store.commit('TOGGLE_SUBMENU', false);
                this.$router.push({ name: "User Management",  params: { user: this.user }});
            }
        },
    },
    created(){
        Echo.channel('documents'+this.auth_user.office_id)
        .listen('DocumentEvent', (e) => {
            this.$store.dispatch('getActiveDocuments');
            this.$store.dispatch('getNotifs');
        })
    },
    beforeCreate() {
        this.$store.dispatch('getOffices')
        this.$store.dispatch('getDocumentTypes')
        this.$store.dispatch("getActiveDocuments")

    },
}
</script>

<style>
#sidebar {
    background-image: linear-gradient(180deg, #0675BB, #F72e2E);
}
.namespace {
    background:rgba(0, 0, 0, 0.09);
}
.outside {
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0px;
  left: 0px;
}
</style>