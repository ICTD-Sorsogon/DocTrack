<template>
<div>
<v-app>
    <v-app-bar
        app
        color="#0675BB"
        dark
    >
        <v-toolbar-title>Document Tracking System</v-toolbar-title>
        <v-progress-linear
            :active="page_loader"
            color="#F72e2E"
            height="8"
            indeterminate
            striped
            absolute
            bottom
        />
        <v-spacer></v-spacer>
    </v-app-bar>
    <v-main>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-layout align-center justify-center flat>
                        <v-flex xs11 sm10 md7 lg5 xl4>
                            <v-row align="center" justify="center">
                                <v-img
                                    src="/images/provincial_logo.png"
                                    lazy-src="/images/provincial_logo.png"
                                    :max-width="max_width"
                                    :max-height="max_height"
                                >
                                    <template v-slot:placeholder>
                                        <v-row
                                            class="fill-height ma-0"
                                            align="center"
                                            justify="center"
                                        >
                                            <v-progress-circular indeterminate color="#F72e2E"/>
                                        </v-row>
                                    </template>
                                </v-img>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <v-card>
                                        <ValidationObserver ref="observer" v-slot="{ valid }">
                                            <v-form @submit.prevent="login">
                                                <v-toolbar color="#0675BB" dark flat>
                                                    <v-toolbar-title>Login</v-toolbar-title>
                                                </v-toolbar>
                                                <v-card-text>
                                                    <v-alert
                                                        dense
                                                        outlined
                                                        dismissible
                                                        v-if="submitStatus === 'ERROR'"
                                                        type="error"
                                                    >
                                                        Login Failed. Incorrect username or password
                                                    </v-alert>

                                                    <ValidationProvider rules="required" v-slot="{ errors }">
                                                        <v-text-field
                                                            prepend-inner-icon="mdi-account-box"
                                                            name="username"
                                                            v-model="form.username"
                                                            :error-messages="errors"
                                                            label="Username"
                                                            id="username"
                                                            type="text"
                                                            outlined
                                                            required
                                                        />
                                                    </ValidationProvider>
                                                    <ValidationProvider rules="required" v-slot="{ errors }">
                                                        <v-text-field
                                                            prepend-inner-icon="mdi-form-textbox-password"
                                                            name="password"
                                                            v-model="form.password"
                                                            :error-messages="errors"
                                                            label="Password"
                                                            id="password"
                                                            type="password"
                                                            required
                                                            outlined
                                                        />
                                                    </ValidationProvider>
                                                </v-card-text>
                                                <v-card-actions>
                                                    <v-row
                                                        align="center"
                                                        justify="center"
                                                        class="text-center"
                                                    >
                                                        <v-col>
                                                            <v-btn :disabled="!valid" :dark="valid" color="#0675BB" type="submit">Login</v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-card-actions>
                                            </v-form>
                                        </ValidationObserver>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-flex>
                    </v-layout>
                </v-col>
            </v-row>
        </v-container>
    </v-main>
</v-app>
</div>


</template>

<script>
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';

export default {
    components:{
        ValidationProvider,
        ValidationObserver,
    },
    data() {
        return {
            form: {
                username: '',
                password: ''
            },
            submitStatus: '',
            valid: false,
        }
    },
    computed: {
        page_loader () {
            return this.$store.getters.page_loader;
        },
        max_width() {
            switch (this.$vuetify.breakpoint.name) {
            case 'xs': return 300
            case 'sm': return 400
            case 'md': return 400
            case 'lg': return 415
            case 'xl': return 580
            }
        },
        max_height() {
            switch (this.$vuetify.breakpoint.name) {
            case 'xs': return 350
            case 'sm': return 420
            case 'md': return 450
            case 'lg': return 455
            case 'xl': return 620
            }
        }
    },
    methods: {
        login() {
            this.$store.dispatch('setLoader');
            this.submitStatus = '';
            axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('login', this.form)
                    .then(response => {
                        this.$router.push({
                            path: 'all_active_document'
                        });
                    })
                    .catch(error => {
                        this.submitStatus = 'ERROR';
                        this.$store.dispatch('unsetLoader');
                    });
            });
        }
        },

    mounted() {
        this.$store.dispatch('unsetLoader');
    }
};
</script>