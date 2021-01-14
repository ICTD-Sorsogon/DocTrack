<template>
<v-dialog v-model="dialog" persistent scrollable max-width="1000px">
      <v-card v-if="selected_office">
        <v-container>
          <v-row>
            <v-col cols="6" sm="6">
              <v-card-title primary-title> Document Details </v-card-title>
            </v-col>
            <v-col cols="6" sm="6">
                <v-card-actions class="mr-4">
                    <v-spacer></v-spacer>
                    <v-btn x-large color="gray" @click="$emit('close-dialog', false)" icon>
                    <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-col>
          </v-row>
        </v-container>
        <v-card-text>

          <v-row>
            <v-col>
              <v-list flat subheader>
                <v-subheader>Subject</v-subheader>
                <v-list-item>
                  <v-list-item-action>
                    <v-icon>mdi-square-medium</v-icon>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>{{
                      selected_office.name
                    }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>

        </v-card-text>
      </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: ['selected_office', 'dialog'],
        mounted() {
            console.log('ff',this.selected_office);
        }
    }

</script>

<style>

</style>
