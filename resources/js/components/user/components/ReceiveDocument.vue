<template>
    <!-- TODO: CONTINUE THIS WITH AUTHORIZING PERSON TABLE -->
    <v-card flat>
        <v-card-title primary-title>
            Receive Document : {{ selected_document.tracking_code }}
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-row>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-select
                                v-model="form.action"
                                :items="action"
                                label="Action"
                                prepend-inner-icon="mdi-account-question-outline"
                                :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                                outlined
                                required
                                :error-messages="errors"
                                :success="valid"
                            ></v-select>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-select
                                v-model="form.status"
                                :items="status"
                                label="Status"
                                prepend-inner-icon="mdi-file-question-outline"
                                :menu-props="{ bottom: true, offsetY: true, transition: 'slide-y-transition'}"
                                outlined
                                required
                                :error-messages="errors"
                                :success="valid"
                            ></v-select>
                        </ValidationProvider>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" xl="6" lg="6" md="6">
                        <ValidationProvider rules="required" v-slot="{ errors, valid }">
                            <v-combobox
                                v-model="form.approved_by"

                                item-text="name"
                                clearable
                                hide-selected
                                outlined
                                persistent-hint
                                label="Originating Office"
                                prepend-inner-icon="mdi-office-building-marker-outline"
                                :error-messages="errors"
                                :success="valid"
                                required
                            ></v-combobox>
                        </ValidationProvider>
                    </v-col>
                    <v-col cols="12" xl="6" lg="6" md="6">

                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';
export default {
    components: {
        ValidationProvider,
        ValidationObserver
    },
    computed: {
        ...mapGetters(['selected_document']),
    },
    data() {
        return {
            status: ['Sample', 'Sample2'],
            action: ['Sample3', 'Sample4'],
            form: {
                status: '',
                action: '',
                approved_by: '',
            }
        }
    },
    mounted() {

    }
}
</script>