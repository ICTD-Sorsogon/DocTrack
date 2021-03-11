<template>
<v-dialog v-model="printDialog" persistent scrollable eager :max-width="maxWidth">
<v-card class="pa-1">
	<v-container>
		<v-tabs v-model="tab" color="deep-purple accent-4" >
			<v-tab>Bar Code</v-tab>
			<v-tab>Routing Slip</v-tab>
			<v-btn x-large color="gray" class="ml-auto" @click="$emit('closeDialog')" icon>
				<v-icon>mdi-close</v-icon>
			</v-btn>
		</v-tabs>
	</v-container>
	<v-card-text>
	<v-row class="row ma-1">
		<v-btn color="primary" @click="print">Print</v-btn>
	</v-row>
	<v-tabs-items v-model="tab">
		<v-tab-item>
			<v-container fluid>
			<v-row>
			<v-col class="mt-1 d-flex justify-content-center ma-1" style="position: relative; box-shadow: 0px 0px 10px 0px grey; height: 60vh;">
				<img id="dummy" ref="dummy" src="/images/cbimage.jpg"/>
			</v-col>
			<v-col id="printPage">
				<img ref="barCode" id="barCode" ></img>
			</v-col>
          </v-row>
			</v-container>
		</v-tab-item>
		<v-tab-item>
		<main v-if="item" id="routeSlip">
<v-app >
<v-container fluid  class="hello">
	<v-row class="mt-4">
	<v-col cols="6">
		<h1>Routing Slip</h1>
		<div class="">
			<strong>Tracking Code:</strong> {{item.tracking_code}}
		</div>
		<div class="">
			<strong>Subject:</strong> {{item.subject}}
		</div>
		<div class="">
			<strong>Originating Office:</strong> {{item.originating_office}}
		</div>
		<div class="">
			<strong>Destination Office:</strong> <v-chip x-small v-for="office in item.destination" :key="office.office_code" >
				{{office.office_code}}
			</v-chip>
		</div>
		<div class="">
			<strong>Date Filed:</strong> {{dateFiled}}
		</div>
		<div class="">
			<strong>Remarks:</strong> {{item.remarks}}
		</div>
	</v-col>
	<v-col cols="6" right>
		<img :src="barCodeData" style="position: absolute; right: 0; width: 3in !important; height: 1in !important"/>
	</v-col>
	</v-row>
	<v-row class="d-flex">

	<v-simple-table class="flex-grow-1 mt-5 bordered pa-3">
		<template v-slot:default>
		<thead>
			<tr>
			<th class="text-left">
				Received By
			</th>
			<th class="text-left">
				Remarks
			</th>
			<th class="text-left">
				Signature
			</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="n in 7">
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
		</template>
	</v-simple-table>
	</v-row>
	</v-container>
</v-app>
</main>
		</v-tab-item>
	</v-tabs-items>
</v-card-text>
      </v-card>
    </v-dialog>
</template>
<script>

import Draggable from 'draggable'
import jsbarcode from 'jsbarcode'

export default {
	props: ['printDialog', 'item'],
	data(){
		return {
			barCodeData: null,
			tab: 0,
			x: null,
			y: null,
			offsetX: '',
			offsetY: '',
			dragObject: {},
			inset: 'auto  auto auto auto',
		}
	},
	computed:{
		dateFiled(){
			return new Date(this.item.created_at).toDateString()
		},
		maxWidth(){
			return this.tab ? '70%' : '30%'
		},

	},
	watch: {
		printDialog(newState, oldSate) {
			if(newState) {
				this.dragObject = new Draggable( this.$refs.dummy,  { setCursor: true, limit: this.xy })
				jsbarcode(this.$refs.barCode, this.item.tracking_code);
				this.barCodeData = this.$refs.barCode.src
			}
		}
	},
	methods: {
	getWindowSize(event) {
		this.x = this.$refs.dummy.parentElement.clientWidth
		this.y = this.$refs.dummy.parentElement.clientHeight
	},
	print(){
		let page = this.tab ? 'routeSlip' : 'printPage'
		if(!this.tab){
			this.$refs.barCode.setAttribute('style', `width: 1.5in !important; height: .5in !important; inset: ${this.inset}; position: absolute`)
		}
		this.$htmlToPaper(page);
	},
	xy( x, y, x0, y0 ) {
			this.getWindowSize()
			x = x > (this.x - 80)  ? this.x - 80 : x < 0 ? 0 : x
			y = y > (this.y - 50) ? this.y - 50 : y < 0 ? 0 : y
			let offsetX = String((x/(this.x - 80)) * 65)
			let offsetY = String((y/(this.y-50)) * 92)

			let top = offsetY == 92 ? 'auto' : offsetY + '%'
			let right= offsetX == 65 ? 0 : 'auto'
			let bottom= offsetY == 92 ? 0 : 'auto'
			let left= offsetX == 65 ? 'auto' : offsetX + '%'
			this.inset = `${top} ${right} ${bottom} ${left}`
			return { x: x, y: y };
		}
	},
	mounted() {
    this.$nextTick(function() {
		window.addEventListener('resize', this.getWindowSize)

		this.getWindowSize()
	})

	},
	beforeDestroy() {
		window.removeEventListener('resize', this.getWindowSize);
  }
}
</script>

<style>
#printPage {
	display: none;
}

</style>