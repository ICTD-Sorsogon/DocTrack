<template>
    <v-app>
        <div v-if="auth_user">
            <user-home-component></user-home-component>
        </div>
        <v-scroll-x-transition>
            <v-snackbar
                :timeout="30000"
                v-model="snackbar.showing"
                :vertical="true"
                :color="snackbar.color"
                right
                top
            >
                <v-alert prominent color="transparent" :icon="snackbar.icon">
                    <div class="title">
                        {{ snackbar.title }}
                    </div>
                    <div
                        v-if="
                            typeof snackbar.text === 'object' &&
                                snackbar.text !== null
                        "
                    >
                        <div
                            v-for="(text, index) in snackbar.text"
                            :key="index"
                        >
                            <ul>
                                <li>{{ text[0] }}</li>
                            </ul>
                        </div>
                    </div>
                    <div v-else>
                        {{ snackbar.text }}
                    </div>
                </v-alert>
                <template v-slot:action="{ attrs }">
                    <v-btn text v-bind="attrs" @click="closeSnackbar">
                        Close
                    </v-btn>
                </template>
            </v-snackbar>
        </v-scroll-x-transition>
    </v-app>
</template>

<script>
import { mapGetters } from "vuex";
import UserHomeComponent from "./user/Home";
export default {
    name: "HomeContainer",
    components: {
        UserHomeComponent
    },
    computed: {
        ...mapGetters(["auth_user"]),
        snackbar() {
            var snackbar = this.$store.state.snackbars.snackbar;
            if (!snackbar.showing) {
                this.$store.dispatch("unsetSnackbar");
            }
            return snackbar;
        }
    },
    methods: {
        closeSnackbar() {
            this.$store.dispatch("unsetSnackbar");
        }
    },
    mounted() {
        this.$store.dispatch("getAuthUser");
    }
};
</script>
