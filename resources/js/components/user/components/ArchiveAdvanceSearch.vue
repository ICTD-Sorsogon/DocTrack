<template>
    <v-row class="d-flex">

        <v-col v-bind="bp(6)">
            <v-text-field v-model="advanceSearch.trackingId" @input="textboxChange" label="Traking ID" class="mx-4" clearable/>
        </v-col>
        <v-col v-bind="bp(6)">
            <v-text-field v-model="advanceSearch.search" @input="textboxChange" label="Subject" class="mx-4" clearable/>
        </v-col>
        <v-col v-bind="bp(6)">
            <v-select class="mx-4" :items="advanceSearch.optionSource" v-model="advanceSearch.source" @change="textboxChange" label="Document Source" dense clearable hide-selected multiple deletable-chips chips>
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeSourceChip(item)">
                                {{item}}
                            </v-chip>
                        </template>
                        <span >{{item}}</span>
                    </v-tooltip>
                </template>
            </v-select>
        </v-col>
        <v-col v-bind="bp(6)">
            <v-select class="mx-4" :items="document_types" item-text="name" item-value="name" v-model="advanceSearch.type" @change="textboxChange" label="Document Type" dense clearable hide-selected multiple deletable-chips chips>
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeTypeChip(item.name)">
                                {{item.name}}
                            </v-chip>
                        </template>
                        <span >{{item.name}}</span>
                    </v-tooltip>
                </template>
            </v-select>
        </v-col>
        <v-col v-bind="bp(6)">
            <!--<v-select class="mx-4" :items="offices" v-model="advanceSearch.originating" @change="textboxChange" label="Originating Office" dense/>-->
            <v-combobox
                v-model="advanceSearch.originating"
                :items="offices"
                item-text="name"
                clearable
                hide-selected
                persistent-hint
                label="Originating Office"
                chips
                required
                class="mx-4"
                dense
                multiple
            >
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeOriginatingChip(item)">
                                {{ item.office_code || item }}
                            </v-chip>
                        </template>
                        <span >{{item.name || item}}</span>
                    </v-tooltip>
                </template>
            </v-combobox>
        </v-col>
        <v-col v-bind="bp(6)">
            <!--<v-select class="mx-4" :items="keys" v-model="advanceSearch.destination" @change="textboxChange" label="Destination Office" dense/>-->
            <v-combobox
                v-model="advanceSearch.destination"
                :items="offices"
                item-text="name"
                clearable
                hide-selected
                persistent-hint
                label="Destination Office"
                chips
                multiple
                required
                class="mx-4"
                dense
            >
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeDestinationChip(item)">
                                {{ item.office_code || item }}
                            </v-chip>
                        </template>
                        <span >{{item.name || item}}</span>
                    </v-tooltip>
                </template>
            </v-combobox>
        </v-col>

        <v-col v-bind="bp(6)">
            <!--<v-text-field v-model="advanceSearch.sender" @input="textboxChange" label="Sender Name" class="mx-4"/>-->
            <v-combobox
                v-model="advanceSearch.sender"
                :items="all_users"
                item-text="full_name"
                clearable
                hide-selected
                persistent-hint
                label="Sender Name"
                chips
                multiple
                required
                class="mx-4"
                dense
            >
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeSenderChip(item)">
                                {{ item.full_name || item }}
                            </v-chip>
                        </template>
                        <span >{{item.full_name || item}}</span>
                    </v-tooltip>
                </template>
            </v-combobox>
        </v-col>
        <v-col v-bind="bp(6)">
            <!--<v-text-field v-model="advanceSearch.dateCreated" @input="textboxChange" label="Date Created" class="mx-4"/>-->
            <v-dialog
                ref="dialog_created"
                v-model="advanceSearch.optionDateCreated"
                :return-value.sync="advanceSearch.dateCreated"
                persistent
                width="290px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="advanceSearch.dateCreated"
                        label="Date Created"
                        prepend-icon=""
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        class="mx-4"
                        clearable
                    />
                </template>
                <v-date-picker
                    v-model="advanceSearch.dateCreated"
                    scrollable
                    :max="new Date().toISOString().slice(0,10)"
                    :min="minimumYear"
                >
                    <v-spacer/>
                    <v-btn text color="primary" @click="advanceSearch.optionDateCreated = false"> Cancel </v-btn>
                    <v-btn text color="primary" @click="$refs.dialog_created.save(advanceSearch.dateCreated)"> OK </v-btn>
                </v-date-picker>
            </v-dialog>
        </v-col>

        <v-col v-bind="bp(12)" align="right">
            <v-btn color="#5D97C1" dark class="mb-2 ma-1 pl-13 pr-13" @click="searchOption = 'normal'">
                <v-icon class="ma-1 mr-1">mdi-cancel</v-icon> CANCEL
            </v-btn>
            <v-btn color="primary" dark class="mb-2 ma-1 ml-0 mr-2 pl-13 pr-13" @click.stop="$emit('searchParameter', advanceSearch)">
                <v-icon class="ma-1 mr-1">mdi-magnify</v-icon> SEARCH
            </v-btn>
        </v-col>

    </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { breakpoint } from '../../../constants';
export default {
    props: [
        'minimumYear'
    ],
    data() {
        return {
            advanceSearch: {
                trackingId: '',
                subject: '',
                source: '',
                type: '',
                originating: '',
                destination: '',
                sender: '',
                dateCreated: null,
                optionSource: [
                    'External',
                    'Internal'
                ],
                optionDateCreated: false,
            },
        }
    },
    computed: {
        ...mapGetters(['document_types', 'offices', 'all_users', 'auth_user']),
    },
    methods: {
        removeOriginatingChip(item){
            this.advanceSearch.originating.splice(this.advanceSearch.originating.indexOf(item), 1)
            this.advanceSearch.originating = [...this.advanceSearch.originating]
        },
        removeDestinationChip(item){
            this.advanceSearch.destination.splice(this.advanceSearch.destination.indexOf(item), 1)
            this.advanceSearch.destination = [...this.advanceSearch.destination]
            //console.log(objIndex)
        },
        removeSourceChip(item){
            this.advanceSearch.source.splice(this.advanceSearch.source.indexOf(item), 1)
            this.advanceSearch.source = [...this.advanceSearch.source]
        },
        removeTypeChip(item){
            this.advanceSearch.type.splice(this.advanceSearch.type.indexOf(item), 1)
            this.advanceSearch.type = [...this.advanceSearch.type]
        },
        removeSenderChip(item){
            this.advanceSearch.sender.splice(this.advanceSearch.sender.indexOf(item), 1)
            this.advanceSearch.sender = [...this.advanceSearch.sender]
            //console.log(objIndex)
        },
        bp(col){
            return breakpoint(col)
        },
        textboxChange(value){
            /*var advanceSearch = {
                trackingId: '',
                subject: '',
                source: '',
                type: '',
                originating: '',
                destination: '',
                sender: '',
                dateCreated: ''
            }*/

            //console.log(value)
            //console.log(this.$refs)

            console.log(this.advanceSearch)

            //this.$refs.trackingId.value)
            //trackingId
            //console.log(`${this.searchOption} : ` + value)
        },
    },
    mounted() {
        //console.log(this.minimumYear)
    }
}
</script>

<style>

</style>