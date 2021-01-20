<template>
<v-card-text>
	<v-data-table
		v-if="documents"
		:headers="headers"
		:items="extendedData"
		:page.sync="page"
		:items-per-page="itemsPerPage"
		item-key="id"
		hide-default-footer
		:loading="datatable_loader"
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
			/>
		</template>
		<template v-slot:[`item.tracking_code`] = "{ item }">
					<v-chip label dark :color="getTrackingCodeColor(item, item.document_type_id)" >
						{{ item.tracking_code }}
					</v-chip>
		</template>
		<template v-slot:[`item.view_more`]="{ item }">
			<td>
				<v-btn
					color="primary"
					icon
					@click.prevent="selectDoc(item.id)"
				>
					<v-icon>mdi-more</v-icon>
				</v-btn>
			</td>
		</template>
		<template  v-slot:expanded-item="{ headers, item }">
			<td :colspan="headers.length">
				<v-row>
					<v-col v-if="isEditable(item.originating_office)" cols="12" sm="3">
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
					<v-col cols="12" sm="3">
						<v-btn @click.prevent="redirectToReceivePage(item.id, 'receive')" text color="#FFCA28" block
						>
							<v-icon left>
                                mdi-email-receive-outline
							</v-icon>
							Receive
						</v-btn>
					</v-col>
					<v-col cols="12" sm="3">
						<v-btn
							link @click.prevent="redirectToReceivePage(item.id, 'forward')" text color="#9575CD" block
						>
							<v-icon left>
								mdi-email-send-outline
							</v-icon>
							Forward
						</v-btn>
					</v-col>
					<v-col cols="12" sm="3">
						<v-btn link @click.prevent="redirectToReceivePage(item.id, 'terminal')" text color="#F06292" block
						>
							<v-icon left>
								mdi-email-off-outline
							</v-icon>
							Terminal
						</v-btn>
					</v-col>
				</v-row>
			</td>
		</template>
	</v-data-table>
	<div class="text-center pt-2">
		<v-pagination
			v-model="page"
			:length="pageCount"
		></v-pagination>
	</div>
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
import { colors } from '../../../constants';
import {mapGetters} from 'vuex'

export default {
	components: {TableModal},
	props: ['documents', 'datatable_loader'],
	data() {
		return {
			activeDoc: null,
			search: '',
            page: 1,
            itemsPerPage: 25,
            expanded: [],
            headers: [
                { text: 'Tracking ID', value: 'tracking_code', sortable: false },
                { text: 'Subject', value: 'subject', sortable: false },
                { text: 'Source', value: 'is_external', sortable: false },
                { text: 'Type', value: 'document_type.name', sortable: false },
                { text: 'Originating Office', value: 'originating_office', sortable: false },
                { text: 'Destination Office', value: 'destination.name', sortable: false },
                { text: 'Sender', value: 'sender_name', sortable: false },
                { text: 'Date Filed', value: 'date_filed', sortable: false },
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
				doc.is_external = doc.is_external ? 'External' : 'Internal'
				doc.sender_name = doc.sender?.name ?? doc.sender_name
				doc.originating_office = doc.origin_office?.name ?? doc.originating_office
				return doc
			})
		},
		selected_document() {
			return this.extendedData.find(data=>data.id == this.activeDoc)
		},
		isAdmin() {
			return this.auth_user.role_id == 1
		}
	},
	methods: {
		closeDialog(){
			this.dialog = false
		},
		selectDoc(id){
			this.activeDoc = id
			this.dialog = true
		},
        redirectToReceivePage(id, type) {
            /**
            * TODO:
            * Save the document id or the document object to Vuex instead because the dynamic routing is messing
            * up the Vuex getter for auth_user creating a call for receive_document/auth_user which is non-existent
            **/
            if (this.$route.name !== "Receive Document") {
                this.$router.push({ name: "Receive Document" , params: {type:type, id: id}});
            }
        },
		isEditable(docOrigin) {
			return this.auth_user.office_id == docOrigin || this.isAdmin
		},
		getTrackingCodeColor(document, document_type_id) {
            // document.color = colors[document_type_id];
            return colors[document_type_id];
        },
	}

}
</script>

<style>

</style>