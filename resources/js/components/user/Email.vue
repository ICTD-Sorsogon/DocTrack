<template>
    <div>
        <v-combobox
            v-model="selected_offices"
            :items="offices"
            item-text="name"
            clearable
            hide-selected
            persistent-hint
            label="Originating Office"
            chips
            required
            class="mx-4"
            dense
            multiple
            counter
        >
            <template
                v-slot:selection="{
                    item,
                    select,
                    selected
                }"
            >
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-chip
                            color="primary"
                            v-bind="attrs"
                            v-on="on"
                            small
                            @click="select"
                            :input-value="selected"
                            close
                            @click:close="
                                removeSelectedChips('originating', item)
                            "
                        >
                            {{ item.office_code || item }}
                        </v-chip>
                    </template>
                    <span>{{ item.name || item }}</span>
                </v-tooltip>
            </template>
        </v-combobox>

    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {

    data(){
        return {
            selected_offices: []

        }
    },
    watch: {
        "selected_offices"(newVal) {
            newVal.forEach(currItem => {
                if (!this.offices.map(o => o.id).includes(currItem.id)) {
                    this.selected_offices.pop();
                }
            });
        },
    },
    computed:{
        ...mapGetters(["offices"]),
    },
    methods:{
        removeSelectedChips(el, item) {
            this.selected_offices.splice(
                this.selected_offices.indexOf(item),
                1
            );
            this.selected_offices = [...this.selected_offices];
        },
    },
    mounted() {
        this.$store.dispatch("unsetLoader");
        this.$store.dispatch("getOffices");
    }
}
</script>

<style>

</style>
