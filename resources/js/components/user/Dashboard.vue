<template>
<div>Dashboard</div>
</template>

<script>
export default {
    mounted() {
        this.$store.dispatch('unsetLoader');
    }
}
</script>