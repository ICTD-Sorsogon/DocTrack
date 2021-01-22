<template>
<v-app>
    <v-main>
        <v-container fill-height fluid>
        <v-row
            align="center"
            justify="center"
            class="text-center"
        >
            <v-col cols="12">
                <v-icon
                    size="25em"
                    color="black"
                >
                    mdi-shredder
                </v-icon>
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
                        mdi-home
                    </v-icon>
                    Back to Home
                </v-btn>
            </v-col>
        </v-row>
        </v-container>
    </v-main>
</v-app>

</template>

<script>
export default {

}
</script>