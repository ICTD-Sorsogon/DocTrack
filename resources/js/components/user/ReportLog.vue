<template>
    <v-card flat>
        <v-card-title primary-title>
            Logs 
            <v-row align="center" justify="end" class="pr-4">
                <v-btn color="primary" @click="test"
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

<div class="row">
  <div class="col-4 pr-0">
<v-simple-table>
    <template v-slot:default>
      <thead>
        <tr>
          <th class="text-left">
            Label
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, index) in view_log_key"
          :key="index"
        >
          <td>{{ item }}</td>
        </tr>
      </tbody>
    </template>
  </v-simple-table>
  </div>
  <div class="col-4 pr-0 pl-0">
<v-simple-table>
    <template v-slot:default>
      <thead>
        <tr>
          <th class="text-left">
            New
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, index) in view_log"
          :key="index"
        >
          <td>{{ item }}</td>
        </tr>
      </tbody>
    </template>
  </v-simple-table>
  </div>
<div class="col-4 pl-0">
<v-simple-table>
    <template v-slot:default>
      <thead>
        <tr>
          <th class="text-left">
            Old
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, index) in view_log_old"
          :key="index"
        >
          <td>{{ item }}</td>
        </tr>
      </tbody>
    </template>
  </v-simple-table>
  </div>
</div>


            <!-- <v-card-text>
              <div class="row">
                <div class="col-4">
                  <ul>
                    <li v-for="(item, index) in view_log_key" :key="index">
                      {{item}}
                    </li>
                  </ul>
                </div>
                <div class="col-4">
                  <ul>
                    <li v-for="(item, index) in view_log_old" :key="index">
                      {{item}}
                    </li>
                  </ul>
                </div>
                <div class="col-4">
                  <ul>
                    <li v-for="(item, index) in view_log" :key="index">
                      {{item}}
                    </li>
                  </ul>
                </div>
              </div>


            </v-card-text> -->

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
    <template v-slot:item.actions="{ item }">
      <v-icon 
        small
        class="ml-4"
        @click="editItem(item)"
      >
        mdi-more
      </v-icon>
    </template>
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
            view_log: [],
            view_log_old: [],
            view_log_key: [],
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
        this.editedItem = Object.assign(item)
        this.dialog = true

        let log_data = this.editedItem;

        var arr = [
          {
            id: '',
            code: '',
            old_value: '',
            new_value: ''
          }
        ];

        var log_view_key = [];
        var log_view_old = [];
        var log_view_new = [];

    
        log_view_key = Object.keys(JSON.parse(log_data.new_values));
        log_view_new = Object.values(JSON.parse(log_data.new_values));
        this.view_log = [];
        this.view_log_old = [];
        this.view_log_key = [];


       for (let index = 0; index < (Object.keys(JSON.parse(log_data.new_values)).length - 1); index++) {

        var newlog= log_view_new[index];
        var keylog = log_view_key[index];

        // this.view_log.push({[ keylog ]:newlog})
        this.view_log.push(newlog);
        this.view_log_key.push(keylog);

       } 


        if(log_data.original_values != null){

          this.view_log_key = [];
          log_view_old = Object.values(JSON.parse(log_data.original_values));
          for (let index = 0; index < (Object.keys(JSON.parse(log_data.original_values)).length - 1); index++) {
            var oldlog= log_view_old[index];
            var keylog = log_view_key[index];

            this.view_log_old.push(oldlog);
            this.view_log_key.push(keylog);

          } 
        }else{
          console.log('no value');
        }



        // let view_log_old = JSON.parse(this.editedItem.original_values);
        // let view_log_new = JSON.parse(this.editedItem.new_values);
        // this.view_log = [];

        // this.view_log.push(view_log_old);
        // this.view_log.push(view_log_new);

      },
      dataTable(data){
        //   Object.entries(data );
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
      test(){
        return this.editedItem;
      },
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
      this.initialize();
      console.log(this.test);

    },
    mounted() {

      this.$store.dispatch('unsetLoader');
      this.$store.dispatch('getLogs');
    }

}
</script>