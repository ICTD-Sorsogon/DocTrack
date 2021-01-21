<template>
<v-container>
  <v-card flat>
          <v-card-title primary-title>
              Logs 
              <v-row align="center" justify="end" class="pr-4">
                  <v-btn color="primary" @click="exportExcel"
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
      class="elevation-1"
    >
      <template v-slot:top>
      
          <v-dialog
            :headers="headers2"
            v-model="dialog"
            max-width="80vw"
          >
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>

                <v-spacer></v-spacer>
                  <v-icon 
                      @click="close"
                        large
                        class="ml-4"
                      >
                    mdi-close
                  </v-icon>
              </v-card-title>

            <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Label
                      </th>
                      <th class="text-left">
                        New
                      </th>
                      <th class="text-left">
                        Old
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in final_data"
                      :key="index"
                    >
                      <td>{{ item['key'] }}</td>
                      <td>{{ item['new'] }}</td>
                      <td>{{ item['old'] }}</td>
                    </tr>
                  </tbody>
                </template>
            </v-simple-table>

              <v-card-actions>
                <v-spacer></v-spacer>
                
              </v-card-actions>
            </v-card>
          </v-dialog>
          
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon 
          medium
          class="ml-3"
          color="blue"
          @click="editItem(item)"
        >
          mdi-more
        </v-icon>
      </template>
    </v-data-table>
  </template>



  </v-card>
</v-container>
    

</template>

<script>
import { mapGetters, mapActions } from "vuex";
import XLSX from 'xlsx';
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
            final_data: [],
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
            export_excel: [],
            data: []
        }
    },
    methods:{
      exportExcel(){
        var currentDate = new Date();
        var day = currentDate.getDate()
        var month = currentDate.getMonth() + 1
        var year = currentDate.getFullYear()
        let date_format = month+"/"+day+"/"+year;
        this.export_excel = [];
        this.logs.forEach((log)=>{
          console.log(log);
          this.export_excel.push({
            Username: log.user.username,
            Action: log.action,
            Table_Name: log.table_name,
            Item_ID: log.item_id,
            New_Values: log.new_values,
            Old_Values: log.original_values,
          })
        })
        var sheet_name = "Logs";
        var logs_rec = XLSX.utils.json_to_sheet(this.export_excel);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, logs_rec, sheet_name);
        XLSX.writeFile(wb, date_format+' - Record_List.xlsx');
      },

      initialize () {this.logs},
      editItem (item) {
        this.editedIndex = this.logs.indexOf(item)
        this.editedItem = Object.assign(item)
        this.dialog = true

        let log_data = this.editedItem;

        var log_new_key = [];
        var log_view_new = [];

        var log_view_old = [];
        var log_old_key = [];

        this.final_data = [];
    
        log_new_key = Object.keys(JSON.parse(log_data.new_values));
        log_view_new = Object.values(JSON.parse(log_data.new_values));

        if(log_data.original_values != null){

          log_old_key = Object.keys(JSON.parse(log_data.original_values));
          log_view_old = Object.values(JSON.parse(log_data.original_values));
            for (var i = 0 ; i < log_old_key.length; i++){
              this.final_data.push({
                'key': log_new_key[i],
                'new': log_view_new[i],
                'old': log_view_old[i]
              })
            }


        }else{
          for (var i = 0 ; i < log_new_key.length; i++){
            this.final_data.push({
              'key': log_new_key[i],
              'new': log_view_new[i]
            })
          }


        }

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
    },
    mounted() {

      this.$store.dispatch('unsetLoader');
      this.$store.dispatch('getLogs');
    }

}
</script>