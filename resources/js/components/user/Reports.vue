<template>
<div>Document Reports</div>
</template>

<script>
export default {
    mounted() {
        this.$store.dispatch('unsetLoader');
    }
}
</script>