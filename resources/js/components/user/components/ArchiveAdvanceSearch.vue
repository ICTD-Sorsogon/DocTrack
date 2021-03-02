<template>
    <v-row class="d-flex">

        <v-col v-bind="bp(6)">
            <v-text-field v-model="advanceSearch.trackingId" label="Traking ID" class="mx-4" clearable/>
        </v-col>

        <v-col v-bind="bp(6)">
            <v-text-field v-model="advanceSearch.subject" label="Subject" class="mx-4" clearable/>
        </v-col>

        <v-col v-bind="bp(6)">
            <v-select class="mx-4" :items="advanceSearch.optionSource" v-model="advanceSearch.source" label="Document Source" dense clearable hide-selected multiple deletable-chips chips counter>
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

        <v-col v-bind="bp(6)">
            <v-select class="mx-4" :items="document_types" item-text="name" item-value="name" v-model="advanceSearch.type" label="Document Type" dense clearable hide-selected multiple deletable-chips chips counter>
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small  @click="select" :input-value="selected" close @click:close="removeSelectedChips('type', item.name)">
                                {{item.name}}
                            </v-chip>
                        </template>
                        <span >{{item.name}}</span>
                    </v-tooltip>
                </template>
            </v-select>
        </v-col>

        <v-col v-bind="bp(6)">
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

        <v-col v-bind="bp(6)">
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
                counter
            >
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeSelectedChips('destination', item)">
                                {{ item.office_code || item }}
                            </v-chip>
                        </template>
                        <span >{{item.name || item}}</span>
                    </v-tooltip>
                </template>
            </v-combobox>
        </v-col>

        <v-col v-bind="bp(6)">
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
                counter
            >
                <template v-slot:selection="{ attrs, item, parent, select, selected, index }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip color="primary" v-bind="attrs" v-on="on" small @click="select" :input-value="selected" close @click:close="removeSelectedChips('sender', item)">
                                {{ item.full_name || item }}
                            </v-chip>
                        </template>
                        <span >{{item.full_name || item}}</span>
                    </v-tooltip>
                </template>
            </v-combobox>
        </v-col>

        <v-col v-bind="bp(6)">
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
            <v-dialog persistent max-width="450px">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn class="mb-2 ma-1 pl-13 pr-13" color="#5D97C1" dark  v-bind="attrs" v-on="on">
                        <v-icon class="ma-1 mr-1">mdi-cancel</v-icon> CANCEL
                    </v-btn>
                </template>
                <template v-slot:default="dialog">
                    <v-card color="grey lighten-2">
                        <v-card-title class="headline">
                            <v-icon class="mr-2" size="30px">mdi-cancel</v-icon> Cancel
                        </v-card-title>
                        <v-card-text> Are you sure you want to cancel advanced search it will reset the search parameter! </v-card-text>
                        <v-card-actions>
                            <v-spacer/>
                            <v-btn text @click.prevent="dialog.value = false"> NO </v-btn>
                            <v-btn text @click.prevent="$emit('changeSearch')"> YES </v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
            </v-dialog>
            <v-btn color="primary" :dark="hasSearchParam" :disabled="!hasSearchParam" class="mb-2 ma-1 ml-0 mr-2 pl-13 pr-13" @click.stop="submitSearchParameter">
                <v-icon class="ma-1 mr-1">mdi-magnify</v-icon> SEARCH
            </v-btn>
        </v-col>

    </v-row>
</template>

<script>
import { mapGetters } from "vuex";
import { breakpoint } from '../../../constants';

export default {
    props: ['minimumYear'],
    data() {
        return {
            advanceSearch: {
                trackingId: '',
                subject: '',
                source: [],
                type: [],
                originating: [],
                destination: [],
                sender: [],
                dateCreated: null,
                optionSource: ['External', 'Internal'],
                optionDateCreated: false,
            }
        }
    },
    watch: {
        'advanceSearch.originating'(newVal) {
            newVal.forEach(currItem => {
                if (!this.offices.map(o=>o.id).includes(currItem.id)) {
                    this.advanceSearch.originating.pop()
                }
            })
        },
        'advanceSearch.destination'(newVal) {
            newVal.forEach(currItem => {
                if (!this.offices.map(o=>o.id).includes(currItem.id)) {
                    this.advanceSearch.destination.pop()
                }
            })
        },
        'advanceSearch.sender'(newVal) {
            newVal.forEach(currItem => {
                if (!this.all_users.map(u=>u.id).includes(currItem.id)) {
                    this.advanceSearch.sender.pop()
                }
            })
        }
    },
    computed: {
        ...mapGetters(['document_types', 'offices', 'all_users', 'auth_user']),
        hasSearchParam(){
            const {trackingId, subject, source, type, originating, destination, sender, dateCreated} = this.advanceSearch;
            let watchSearchParam =  (trackingId == null? '' : trackingId.trim() != '')? true:false ||
                                    (subject == null? '' : subject.trim() != '')? true:false ||
                                    source.length > 0 ||
                                    type.length > 0 ||
                                    originating.length > 0 ||
                                    destination.length > 0 ||
                                    sender.length > 0 ||
                                    dateCreated != null;
            return (watchSearchParam)? true:false
        }
    },
    methods: {
        removeSelectedChips(el, item){
            this.advanceSearch[el].splice(this.advanceSearch[el].indexOf(item), 1)
            this.advanceSearch[el] = [...this.advanceSearch[el]]
        },
        bp(col){
            return breakpoint(col)
        },
        submitSearchParameter(){
            if (this.hasSearchParam) {
                this.$emit('searchParameter', this.advanceSearch)
            } else {
                this.$store.dispatch('setSnackbar', {
                    title: 'Invalid!',
                    type: 'error',
                    message: 'Please input some parameter for search'
                })
            }
        }
    }
}
</script>

<style>

</style>
