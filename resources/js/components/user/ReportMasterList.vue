<template>
    <v-card flat>
        Master List Page
    </v-card>
</template>

<script>
export default {
    mounted () {
        this.$store.dispatch('unsetLoader');
    }
}
</script>