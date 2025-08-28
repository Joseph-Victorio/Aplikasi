<template>
   <div class="">
      <div id="courier" ref="courier" class="q-mb-lg">

         <div id="shipping_destination">
            <div flat bordered id="customer">
               <fieldset>
                  <legend class="q-mb-sm">Detail Penerima</legend>
                  <div class="q-gutter-y-md">
                     <q-input filled square stack-label label="Nama Penerima" v-model="customer_name"
                        :error="errors.customer_name" debounce="1000">
                     </q-input>
                     <q-input v-if="canEmail" filled square stack-label type="email" required label="Alamat Email"
                        v-model="customer_email" :error="errors.customer_email" debounce="1000">
                     </q-input>
                     <q-input label="No ponsel / Whatsapp" filled square stack-label v-model="customer_phone"
                        type="number" :error="errors.customer_phone" debounce="1000">
                     </q-input>
                  </div>
               </fieldset>
            </div>
            <div class="q-mt-md" flat bordered id="shipping">
               <fieldset>
                  <legend class="q-mb-sm">
                     <div>Tujuan Pengiriman</div>

                  </legend>
                  <div class="q-pa-md bg-grey-2">
                     <div v-if="formOrder.shipping_destination">
                        <q-badge class="q-mb-sm" color="green">{{ formOrder.shipping_destination.label }}</q-badge>
                        <div>{{ formOrder.shipping_destination.address_street }}</div>
                        <div>{{ formOrder.shipping_destination.address.label }}</div>
                        <q-btn no-caps label="Ganti Alamat" color="amber-10" unelevated size="sm" class="q-mt-sm"
                           @click="handleOpenAddressModal"></q-btn>
                     </div>
                     <div v-else class="justify-center">
                        <q-btn v-if="user_address.length" no-caps label="Pilih Alamat" color="teal" unelevated
                           class="full-width" @click="handleOpenAddressModal"></q-btn>
                        <div class="text-center" v-else>
                           <div class="text-md q-mb-md">Belum ada alamat tersimpan</div>
                           <q-btn no-caps label="Tambah Alamat" color="primary" unelevated
                              @click="handleAddAddress"></q-btn>
                        </div>
                     </div>

                  </div>
               </fieldset>
               <div class="text-red q-pa-xs text-xs" v-if="errors.shipping_destination">Pengiriman belum dipilih</div>
            </div>
            <div class="q-mt-md" flat bordered>
               <fieldset>
                  <legend class="q-mb-sm">Kurir & Ongkos Kirim</legend>

                  <div class="relative q-py-sm" style="max-height: 300px;overflow-y: auto;">

                     <q-list separator v-if="shippingCostReady">
                        <template v-if="render_shipping_costs.length">
                           <q-item v-for="(item, idx) in render_shipping_costs" :key="idx" v-ripple
                              @click="selectCost(item)" clickable class="bg-grey-1">
                              <q-item-section avatar>
                                 <q-icon
                                    :name="selected_cost && selected_cost.id == item.id ? 'radio_button_checked' : 'radio_button_unchecked'"
                                    :color="selected_cost && selected_cost.id == item.id ? 'primary' : 'grey-6'"></q-icon>
                              </q-item-section>
                              <q-item-section>
                                 <q-item-label class="text-weight-medium">
                                    {{ item.courier_name }}
                                 </q-item-label>
                                 <q-item-label>Biaya {{ moneyIDR(item.price) }}</q-item-label>
                                 <q-item-label class="text-grey-8">
                                    {{ item.courier_service_name }} - {{ item.courier_service_code }}
                                 </q-item-label>
                                 <q-item-label class="text-grey-8">
                                    Etd {{ item.duration }}
                                 </q-item-label>
                              </q-item-section>
                           </q-item>
                        </template>
                        <q-item v-if="costNotFound">
                           <q-item-section>
                              <q-item-label class="text-red-5 q-pa-lg">Ongkos kirim tidak ditemukan, silahkan hubungi
                                 admin</q-item-label>
                           </q-item-section>
                        </q-item>
                     </q-list>
                  </div>

                  <div ref="courier_skeleton">
                     <q-list v-if="loading">
                        <q-item v-for="i in 3" :key="i">
                           <q-item-section avatar top>
                              <div class="q-pa-sm">
                                 <q-skeleton width="20px" height="20px" class="round"></q-skeleton>
                              </div>
                           </q-item-section>
                           <q-item-section>
                              <q-skeleton type="text" width="80px"></q-skeleton>
                              <q-skeleton type="text" width="180px"></q-skeleton>
                              <q-skeleton type="text" width="110px"></q-skeleton>
                              <q-skeleton type="text" width="90px"></q-skeleton>
                           </q-item-section>
                        </q-item>
                     </q-list>
                  </div>
               </fieldset>

            </div>
         </div>

      </div>

      <q-dialog v-model="useDataUserPrompt">
         <q-card style="max-width:400px;">
            <q-card-section>
               <div style="font-size:17px;font-weight:500;margin-bottom:6px;">Halo kak,</div>
               <div>Kami menemukan data alamat tersimpan dari order sebelumnya. Apakah akan menggunakan data tersebut?
               </div>
            </q-card-section>
            <q-card-actions class="justify-end">
               <q-btn size="12px" outline color="primary" label="Tidak" @click="closeModalAddress"></q-btn>
               <q-btn size="12px" unelevated color="primary" label="Ya Gunakan" @click="setDataUser"></q-btn>
            </q-card-actions>
         </q-card>
      </q-dialog>
      <UserAddressForm autoSelectModal ref="userAddressForm" @onSelectAddress="handleSelectAddress" />

      <div id="mapView"></div>
   </div>
