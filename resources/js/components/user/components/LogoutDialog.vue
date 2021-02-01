<template>
    <v-row justify="space-around">
        <v-col>
            <v-dialog
                persistent
                max-width="450px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        block
                        dark
                        outlined
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon left>
                            mdi-logout-variant
                        </v-icon>
                        Logout
                    </v-btn>
                </template>
                <template v-slot:default="dialog">
                    <v-card color="grey lighten-2">
                        <v-card-title class="headline">
                            <v-icon class="mr-2" size="30px">mdi-logout</v-icon> Logout
                        </v-card-title>
                        <v-card-text>
                            Are you sure you want to logout of the application?
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                text
                                @click.prevent="triggerLogout()"
                            >
                                Yes
                            </v-btn>
                            <v-btn
                                text
                                @click.prevent="dialog.value = false"
                            >
                                No
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
            </v-dialog>
        </v-col>
    </v-row>
</template>

<script>
export default {
    methods: {
        triggerLogout() {
            this.$emit('trigger-logout');
        }
    }
}
</script>