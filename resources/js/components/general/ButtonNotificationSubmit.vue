<template>
    <div>
        <v-col
            align="center"
            justify="end"
        >
            <v-btn
                color="primary"
                dark
                @click="dialog=true"
            >
                <slot name="button-title"></slot>
            </v-btn>
        </v-col>

        <v-row justify="center">
            <v-dialog
                v-model="dialog"
                persistent
                max-width="400px"
            >
            <v-card>
                <v-card-title class="headline light-blue lighten-5">
                    <slot name="modal-title"></slot>
                </v-card-title>
                <v-card-text>
                    <slot name="modal-message"></slot>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary darken-1"
                        text
                        @click="dialog = false"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary darken-1"
                        text
                        type="submit"
                        @click="dialog = false;this.$emit('submit');"
                    >
                        Confirm
                    </v-btn>
                </v-card-actions>
            </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
export default {
    data() {
        return {
            dialog: false,
            clicked: '',
        }
    },
    methods: {
        emitSubmit() {
            this.$emit('submit');
        }
    }
}
</script>