</template>

<script>
import { Api } from 'boot/axios'
import UserAddressForm from 'src/components/UserAddressForm.vue'
export default {
   name: 'ShippingAddress',
   components: { UserAddressForm },
   props: {
      canEmail: {
         type: Boolean,
         default: false
      },
   },
   data() {
      return {
         shippingCostReady: false,
         formAddressModal: false,
         addressModal: false,
         useDataUserPrompt: false,
         selected_cost: null,
         formGetCost: {
            origin: '',
            destination: '',
            weight: '',
         },
         shippingCosts: [],
         local_cost: null
      }
   },
   computed: {
      user_address() {
         return this.$store.getters['user/getAllAddress']
      },
      costNotFound() {
         if (this.shippingCostReady) {
            if (!this.render_shipping_costs.length) {
               return true
            }
         }
         return false
      },
      render_shipping_costs() {

         let data = [];

         if (this.formOrder.customer_address) {

            if (this.currentConfig.is_pic_order) {
               data.push({
                  id: 'PICKUP',
                  courier_code: "PICKUP",
                  courier_name: "Ambil Di Toko",
                  courier_service_name: "Diambil sendiri oleh pelanggan",
                  courier_service_code: "PICKUP",
                  price: 0,
                  type: 'other'
               })
            }
         }

         if (this.local_cost) {
            data = [...data, this.local_cost]

         }

         return [...data, ...this.shippingCosts];
      },
      customer_address: {
         set: function (val) {
            this.commitFormOrder('customer_address', val)
         },
         get: function () {
            return this.$store.state.order.formOrder.customer_address
         }
      },
      customer_phone: {
         set: function (val) {
            this.commitFormOrder('customer_phone', val)
         },
         get: function () {
            return this.$store.state.order.formOrder.customer_phone
         }
      },
      customer_name: {
         set: function (val) {
            this.commitFormOrder('customer_name', val)
         },
         get: function () {
            return this.$store.state.order.formOrder.customer_name
         }
      },
      customer_email: {
         set: function (val) {
            this.commitFormOrder('customer_email', val)
         },
         get: function () {
            return this.$store.state.order.formOrder.customer_email
         }
      },
      shipping_destination: {
         set: function (val) {
            this.commitFormOrder('shipping_destination', val)
         },
         get: function () {
            return this.$store.state.order.formOrder.shipping_destination
         }
      },
      errors() {
         return this.$store.state.errors
      },
      formOrder() {
         return this.$store.state.order.formOrder
      },
      loading() {
         return this.$store.state.loading
      },
      canGetCost() {
         if (this.formGetCost.destination && this.formGetCost.weight && this.formGetCost.origin) {
            return true
         } else {
            return false
         }
      },
   },
   mounted() {
      this.setFormGetCost()
      if (localStorage.getItem('_nextwalocaluser')) {
         if (!this.customer_name || !this.customer_phone) {
            this.useDataUserPrompt = true
         }
      }

   },
   methods: {
      handleAddAddress() {
         this.$refs.userAddressForm.handleAddAddress()
      },
      handleOpenAddressModal() {
         this.$refs.userAddressForm.handleOpenAddressModal()
      },
      removeSelectedAddress() {
         this.shipping_destination = null
         this.customer_address = ''
      },

      clearShipping() {
         this.commitFormOrder('shipping_courier_name', '')
         this.commitFormOrder('shipping_courier_service', '')
         this.commitFormOrder('shipping_cost', 0)
      },
      commitFormOrder(key, val) {
         this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val })

         this.saveDataUser()
      },
      selectCost(item) {

         this.selected_cost = item

         this.commitFormOrder('shipping_courier_name', item.courier_service_name)
         this.commitFormOrder('shipping_courier_service', item.courier_service_code)
         this.commitFormOrder('shipping_cost', item.price)

      },
      changeNewAddress() {
         this.clearAddress()
         this.formGetCost.destination = ''
         this.clearSelectedCost()
      },
      closeModalAddress() {
         this.changeNewAddress()
         this.useDataUserPrompt = false
         this.$emit('closeModal')
      },
      handleSelectAddress(item) {
         this.clearSelectedCost()
         if (!item) {
            this.shipping_destination = null
            this.customer_address = null
            this.clearAddress()

            return

         }
         this.shipping_destination = item
         let addr = `${item.address_street.toUpperCase()} \n${item.address.name}`

         this.customer_address = addr

         this.getCost()

         if (this.currentConfig.can_cod && item && item.coordinate && this.currentConfig.warehouse_coordinate) {
            this.getLocalCost(item.coordinate)
         }

      },
      clearAddress() {
         this.formGetCost.destination = ''
         this.clearSelectedCost()

         this.commitFormOrder('shipping_destination', '')

      },

      setDataUser() {

         let data = JSON.parse(localStorage.getItem('_nextwalocaluser'))

         this.customer_name = data.customer_name
         this.customer_phone = data.customer_phone
         this.customer_email = data.customer_email ? data.customer_email : ''

         this.useDataUserPrompt = false

      },

      saveDataUser() {

         if (this.formOrder.customer_name
            && this.formOrder.customer_phone
         ) {

            let userData = {
               customer_name: this.formOrder.customer_name,
               customer_phone: this.formOrder.customer_phone,
               customer_email: this.formOrder.customer_email,
            }

            localStorage.setItem('_nextwalocaluser', JSON.stringify(userData))

         }
      },
      clearSelectedCost() {
         this.local_cost = null
         this.shippingCosts = []
         this.selected_cost = null

         this.clearShipping()
      },
      getCost() {
         this.setFormGetCost()

         if (this.canGetCost) {

            this.jumpTo('shipping')

            this.$store.commit('SET_LOADING', true)
            Api().post('shipping/costs', this.formGetCost).then(response => {
               if (response.status == 200) {

                  this.shippingCosts = response.data.results;

                  this.$store.commit('SET_LOADING', false)
               }
            }).catch(() => {
               this.$store.commit('SET_LOADING', false)
            }).finally(() => {
               this.shippingCostReady = true
            })
         }
      },
      setFormGetCost() {
         this.formGetCost.weight = this.formOrder.weight;

         if (this.formOrder.shipping_destination) {
            this.formGetCost.destination = this.formOrder.shipping_destination.address.city_id
         }

         if (this.currentConfig && this.currentConfig.can_shipping) {

            this.formGetCost.origin = this.currentConfig.warehouse_address.city_id

         }
      },
      getLocalCost(coords) {

         if (!this.map) {
            this.map = L.map('mapView', {
               center: this.currentConfig.warehouse_coordinate,
            });
         }

         let distanceInMeter = this.map.distance(this.currentConfig.warehouse_coordinate, coords)

         let total_distance = (distanceInMeter / 1000).toFixed(1);

         // console.log('direct distance: in M', distanceInMeter);
         // console.log('direct distance: in KM', total_distance);

         let localCosts = this.currentConfig.local_shipping_costs

         // Get max distance from last item

         let lastItem = localCosts[localCosts.length - 1]

         let max_distance = parseInt(lastItem.end)

         if (total_distance > max_distance) {

            this.local_cost = null

            return
         }

         // Get Cost by current distance

         let selectedRule = null

         for (let i = 0; i < localCosts.length; i++) {

            let currentRule = localCosts[i]

            let radius = parseInt(currentRule.radius)

            if (total_distance > radius) {

               continue;

            } else {
               selectedRule = currentRule
               break;
            }
         }

         if (!selectedRule) {
            this.local_cost = null
            return
         }

         let ongkir = 0

         console.log('total_distance', total_distance);
         console.log('selectedRule', selectedRule);


         if (selectedRule.flat) {
            ongkir = parseInt(selectedRule.cost)
         } else {
            if (total_distance > 0) {
               ongkir = Math.round(total_distance * parseInt(selectedRule.cost))
            } else {
               ongkir = parseInt(selectedRule.cost)
            }
         }

         this.local_cost = {
            id: "COD",
            courier_code: "COD",
            courier_name: "Via Kurir Toko",
            courier_service_name: "Diantar oleh kurir toko",
            courier_service_code: "COD",
            price: ongkir,
            type: 'other'
         }

         this.shippingCostReady = true

      },
   }
}
</script>