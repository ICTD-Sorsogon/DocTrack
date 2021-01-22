<template>
<v-app>
    <v-main>
        <v-container fill-height fluid>
        <v-row
            align="center"
            justify="center"
            class="text-center"
        >
            <v-col
                cols="12"
                align="center"
                justify="center"
            >
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
                            <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                        </v-row>
                    </template>
                </v-img>
            </v-col>
            <v-col cols="12">
                 <h1>
                    PAGE NOT FOUND
                </h1>
            </v-col>
            <v-col cols="12">
                <v-btn
                    outlined
                    color="primary"
                    link
                    to="/"
                >
                    <v-icon
                        left
                        dark
                    >
                        mdi-keyboard-backspace
                    </v-icon>
                    Back
                </v-btn>
            </v-col>
        </v-row>
        </v-container>
    </v-main>
</v-app>

</template>

<script>
export default {
    computed: {
        max_width() {
            switch (this.$vuetify.breakpoint.name) {
            case 'xs': return 300
            case 'sm': return 400
            case 'md': return 400
            case 'lg': return 415
            case 'xl': return 680
            }
        },
        max_height() {
            switch (this.$vuetify.breakpoint.name) {
            case 'xs': return 350
            case 'sm': return 420
            case 'md': return 450
            case 'lg': return 455
            case 'xl': return 720
            }
        }
    },
}
</script>