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
                            >
                                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-chip :color="group.byOC" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeSelectedChips('originating', item)">
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
                            >
                                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-chip :color="group.byTC" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeSelectedChips('selected_type', item.name)">
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
                            >
                                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-chip :color="group.bySC" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeSelectedChips('source', item)">
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
                bySource: false,
                byOC: 'primary',
                byTC: 'primary',
                bySC: 'primary'
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
            if (newVal == 'group') {
                this.group.byOC = 'primary'
                this.group.byTC = (this.auth_user.role_id ==1)?'':'primary';
                this.group.bySC = ''
            } else {
                this.group.byOC = 'primary'
                this.group.byTC = 'primary'
                this.group.bySC = 'primary'
            }
        },
        'isByGroup'(newVal){
            if (newVal) {
                if(this.auth_user.role_id == 1) {
                    this.wsTypeSel = 'byOffice';
                    this.group.byOffice = true
                } else {
                    this.wsTypeSel = 'byType';
                    this.group.byType = true
                }
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
            this.group.byOffice = false
            this.group.byType = false
            this.group.bySource = false
            this.group.byOC = (this.wsTypeSel=='byOffice')?'primary':''
            this.group.byTC = (this.wsTypeSel=='byType')?'primary':''
            this.group.bySC = (this.wsTypeSel=='bySource')?'primary':''

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
            let group = JSON.parse(JSON.stringify(this.group));
            ['byOC', 'byTC', 'bySC'].forEach(key => delete group[key]);
            if(this.auth_user.role_id != 1) delete group.byOffice;
            Object.entries(this.group).forEach(s =>{
                if(s[1]==true){
                    selected.type = s[0]
                    selected.data = this.$refs[s[0]].value
                }
                if ((this.$refs[s[0]].value).length > 0) {
                    filter[s[0]] = this.$refs[s[0]].value
                }
            })

            import('./modules/archiveae').then(({archiveae}) => {
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

            import('./modules/archiveml').then(({archiveml}) => {
                archiveml({
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
