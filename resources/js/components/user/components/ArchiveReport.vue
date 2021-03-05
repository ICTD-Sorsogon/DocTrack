<template>
    <div>
        <v-row>
            <v-col v-bind="bp(12)" v-if="dialog_for=='masterList'">
                <v-btn @click="download" color="primary" style="width:100%" elevation="4" depressed large>Export Master List</v-btn>
            </v-col>
        </v-row>
        <div v-if="dialog_for=='advanceExport'">
            <v-row>
                <v-col v-bind="bp(12)">
                    <v-radio-group class="mx-5" v-model="wsType" row>
                        <v-radio label="Single Worksheet" value="single"/>
                        <v-radio label="Group by Worksheet" value="group"/>
                    </v-radio-group>
                </v-col>
            </v-row>
            <v-col v-bind="bp(12)" v-if="auth_user.role_id === 1 ">
                <v-radio-group class="mx-4" v-model="wsTypeSel" row v-bind="bp(12)">
                    <v-row class="d-flex align-center">
                        <v-col cols="1" v-if="isByGroup">
                            <v-radio value="byOffice" @click="groupBy()"/>
                        </v-col>
                        <v-col :cols="(isByGroup)?'11':'12'">
                            <v-combobox
                                ref="byOffice"
                                v-model="originating"
                                :items="offices"
                                item-text="name"
                                clearable
                                hide-selected
                                persistent-hint
                                label="Select Office's"
                                chips
                                required
                                dense
                                multiple
                                counter
                                :disabled="isByGroup && !group.byOffice"
                                :filled="isByGroup && !group.byOffice"
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
                <v-radio-group class="mx-4" v-model="wsTypeSel" row v-bind="bp(12)">
                    <v-row class="d-flex align-center">
                        <v-col cols="1" v-if="isByGroup">
                            <v-radio value="byType" @click="groupBy()"/>
                        </v-col>
                        <v-col :cols="(isByGroup)?'11':'12'">
                            <v-select
                                ref="byType"
                                :items="document_types"
                                item-text="name"
                                item-value="name"
                                v-model="selected_type"
                                label="Document Type"
                                dense
                                clearable
                                hide-selected
                                multiple
                                deletable-chips
                                chips
                                counter
                                :disabled="isByGroup && !group.byType"
                                :filled="isByGroup && !group.byType"
                            >
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
                <v-radio-group class="mx-4" v-model="wsTypeSel" row v-bind="bp(12)">
                    <v-row class="d-flex align-center">
                        <v-col cols="1" v-if="isByGroup">
                            <v-radio value="bySource" @click="groupBy()"/>
                        </v-col>
                        <v-col :cols="(isByGroup)?'11':'12'">
                            <v-select
                                ref="bySource"
                                :items="source_list"
                                v-model="source"
                                label="Document Source"
                                dense
                                clearable
                                hide-selected
                                multiple
                                deletable-chips
                                chips
                                counter
                                :disabled="isByGroup && !group.bySource"
                                :filled="isByGroup && !group.bySource"
                            >
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
                    <v-btn :disabled="advanceBtn" @click="download" color="primary" style="width:100%" elevation="4" depressed large>Export Custom Report</v-btn>
                </v-col>
            </v-row>
        </div>
    </div>
</template>

<script>

import { mapGetters, mapState } from 'vuex';
import { breakpoint } from '../../../constants';

export default {
    props: ['dialog_for'],
    data(){
        return {
            advanceBtn: true,
            selected_type: [],
            source: ['External', 'Internal'],
            source_list: ['External', 'Internal'],
            originating: [],
            wsType: 'single',
            wsTypeSel: 'byOffice',
            isByGroup: false,
            group: {
                byOffice: false,
                byType: false,
                bySource: false
            }
        }
    },
    watch: {
        'originating'(val){
            this.advanceBtn = this.disableBtn
            if(this.originating.length > 0) {
                if (!(val[val.length-1] instanceof Object)) {
                    this.originating.pop()
                }
            }
        },
        'source'(val){
            this.advanceBtn = this.disableBtn
        },
        'selected_type'(val) {
            this.advanceBtn = this.disableBtn
        },
        'wsType'(newVal){
            this.isByGroup = (newVal=='group')?true:false
        },
        'isByGroup'(newVal){
            if (newVal) {
                this.wsTypeSel = 'byOffice';
                this.group.byOffice = true
            } else {
                this.wsTypeSel = '';
            }
        }
    },
    computed: {
        ...mapGetters(['auth_user']),
        offices(){
            var data = JSON.parse(JSON.stringify(this.$store.state.offices.offices));
            return data
        },
        document_types(){
            var data = JSON.parse(JSON.stringify(this.$store.state.documents.document_types));
            return data
        },
        disableBtn(){
            return (this.originating.length < 1 && this.selected_type.length < 1 && this.source.length < 1)? true:false
        }
    },
    methods: {
        groupBy() {
            this.group.byOffice = (this.wsTypeSel == this.group.byOffice)?true:false
            this.group.byType = (this.wsTypeSel == this.group.byType)?true:false
            this.group.bySource = (this.wsTypeSel == this.group.bySource)?true:false
            this.group[this.wsTypeSel] = true
        },
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
            const archive = this.$store.state.documents.documentsArchive[0].selected
            const data = archive[archive.filter.toLowerCase()].data

            let selected = []
            let filter = {}
            Object.entries(this.group).forEach(s =>{
                if(s[1]==true){
                    selected.type = s[0]
                    selected.data = this.$refs[s[0]].value
                }
                if ((this.$refs[s[0]].value).length > 0) {
                    filter[s[0]] = this.$refs[s[0]].value
                }
            })

            import('./modules').then(({archiveae}) => {
                archiveae({
                    type: this.wsType,
                    data: data,
                    selected: selected,
                    filter: filter
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
        if (this.dialog_for == 'advanceExport') {
            let groupBy = this.auth_user.role_id === 1 ? ['byOffice', 'byType'] : ['byType']
            groupBy.forEach(b=>this.$refs[b].lastItem = 200)
            this.selected_type = this.document_types.map(t => t.name)
            this.originating = this.offices
        }
    }

}
</script>

<style>

</style>
