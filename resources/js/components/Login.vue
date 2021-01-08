<template>
<div>
<v-app>
    <v-app-bar
        app
        color="blue darken-3"
        dark
    >
        <v-toolbar-title>Document Tracking System</v-toolbar-title>
        <v-progress-linear
            :active="page_loader"
            color="#A83F39"
            height="8"
            indeterminate
            striped
            absolute
            bottom
        />
        <v-spacer></v-spacer>
    </v-app-bar>
    <v-main>
        <v-layout align-center justify-center flat>
            <v-flex xs12 sm10 md4>
                <v-row align="center" justify="center" class="mb-5">
                    <v-img
                        src="/images/provincial_logo.png"
                        lazy-src="/images/provincial_logo.png"
                        max-width="300"
                        max-height="350"
                    >
                        <template v-slot:placeholder>
                            <v-row
                                class="fill-height ma-0"
                                align="center"
                                justify="center"
                            >
                                <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                            </v-row>
                        </template>
                    </v-img>
                </v-row>
                <!-- TODO: Add VeeValidate and disable button when login is pressed to prevent multiple login request -->
                <v-form @submit.prevent="login">
                    <v-card>
                        <v-toolbar color="blue darken-3" dark flat>
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
                                <v-text-field
                                    prepend-inner-icon="mdi-account-box"
                                    name="username"
                                    v-model="form.username"
                                    label="Username"
                                    id="username"
                                    type="text"
                                    outlined
                                    required
                                ></v-text-field>
                                <v-text-field
                                    prepend-inner-icon="mdi-form-textbox-password"
                                    name="password"
                                    v-model="form.password"
                                    label="Password"
                                    id="password"
                                    type="password"
                                    required
                                    outlined
                                ></v-text-field>
                        </v-card-text>
                        <v-card-actions >
                            <v-spacer></v-spacer>
                            <v-btn dark color="blue darken-3" type="submit">Login</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-flex>
        </v-layout>
    </v-main>
</v-app>
</div>


</template>

<script>
import { mapGetters } from 'vuex';
export default {
  computed: mapGetters(['page_loader']),
  data() {
      return {
          form: {
              username: '',
              password: ''
          },
          submitStatus: ''
      }
  },
  methods: {
       login() {
           this.$store.dispatch('setLoader');
           this.submitStatus = '';
           axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/login', this.form)
                .then(response => {
                    this.$router.push({
                        name: 'Dashboard'
                    });
                })
                .catch(error => {
                    this.submitStatus = 'ERROR'
                });
           });
       }
    },

    mounted() {
        this.$store.dispatch('unsetLoader');
    }
};
</script>