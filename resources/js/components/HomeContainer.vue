<template>
    <div>
        <div v-if="auth_user">
            <div>
                <user-home-component></user-home-component>
            </div>
        </div>
        <v-scroll-x-transition>
            <v-snackbar
                :timeout="5000"
                v-model="snackbar.showing"
                :vertical="true"
                :color="snackbar.color"
                right
                top
            >
                <v-alert
                    dense
                    prominent
                    color="transparent"
                    :icon="snackbar.icon"
                >
                    {{snackbar.text}}
                </v-alert>
                <template v-slot:action="{ attrs }">
                    <v-btn
                        text
                        v-bind="attrs"
                        @click="closeSnackbar"
                    >
                        Close
                    </v-btn>
                </template>
            </v-snackbar>
        </v-scroll-x-transition>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import UserHomeComponent from './user/Home';
export default {
    name: "HomeContainer",
    components: {
        UserHomeComponent
    },
    computed: {
        ...mapGetters(["auth_user"]),
        snackbar(){
            var snackbar = this.$store.state.snackbars.snackbar;
            if(!snackbar.showing){
                this.$store.dispatch('unsetSnackbar');
            }
            return snackbar
        }
    },
    methods: {
        closeSnackbar(){
            this.$store.dispatch('unsetSnackbar');
        }
    },
    mounted() {
        this.$store.dispatch('getAuthUser');
    }
}
</script>