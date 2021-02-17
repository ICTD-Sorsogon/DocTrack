<template>
<v-card-text>
	<v-data-table
		v-if="documents"
		:headers="headers"
		:items="extendedData"
		:page.sync="page"
		:items-per-page="itemsPerPage"
		item-key="keys"
		:loading="datatable_loader"
        :sort-by="['priority_level']"
        :sort-desc="[false]"
		loading-text="Loading... Please wait"
		class="elevation-1"
		:search="search"
		:single-expand="false"
		:expanded.sync="expanded"
		show-expand
	>
		<template v-slot:top>
			<v-text-field
				v-model="search"
				label="Search"
				class="mx-4"
                prepend-inner-icon="mdi-magnify"
			/>
		</template>
		<template v-slot:[`item.tracking_code`] = "{ item }">
					<v-chip class='trackin' @click="$emit('print', item)" label dark :color="getTrackingCodeColor(item, item.document_type_id)" >
						{{ item.tracking_code }}
					</v-chip>
		</template>
        <template v-slot:[`item.priority_level`] = "{ item }">
					<v-chip class="uniform" dark :color="getPriorityLevelColor(item, item.priority_level)" >
						{{ item.prio_text }}
					</v-chip>
		</template>
		<template v-slot:[`item.view_more`]="{ item }">
			<td>
				<v-btn
					color="primary"
					icon
					@click.prevent="{selectDoc(item.id); dialog = true}"
				>
					<v-icon>mdi-more</v-icon>
				</v-btn>
			</td>
		</template>
		<template v-slot:[`item.destination`]="{ item }">
				<v-tooltip v-if="destination" :key="destination.office_code" v-for="destination in item.destination" top>
					<template v-slot:activator="{ on, attrs }">
						<v-chip color="primary" v-bind="attrs" v-on="on" :x-small="item.destination.length > 1" >
							{{destination.office_code}}
						</v-chip>
					</template>
					<span>{{destination.name}}</span>
				</v-tooltip>
		</template>
		<template  v-slot:expanded-item="{ headers, item }">
			<td :colspan="headers.length">
				<v-row class="d-flex justify-space-around">
					<v-col v-if="isEditable(item) && !Boolean(item.acknowledged)">
						<v-btn
							@click="$emit('editDocument', item.id)"
							text
							color="#26A69A"
							block
						>
							<v-icon left>
								mdi-pencil
							</v-icon>
							Edit
						</v-btn>
					</v-col>
					<v-col v-if="(incoming || item.DO_reciever ) && !Boolean(item.received)">
						<v-btn @click.prevent="redirectToReceivePage(item, 'receive')" text color="#FFCA28" block >
							<v-icon left>
                                mdi-email-receive-outline
							</v-icon>
							Receive
						</v-btn>
					</v-col>
					<v-col v-if="(incoming || item.DO_reciever ) && (isAdmin || Boolean(item.received)) && !item.multiple && !Boolean(item.forwarded)">
						<v-btn
							link @click.prevent="redirectToReceivePage(item, 'forward')" text color="#9575CD" block
						>
							<v-icon left>
								mdi-email-send-outline
							</v-icon>
							Forward
						</v-btn>
					</v-col>
					<v-col v-if="((isEditable(item) && Boolean(item.acknowledged) && Boolean(item.received)) ||  (!isAdmin && item.received)) && !item.forwarded">
						<v-btn link @click.prevent="redirectToReceivePage(item, 'terminal')" text color="#F06292" block
						>
							<v-icon left>
								mdi-email-off-outline
							</v-icon>
							Terminal
						</v-btn>
					</v-col>
                    <v-col v-if="isAdmin && !Boolean(item.acknowledged)">
						<v-btn link @click.prevent="redirectToReceivePage(item, 'acknowledge')" text color="#4CAF50" block
						>
							<v-icon left>
								mdi-email-check-outline
							</v-icon>
							Acknowledge
						</v-btn>
					</v-col>
                    <v-col v-if="isGO">
						<v-btn link @click.prevent="redirectToReceivePage(item, 'Change Date')" text color="#E65100" block
						>
							<v-icon left>
								mdi-calendar-edit
							</v-icon>
							Change Date
						</v-btn>
					</v-col>
                    <v-col v-if="incoming && Boolean(item.recieved) && !Boolean(item.forwarded)">
						<v-btn link @click.prevent="redirectToReceivePage(item, 'Hold or Reject')" text color="#F44336" block
						>
							<v-icon left>
								mdi-email-alert-outline
							</v-icon>
							Hold or Reject
						</v-btn>
					</v-col>
				</v-row>
			</td>
		</template>
	</v-data-table>

	<table-modal
		@closeDialog="closeDialog"
        :dialog="dialog"
        v-if="selected_document"
        :selected_document="selected_document"
    ></table-modal>
