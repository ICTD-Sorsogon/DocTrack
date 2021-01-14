<template>
    <v-card flat>
        <v-card-title primary-title>
            Logs Report 
            <v-row align="center" justify="end" class="pr-4">
                <v-btn color="primary" @click.prevent="getAllDocuments"
                >Export</v-btn
                >
            </v-row>
        </v-card-title>
        <v-row>
            <v-col cols="12">
                  <v-data-table :headers="headers"
                    :items="data"
                    :items-per-page="5"
                    class="elevation-1"
                ></v-data-table>
            </v-col>
        </v-row>
    </v-card>


</template>

<script>
export default {
    data() {
        return {
            dialog: false,
            headers: [
                {
                    text: 'Username',
                    align: 'start',
                    value: 'username',
                },
                { text: 'Action', value: 'action' },
                { text: 'Table Name', value: 'table_name' },
                { text: 'Item ID', value: 'item_id' },
                { text: 'Old Value', value: 'old_value' },
                { text: 'New Value', value: 'new_value' },
            ],
            data: [
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            {
                username: 'Username1',
                action: 'action1',
                table_name: 'tablename',
                item_id: 12,
                old_value: 'test',
                new_value: 'new test',
            },
            ],
        }
    },
    mounted() {
        this.$store.dispatch('unsetLoader');
    }
}
</script>