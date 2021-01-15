<template>
    <v-card flat>
        <v-card-title primary-title>
            Logs 
            <v-row align="center" justify="end" class="pr-4">
                <v-btn color="primary" @click.prevent="getAllDocuments"
                >
                <v-icon
                  small
                  class="mr-2"
                >
                  mdi-export
                </v-icon>
                Export</v-btn
                >
            </v-row>
        </v-card-title>

        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
          single-line
          hide-details
        ></v-text-field>

<template>
  <v-data-table
    :headers="headers"
    :items="logs"
    :search="search"
    sort-by="calories"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar
        flat
      >
     
        <v-dialog
          :headers="headers2"
          v-model="dialog"
          max-width="90vw"
        >
        
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    sm="6"
                    md="6"
                  >
<v-textarea
      auto-grow
          class="mx-2"
          readonly
          label="Old Values"
          rows="1"
          prepend-icon="mdi-comment"
          v-model="editedItem.original_values"
        ></v-textarea>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="6"
                  >
<v-textarea
      auto-grow
          class="mx-2"
          readonly
          label="New Values"
          rows="1"
          prepend-icon="mdi-comment"
          v-model="editedItem.new_values"
        ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="close"
              >
                Cancel
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        
      </v-toolbar>
    </template>
    <!-- <template v-slot:item.actions="{ item }">
      <v-icon 
        small
        class="ml-4"
        @click="editItem(item)"
      >
        mdi-more
      </v-icon>
    </template> -->
  </v-data-table>
</template>


 </v-card>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    data() {
        return {
            dialog: false,
            dialogDelete: false,

            search: '',
            editedIndex: -1,
            editedItem: {
              new_values: '',
              action: 0,
              table_name: 0,
              item_id: 0,
            },
            defaultItem: {
              action: 0,
              table_name: 0,
              item_id: 0,
            },
            headers: [
                { text: 'Username', value: 'user.username' },
                { text: 'Action', value: 'action' },
                { text: 'Table Name', value: 'table_name' },
                { text: 'Item ID', value: 'item_id' },
                { text: 'View More', value: 'actions', sortable: false },
            ],
            headers2: [
                { text: 'New Values ', value: 'new_values' },
                { text: 'Original Values ', value: 'original_values' },
            ],
        }
    },
    methods:{
      initialize () {this.logs},
      editItem (item) {
        this.editedIndex = this.logs.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

    },

      computed: {
        ...mapGetters(['logs']),
  
        formTitle () {
          return this.editedIndex !== -1 ? 'Logs Value' : 'Logs Value'
        },
      },
    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },

    created () {
      this.initialize()
    },
    mounted() {
      this.$store.dispatch('unsetLoader');
      this.$store.dispatch('getLogs');
    }

}
</script>