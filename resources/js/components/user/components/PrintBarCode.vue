<template>
<v-dialog v-model="printDialog" persistent scrollable eager max-width="30%">
      <v-card>
        <v-container>
          <v-row class="d-flex justify-space-between">
            <v-card-title primary-title> Print Bar Code </v-card-title>
			<v-btn x-large color="gray" @click="$emit('closePrintDialog')" icon>
				<v-icon>mdi-close</v-icon>
			</v-btn>
          </v-row>
		<v-row class="row py-2 px-4">	
			<v-btn color="primary" @click="print">Print</v-btn>
		</v-row>
        </v-container>
        <v-card-text>
			<v-row>
			<v-col class="mt-5 d-flex justify-content-center" style="position: relative; box-shadow: 0px 0px 10px 0px grey; height: 70vh;">
				<img id="dummy" ref="dummy" src="/images/cbimage.jpg"/>
			</v-col>
			<v-col id="printPage">
				<img ref="barCode" id="barCode" ></img>
			</v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
</template>
<script>

import Draggable from 'draggable'
import jsbarcode from 'jsbarcode'

export default {
	props: ['printDialog', 'code'],
	data(){
		return {
			offsetX: '',
			offsetY: '',
			inset: 'auto  auto auto auto',
		}
	},
	computed:{
		x(){
			return this.$refs.dummy.parentElement.clientWidth
		},
		y(){
			return this.$refs.dummy.parentElement.clientHeight
		}
	},
	watch: {
		code(newState, oldSate) {
			if(newState.tracking_code) {
				jsbarcode(this.$refs.barCode, newState.tracking_code);
			}
		}
	},
	methods: {
		getRefs(e,a,b,c){ 
			e.isMoving = false
			console.log(e.css.x = 0)
		},
		print(){
			this.$refs.barCode.setAttribute('style', `width: 3in !important; height: 1in !important; inset: ${this.inset}; position: absolute`)	
			this.$htmlToPaper('printPage');
		},
		xy( x, y, x0, y0 ) {
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
		new Draggable( this.$refs.dummy,  { setCursor: true, limit: this.xy })
	},
}
</script>

<style>
#printPage {
	display: none;
}
</style>