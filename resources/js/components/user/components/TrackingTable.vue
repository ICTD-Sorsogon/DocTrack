<template>
    <v-card flat>
        <v-card-title primary-title>
            Tracking Reports
        </v-card-title>
            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :search="search"
                        :items="summaries"
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
        </div>
    </v-card>
</template>
