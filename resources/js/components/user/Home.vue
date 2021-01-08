<template>
<div v-if="auth_user">
    <v-navigation-drawer app v-model="drawer">
        <template v-slot:prepend>
            <v-list-item two-line>
                <v-list-item-avatar>
                    <img :src="placeholderImage">
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-list-item-title
                                v-bind="attrs"
                                v-on="on"
                            >
                                {{auth_user_full_name}}
                            </v-list-item-title>
                        </template>
                        <span>{{auth_user_full_name}}</span>
                    </v-tooltip>
                    <v-list-item-subtitle>{{auth_user.username}}</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
        </template>

        <v-divider></v-divider>

        <v-list>

            <v-list-item link @click.prevent="getDashboard">
            <v-list-item-icon>
                <v-icon>mdi-view-dashboard-outline</v-icon>
            </v-list-item-icon>

            <v-list-item-content>
                <v-list-item-title>Dashboard</v-list-item-title>
            </v-list-item-content>
            </v-list-item>

            <v-list-group
                prepend-icon="mdi-file-document-multiple-outline"
                no-action
            >
                <template v-slot:activator>
                    <v-list-item-content>
                        <v-list-item-title>Document</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item link @click.prevent="getAllDocuments" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-book-search-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>View</v-list-item-title>
                </v-list-item>
                <v-list-item link @click.prevent="getNewDocumentRecordForm" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-file-document-edit-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Create</v-list-item-title>
                </v-list-item>
                <v-list-item link @click.prevent="getMasterListReport" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-email-receive-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Receive</v-list-item-title>
                </v-list-item>
                <v-list-item link @click.prevent="getMasterListReport" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-email-send-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Forward</v-list-item-title>
                </v-list-item>
                <v-list-item link @click.prevent="getMasterListReport" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-check-underline-circle-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Terminal</v-list-item-title>
                </v-list-item>
            </v-list-group>

            <v-list-group
                prepend-icon="mdi-timeline-check-outline"
                no-action
            >
                <template v-slot:activator>
                    <v-list-item-content>
                        <v-list-item-title>Reports</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item link @click.prevent="getAgingReport" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-timeline-clock-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Aging</v-list-item-title>
                </v-list-item>
                <v-list-item link @click.prevent="getMasterListReport" v-ripple="{ class: 'primary--text' }">
                    <v-list-item-icon>
                    <v-icon>mdi-timeline-text</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Master List</v-list-item-title>
                </v-list-item>
            </v-list-group>

            <v-list-item link @click.prevent="getAccountSettings">
                <v-list-item-icon>
                    <v-icon>mdi-account-cog-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                    <v-list-item-title>Account Settings</v-list-item-title>
                </v-list-item-content>
            </v-list-item>

            <v-list-item link @click.prevent="logout">
                <v-list-item-icon>
                    <v-icon>mdi-logout-variant</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                    <v-list-item-title>Logout</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <template v-slot:append>
            <div class="pa-2">
                <v-btn block @click.stop="drawer = !drawer">
                    <v-icon left>
                        mdi-close-circle-outline
                    </v-icon>
                    Hide
                </v-btn>
            </div>
        </template>
    </v-navigation-drawer>

    <v-app-bar
        app
        color="blue darken-3"
        dark
    >
        <v-app-bar-nav-icon class=".d-none .d-sm-flex .d-md-none" @click.stop="drawer = !drawer">
            <v-icon>mdi-menu</v-icon>
        </v-app-bar-nav-icon>
        <v-toolbar-title>{{currentRouteName}}</v-toolbar-title>
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
// TODO: Migrate to Vuex
import { mapGetters, mapActions } from "vuex";
export default {
    computed: {
        ...mapGetters(['auth_user', 'auth_user_full_name', 'page_loader']),
        currentRouteName() {
            return this.$route.name;
        },
        placeholderImage() {
            return `${this.image_source+(this.getRandomInt(0,2) == 1 ? 'men':'women')}/${this.getRandomInt(1, 100)}.jpg`;
        }
    },
    data() {
        return {
            drawer: true,
            group: null,
            image_source: 'https://randomuser.me/api/portraits/'
        }
    },
    methods: {
        ...mapActions(['removeAuthUser', 'unsetLoader']),
        logout(){
            this.removeAuthUser();
            this.$store.dispatch('unsetSnackbar');
            this.$store.dispatch('setLoader');
            sessionStorage.clear();
            this.$router.push({ name: "Login"});
        },
        getDashboard() {
            if(this.$route.name !== 'Dashboard') {
                this.$store.dispatch('setLoader');
                axios.get('/').then(()=>{
                    this.$router.push({ name: "Dashboard"});
                })
            }
        },
        getNewDocumentRecordForm() {
            if(this.$route.name !== 'New Document') {
                this.$store.dispatch('setLoader');
                axios.get('new_document').then(() => {
                    this.$router.push({ name: "New Document"});
                });
            }
        },

        getAllDocuments() {
            if(this.$route.name !== 'All Active Documents') {
                this.$store.dispatch('setLoader');
                axios.get('all_active_document').then(() => {
                    this.$router.push({ name: "All Active Documents"});
                });
            }
        },
        getAgingReport() {
            if(this.$route.name !== 'Document Aging Report') {
                this.$store.dispatch('setLoader');
                axios.get('reports/aging').then(()=>{
                    this.$router.push({ name: "Document Aging Report"});
                });
            }
        },
        getMasterListReport() {
            if(this.$route.name !== 'Document Master List') {
                this.$store.dispatch('setLoader');
                axios.get('reports/master_list').then(()=>{
                    this.$router.push({ name: "Document Master List"});
                });
            }
        },
        getAccountSettings() {
            if(this.$route.name !== 'Account Settings') {
                this.$store.dispatch('setLoader');
                axios.get('account_settings').then(()=>{
                    this.$router.push({ name: "Account Settings",  params: { user: this.user }});
                })
            }
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min) + min);
        }
    },
    mounted() {
        this.$store.dispatch('getOffices');
        this.$store.dispatch('getDocumentTypes');
        this.$store.dispatch('getAllUsers');
    }
}
</script>