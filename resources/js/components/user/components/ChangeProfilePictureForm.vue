<template>
    <div>
        <v-col align="center" justify="end">
            <v-img max-height="150" max-width="250" :src="url"></v-img>
        </v-col>

        <ValidationObserver ref="observer" v-slot="{ valid }">
            <v-col cols="12" xl="12" lg="12" md="12" align="center">
            <ValidationProvider rules="required|size:1000" v-slot="{ errors, valid }">
                <v-file-input
                    show-size
                    :clearable="false"
                    accept="image/*"
                    @change="Preview_image"
                    v-model="image"
                    label="Choose from file"
                    :error-messages="errors"
                    :success="valid"
                    >
                </v-file-input>
            </ValidationProvider>
            </v-col>

        <v-row>
            <v-col align="center" justify="end">
                <v-btn
                    color="primary"
                    type="submit"
                    :loading="btnloading"
                    :dark="valid"
                    :disabled="!valid"
                    @click.prevent="uploadProfilePicture"
                >
                    <v-icon left dark>
                        mdi-send-circle-outline
                    </v-icon>
                    Submit
                </v-btn>
            </v-col>
        </v-row>
        </ValidationObserver>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';
export default {
    computed: mapGetters(["auth_user", "form_requests", "request"]),
    components: {
        ValidationProvider,
        ValidationObserver
    },
    data(){
        return {
            url: null,
            image: null,
            btnloading: false,
        }
    },
    methods: {
        Preview_image() {
            this.url= URL.createObjectURL(this.image)
        },
        uploadProfilePicture(){
            this.btnloading = true;
            this.$store.dispatch("uploadProfilePicture", this.image).then(() => {
                    if(this.request.status == 'SUCCESS') {
                        this.$store.dispatch('setSnackbar', {
                           type: 'success',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                            this.$store.dispatch('getAuthUser');
                            this.$router.push({ name: "All Active Documents"});
                        });
                    } else if (this.request.status == 'FAILED') {
                        this.$store.dispatch('setSnackbar', {
                            type: 'error',
                            message: this.request.message,
                        })
                        .then(() => {
                            this.btnloading = false;
                        });
                    }
                });
        },
        mounted() {
            this.$store.dispatch('unsetLoader');
        },

  }

}
</script>