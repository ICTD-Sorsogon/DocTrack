<template>
    <v-card>
        <v-container>
            <h1 style="margin-bottom:20px"> Email</h1>
            <v-combobox
                ref="office_email"
                v-model="emails.selected_offices"
                :items="offices"
                item-text="name"
                clearable
                hide-selected
                persistent-hint
                label="Office Lists"
                chips
                required
                dense
                multiple
                counter
            >
                <template
                    v-slot:selection="{
                        item,
                        select,
                        selected
                    }"
                >
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip
                                color="primary"
                                v-bind="attrs"
                                v-on="on"
                                small
                                @click="select"
                                :input-value="selected"
                                close
                                @click:close="
                                    removeSelectedChips('originating', item)
                                "
                            >
                                {{ item.office_code || item }}
                            </v-chip>
                        </template>
                        <span>{{ item.name || item }}</span>
                    </v-tooltip>
                </template>
            </v-combobox>

            <v-text-field
            v-model="emails.title"
            label="Title"
            hide-details="auto"
            ></v-text-field>

            <v-textarea
            v-model="emails.body"
            autocomplete="email"
            label="Message"
            ></v-textarea>

            <div class="text-end">
                <v-btn
                color="primary"
                @click="send_email">
                    Send
                </v-btn>
            </div>

        </v-container>
    </v-card>
</template>

<script>
import { mapGetters } from "vuex";
export default {

    data(){
        return {
            emails: {
                selected_offices: [],
                body: '',
                title: '',
            }
        }
    },
    watch: {
        "emails.selected_offices"(newVal) {
            newVal.forEach(currItem => {
                if (!this.offices.map(o => o.id).includes(currItem.id)) {
                    this.emails.selected_offices.pop();
                }
            });
        },
    },
    computed:{
        ...mapGetters(["offices"]),
    },
    methods:{
        async send_email(){
            this.emails.selected_offices.forEach(element => {
                element.body = this.emails.body
                element.title = this.emails.title
            });

            await axios.post('api/send_email', this.emails)
            .then(
                this.emails.selected_offices = [],
                this.emails.body = '',
                this.emails.title = '',
            )

        },
        removeSelectedChips(el, item) {
            this.emails.selected_offices.splice(
                this.emails.selected_offices.indexOf(item),
                1
            );
            this.emails.selected_offices = [...this.emails.selected_offices];
        },
    },
    mounted() {
        this.$store.dispatch("unsetLoader");
        this.$store.dispatch("getOffices");
        this.$refs.office_email.lastItem = 200
    }
}
</script>

<style>

</style>
