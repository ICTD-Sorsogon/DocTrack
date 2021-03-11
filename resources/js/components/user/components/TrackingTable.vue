<template>
    <v-row>
        <v-col cols="12">
            <v-data-table
                :search="search"
                :items="stats"
                :items-per-page="10"
                :headers="headers"
                class="elevation-1"
                :custom-filter="filterOnlyOffice"
            >
                <template v-slot:top>
                    <v-text-field
                        prepend-inner-icon="mdi-magnify"
                        v-model="search"
                        label="Search"
                        class="mx-4"
                    />
                </template>
            </v-data-table>
        </v-col>
    </v-row>
</template>
<script>
export default {
    props: ["stats"],
    data() {
        return {
            search: "",
            headers: [
                { text: "Office", align: "start", value: "office.name" },
                {
                    text: "All Transaction",
                    value: "transaction",
                    filterable: false
                },
                { text: "Created", value: "created", filterable: false },
                {
                    text: "Acknowledged",
                    value: "acknowledged",
                    filterable: false
                },
                { text: "Forwarded", value: "forwarded", filterable: false },
                { text: "Received", value: "received", filterable: false },
                {
                    text: "Delayed Document",
                    value: "delayed",
                    filterable: false
                },
                {
                    text: "Fastest Transaction",
                    value: "fast.fast",
                    filterable: false
                },
                {
                    text: "Slowest Transaction",
                    value: "slow.slow",
                    filterable: false
                },
                {
                    text: "Average Transaction Speed",
                    value: "average",
                    filterable: false
                },
                {
                    text: "Efficiency Rating*",
                    value: "efficiency",
                    filterable: false
                }
            ]
        };
    },
    methods: {
        filterOnlyOffice(value, search, item) {
            return (
                value != null &&
                search != null &&
                typeof value === "string" &&
                value
                    .toString()
                    .toLowerCase()
                    .indexOf(search) !== -1
            );
        }
    },
    mounted() {}
};
</script>
