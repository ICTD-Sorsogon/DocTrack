// TODO: Do this to make reusable pagination
<template>
    <v-pagination
        v-model="page"
        :length="page.total"
        :total-visible="10"
    ></v-pagination>
</template>

<script>
export default {
    name: 'Pagination.vue',
    props: ['store', 'collection'],
    computed: {
        page: {
            get() {
                return this.$store.state[this.store][this.collection].current_page;
            },
            set(value) {
                return this.$store.commit(`${this.store}/SET_CURRENT_PAGE`, value);
            }
        },
        last_page: {
            get() {

            }
        }
    }
}
</script>