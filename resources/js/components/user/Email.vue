<template>
  <h1>Email</h1>
</template>

<script>
export default {
    

    mounted() {
        this.$store.dispatch("unsetLoader");
        this.$store.dispatch("getOffices");
    }
}
</script>

<style>

</style>
