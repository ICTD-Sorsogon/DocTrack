<template>
    <div>
        <!-- Kenneth -->
        <!-- MASTER LIST -->
        <v-row>
            <v-col v-bind="bp(12)" v-if="dialog_for=='masterList'">
                <v-btn @click="download" color="primary" style="width:100%" elevation="4" depressed large>Export Master List</v-btn>
            </v-col>
        </v-row>
        <!-- ADVANCE EXPORT -->
        <div v-if="dialog_for=='advanceExport'">
            <v-row>
                <v-col v-bind="bp(12)">
                    <!--<v-radio
                    class="mx-4"
                        label="Radio 0"
                        value="0"
                    ></v-radio>
                    <v-radio
                    class="mx-4"
                        label="Radio 1"
                        value="1"
                    ></v-radio>-->
                    <v-radio-group
                    class="mx-5"
                    v-model="wsType"
                    row
                    >
                    <v-radio
                        label="Single Excel WorkSheet"
                        value="radio-1"
                    ></v-radio>
                    <v-radio
                        label="Group by Excel WorkSheet"
                        value="radio-2"
                    ></v-radio>
                    </v-radio-group>
                </v-col>
            </v-row>
            <v-col v-bind="bp(12)" v-if="auth_user.role_id === 1 ">
                <v-radio-group
                    class="mx-4"
                    v-model="wsType"
                    v-bind="bp(12)"
                    >
                    <v-row class="d-flex align-center">
                        <v-col cols="1">
                            <v-radio
                                label=""
                                value="radio-1"
                            ></v-radio>
                        </v-col>
                        <v-col cols="11">
                            <v-combobox
                                v-model="originating"
                                :items="offices"
                                item-text="name"
                                clearable
                                hide-selected
                                persistent-hint
                                label="Select Office"
                                chips
                                required
                                dense
                                multiple
                                full-width="100%"
                                counter
                            >
                                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-chip color="primary" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeSelectedChips('originating', item)">
                                                {{ item.office_code || item }}
                                            </v-chip>
                                        </template>
                                        <span >{{item.name || item}}</span>
                                    </v-tooltip>
                                </template>
                            </v-combobox>

                        </v-col>

                    </v-row>

                 </v-radio-group>
            </v-col>
            <v-col v-bind="bp(12)">
                <v-radio-group
                    class="mx-4"
                    v-model="wsType"
                    row
                    v-bind="bp(12)"
                    >
                    <v-row class="d-flex align-center">
                        <v-col cols="1">
                            <v-radio
                                label=""
                                value="radio-1"
                            ></v-radio>
                        </v-col>
                        <v-col cols="10">
                            <v-select class="mx-4" width="100%" :items="document_types" item-text="name" item-value="name" v-model="selected_type" label="Document Type" dense clearable hide-selected multiple deletable-chips chips counter>
                                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-chip color="primary" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeSelectedChips('selected_type', item.name)">
                                                {{item.name}}
                                            </v-chip>
                                        </template>
                                        <span >{{item.name}}</span>
                                    </v-tooltip>
                                </template>
                            </v-select>
                        </v-col>
                    </v-row>

                 </v-radio-group>
            </v-col>
            <v-col v-bind="bp(12)">
                <!--<v-autocomplete
                    v-model="source"
                    :items="source_list"
                    item-text="key"
                    dense
                    filled
                    label="Source:"
                ></v-autocomplete>-->

                    <v-radio-group
                        class="mx-4"
                        v-model="wsType"
                        row
                        v-bind="bp(12)"
                        >
                        <v-row class="d-flex align-center">
                            <v-col cols="1">
                                <v-radio
                                    label=""
                                    value="radio-1"
                                ></v-radio>
                            </v-col>
                            <v-col cols="10">
                                <v-select class="mx-4" full-width="100%" :items="source_list" v-model="source" label="Document Source" dense clearable hide-selected multiple deletable-chips chips counter>
                                    <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                                        <v-tooltip top>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-chip color="primary" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeSelectedChips('source', item)">
                                                    {{item}}
                                                </v-chip>
                                            </template>
                                            <span >{{item}}</span>
                                        </v-tooltip>
                                    </template>
                                </v-select>
                            </v-col>
                        </v-row>
                    </v-radio-group>
            </v-col>
            <v-row>
                <v-col v-bind="bp(12)">
                    <v-btn :disabled="advanceBtn" @click="download" color="primary" style="width:100%" elevation="4" depressed large>Advance Export</v-btn>
                </v-col>
            </v-row>
        </div>
        <!-- END  -->
    </div>
</template>

<script>

import { mapGetters } from 'vuex';
import { breakpoint } from '../../../constants';

export default {
    props: ['dialog_for'],
    data(){
        return {
            advanceBtn: true,
            selected_type: [],
            export_list: [
                { key: 'Document Type', value: 'document_type'},
                { key: 'Originating Office', value: 'origin_office'},
            ],
            source: [],
            source_list: ['External', 'Internal'],
            originating: [],
            wsType: 'radio-1'
        }
    },
    watch: {
        'selected_type'(val) {
            this.advanceBtn = (val.length < 1)? true:false
        }
    },
    computed: {
        ...mapGetters(['document_types', 'auth_user', 'offices']),
    },
    methods: {
        removeSelectedChips(vars, item){
            this[vars].splice(this[vars].indexOf(item), 1)
            this[vars] = [...this[vars]]
        },
        bp(col){
            return breakpoint(col)
        },
        download(){
            this[this.dialog_for]()
        },
        advanceExport(){
            const type = this.$store.state.documents.documentsArchive[0].selected.filter;
            const document_data = this.$store.state.documents.documentsArchive[0].selected[type.toLowerCase()].data
            const data = this.source == null ? document_data : document_data.filter(document => document.is_external == this.source)

            const priority_list = ['High', 'Medium', 'Low', 'Indefinite']

            import('./modules').then(({archiveae}) => {
                archiveae({
                    type: type,
                    document_data: document_data,
                    priority_list: priority_list,
                    data:data,
                    selected_type: this.selected_type
                })
            })
        },
        masterList(){
            const type = this.$store.state.documents.documentsArchive[0].selected.filter;
            const data = this.$store.state.documents.documentsArchive[0].selected[type.toLowerCase()].data
            const priority_list = ['High', 'Medium', 'Low', 'Indefinite']

            import('./modules').then(({archiveml}) => {
                archiveml({
                    type: type,
                    priority_list: priority_list,
                    data:data,
                })
            })
        },
    },
    mounted(){
        this.selected_type = this.document_types.map(t => t.name)
        this.originating = this.offices
        this.source = this.source_list
        console.log(this.dialog_for)
    }

}
</script>

<style>

</style>