</v-card-text>
</template>

<script>
import TableModal from './TableModal';
import { colors, priority_level } from '../../../constants';
import {mapGetters} from 'vuex'

export default {
	components: {TableModal},
	props: ['documents', 'datatable_loader', 'incoming'],
	data() {
		return {
			activeDoc: null,
			search: '',
            page: 1,
            itemsPerPage: 10,
            expanded: [],
            headers: [
                { text: 'Tracking ID', value: 'tracking_code', sortable: false },
                { text: 'Subject', value: 'subject', sortable: false },
                { text: 'Source', value: 'is_external', sortable: false },
                { text: 'Type', value: 'document_type.name', sortable: false },
                { text: 'Originating Office', value: 'originating_office', sortable: false },
                { text: 'Destination Office', value: 'destination', sortable: false },
                { text: 'Sender', value: 'sender_name', sortable: false },
                { text: 'Priority Level', value: 'priority_level', sortable: false },
                { text: 'View More', value: 'view_more', sortable: false },
                { text: 'Actions', value: 'data-table-expand', sortable: false },
            ],
            dialog: false,
		}
	},
	computed: {
		...mapGetters(['auth_user']),
		pageCount() {
            return parseInt(this.documents?.length / this.itemsPerPage)
		},
		extendedData() {
			return JSON.parse(JSON.stringify( this.documents)).map(doc=>{
				doc.keys = doc.id + ' ' + (doc.recipient_id ?? '')
                doc.is_external = doc.is_external ? 'External' : 'Internal'
				doc.sender_name = doc.sender?.name ?? doc.sender_name
				doc.destination = doc.destination_office ? [doc.destination.find(destination => destination.id == doc.destination_office)] : doc.destination
                doc.originating_office = doc.origin_office?.office_code ?? doc.originating_office
                doc.prio_text = '';
				doc.DO_reciever = doc.destination.find(target => target.office_code == "DO")


				for(status of [ 'acknowledged', 'received', 'forwarded', 'rejected', 'hold']){
                  doc[status] = doc.recipient.every( recipient => recipient[status] )
                }

                if (doc.priority_level == 1) {
                    doc.prio_text = 'High'
                }
                else if(doc.priority_level == 2) {
                    doc.prio_text = 'Medium'

                }
                else if (doc.priority_level == 3)
                    doc.prio_text = 'Low'
                else
                    doc.prio_text = 'None'
				return doc
			})
		},
		selected_document() {
			return this.extendedData.find(data=>data.id == this.activeDoc)
		},
		isAdmin() {
			return this.auth_user.role_id == 1
		},
        isGO(){
            return this.auth_user.role_id == 3
        }
	},
	methods: {
		closeDialog(){
			this.dialog = false
		},
		selectDoc(id){
			this.activeDoc = id
		},
        redirectToReceivePage(item, type) {
            /**
            * TODO:
            * Save the document id or the document object to Vuex instead because the dynamic routing is messing
            * up the Vuex getter for auth_user creating a call for receive_document/auth_user which is non-existent
            **/
            if (this.$route.name !== "Receive Document") {
                this.$router.push({ name: "Receive Document" , params: {type:type, item: item, id: item.id}});
            }
        },
		isEditable(docOrigin) {
			return this.auth_user.office_id == docOrigin.origin_office?.id || this.isAdmin
		},
		getTrackingCodeColor(document, document_type_id) {
            // document.color = colors[document_type_id];
            return colors[document_type_id];
        },
        getPriorityLevelColor(document, type){
            return priority_level[type];
        }
	}

}
</script>

<style>
.uniform {
    width: 80px;
    justify-content: center;
}
.trackin {
    width: 250px;
    justify-content: center;
}
</style>