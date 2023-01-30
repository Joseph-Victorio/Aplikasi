<template>
  <div class="">
     <div id="courier" ref="courier" class="q-mb-lg">
      <div  v-if="config.can_shipping">
        <div class="text-md text-weight-medium ">Pilih Metode Pengiriman</div>

        <div class="q-py-sm q-gutter-x-sm">
          <q-radio v-model="shipping_method" val="EKSPEDISI" label="Via Ekspedisi"></q-radio>
          <q-radio v-model="shipping_method" val="COD" label="Via Kurir Toko ( COD )">
          </q-radio>
        </div>
      </div>
   
      <div id="shipping_destination" class="q-mt-md">
        <div v-if="shipping_method == 'EKSPEDISI' && config.can_shipping">

          <div id="shipping">
            <div class="text-md text-weight-medium ">Pilih Kecamatan Tujuan</div>
            <div class="q-mt-sm">
              <q-list v-if="formOrder.shipping_destination">
                <q-item class="bg-grey-2">
                  <q-item-section>{{ destinationAddressFormat(formOrder.shipping_destination) }}</q-item-section>
                  <q-item-section side>
                    <q-btn icon="cancel" round dense flat no-caps color="red" @click="clearAddress">
                    </q-btn>
                  </q-item-section>
                </q-item>
              </q-list>
              <div v-else>
                <q-input filled square placeholder="Ketik kecamatan tujuan, min 3 karakter" ref="search" v-model="searchSubdistrictKey" debounce="500" @input="findSubdistrict" :loading="isSearching"
                :error="errors.shipping_destination"
                >
                <!-- <template v-slot:error>Tujuan pengiriman belum diisi</template> -->
                </q-input>
                <transition
                    appear
                    enter-active-class="animated fadeIn"
                    leave-active-class="animated fadeOut"
                  >
                  <div class="relative bg-grey-1" v-show="isSearching || searchReady">
                    <q-list style="min-height:43px;max-height:300px;overflow-y:auto;" v-if="searchAvailable">
                      <q-item v-for="item in subdistrictOptionsData" :key="item.id" clickable @click="selectSubdistrict(item)">
                        <q-item-section>
                          <q-item-label>{{ destinationAddressFormat(item) }}</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                    <div v-else class="text-red-7 q-pa-md">kecamatan {{ searchSubdistrictKey }} tidak ditemukan</div>
                    <q-inner-loading :showing="isSearching"></q-inner-loading>
                  </div>
                </transition>
              </div>
            </div>
          
          </div>
          <div class="relative q-mt-md">
            <div class="text-md text-weight-medium q-pb-xs">Pilih Kurir</div>
           <q-select 
              id="inputCourier"
              filled
              square
              stack-label
              label="Pilih Kurir"
              :options="couriers"  
              v-model="currentSelelectedCourier" 
              :error="errors.shipping_courier_service"
              @input="selectCourier"
              >
              <template v-slot:error>Kurir belum dipilih</template>
            </q-select>

          </div>
          <q-list v-if="shippingCost.ready">
            <template v-if="shippingCost.costs.length">
            <q-item v-for="item in shippingCost.costs" :key="item.service" v-ripple @click="selectCost(item)" clickable class="bg-grey-1">
              <q-item-section avatar>
                <q-icon :name="isSelectedCost && isSelectedCost.service == item.service? 'radio_button_checked' : 'radio_button_unchecked'" :color="isSelectedCost && isSelectedCost.service == item.service? 'primary' : 'grey-6'"></q-icon>
              </q-item-section>
              <q-item-section>
                <q-item-label>Layanan : {{ item.service }}</q-item-label>
                <q-item-label>Ongkos kirim : {{ moneyIDR(item.cost[0].value)}}</q-item-label>
                <q-item-label caption>{{ item.description }} Estimasi {{ item.cost[0].etd }} day</q-item-label>
              </q-item-section>
            </q-item>
            </template>
            <q-item v-else>
              <q-item-section>
                <q-item-label class="text-red-5 q-pa-lg">Ongkos kirim tidak ditemukan, silahkan ganti dengan kurir yang lain</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
          <div ref="courier_skeleton">
          <q-list v-if="loading" >
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
        </div>
        <div v-if="shipping_method == 'COD' || !config.can_shipping">
          
        <div class="q-mb-lg">
            <div class="text-md q-pb-xs text-weight-medium q-mb-xs">Pilih Tujuan Pengiriman</div>
            <q-select filled v-model="codSelected" :options="listCodOptions" label="Pilih" 
            :error="errors.shipping_destination"
            >
            <template v-slot:error>Tujuan pengiriman belum diisi</template>
              <template v-slot:option="scope">
                <q-list separator>
                  <q-item 
                  v-bind="scope.itemProps"
                  v-on="scope.itemEvents"
                  >
                    <q-item-section>
                      <q-item-label>{{ scope.opt.label }}</q-item-label>
                      <q-item-label class="text-primary text-weight-bold text-sm"> Ongkos Kirim {{ scope.opt.price > 0 ? moneyIDR(scope.opt.price) : 'Gratis' }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
            </template>
            </q-select>
          </div>

        </div>
      </div>

    </div>

    <div id="customer" class="">
      <div class="text-md q-pb-xs text-weight-medium">Detail Penerima</div>
        <div class="q-gutter-y-md">
        <q-input
        filled
        square
        stack-label
        label="Nama Penerima"
        v-model="customer_name"
        :error="errors.customer_name"
        debounce="1000"
        >
        </q-input>
        <q-input
        v-if="canEmail"
        filled
        square
        stack-label
        type="email"
        required
        label="Alamat Email"
        v-model="customer_email"
         :error="errors.customer_email"
        debounce="1000"
        >
        </q-input>
        <q-input
          label="No ponsel / Whatsapp"
          filled
          square
          stack-label
          v-model="customer_phone"
          type="number"
          :error="errors.customer_phone"
          debounce="1000"
        >
        </q-input>
        <div>
          <q-input
            class="preline"
            label="Alamat Lengkap"
            type="textarea"
            filled
            square
            stack-label
            :error="errors.customer_address"
            v-model="customer_address"
          >
          </q-input>
        </div>
        </div>
    </div>
    
    <q-dialog v-model="useDataUserPrompt">
        <q-card style="max-width:400px;">
          <q-card-section>
            <div style="font-size:17px;font-weight:500;margin-bottom:6px;">Halo kak,</div>
            <div>Kami menemukan data alamat tersimpan dari order sebelumnya. Apakah akan menggunakan data tersebut?</div>
          </q-card-section>
          <q-card-actions class="justify-end">
            <q-btn size="12px" outline color="primary" label="Tidak" @click="closeModalAddress"></q-btn>
            <q-btn size="12px" unelevated color="primary" label="Ya Gunakan" @click="setDataUser"></q-btn>
          </q-card-actions>
        </q-card>
      </q-dialog>
  </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  name: 'ShippingAddress',
  props: {
    canEmail: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      costNotFound: false,
      readyAddressBlock: false,
      useDataUserPrompt: false,
      isSelectedCost: null,
      isSelectedCostCod: null,
      formGetCost: {
        origin: '',
        destination: '',
        weight: '',
        courier: '',
      },
      shippingCost: {
        code:'',
        name: '',
        costs: [],
        ready: false
      },
      address_street: '',
      userAddressData: {
        destination: '',
        address: ''
      },
      searchSubdistrictKey: '',
      isSearching: false,
      searchAvailable: true,
      searchReady:false,
      subdistrictOptionsData: [],
      codSelected: '',
      currentSelelectedCourier: null
    }
  },
  watch: {
    shipping_method: function(val) {
      if(val) {
        this.clearShipping()
        this.clearPayment()
        this.clearAddress()
        this.codSelected = ''
        this.isSelectedCostCod = ''
      }
    },
    codSelected: function(val) {
      if(val) {
        this.commitFormOrder('shipping_cost', val.price)
        this.commitFormOrder('shipping_courier_name', 'COD')
        this.commitFormOrder('shipping_courier_service', 'Diantar kurir toko')
        this.commitFormOrder('shipping_destination', val)
      }
    }
  },
  computed: {
    customer_address: {
      set: function(val) {
        this.commitFormOrder('customer_address', val)
      },
      get: function() {
        return this.$store.state.order.formOrder.customer_address
      }
    },
    customer_phone: {
      set: function(val) {
        this.commitFormOrder('customer_phone', val)
      },
      get: function() {
        return this.$store.state.order.formOrder.customer_phone
      }
    },
    customer_name: {
      set: function(val) {
        this.commitFormOrder('customer_name', val)
      },
      get: function() {
        return this.$store.state.order.formOrder.customer_name
      }
    },
    customer_email: {
      set: function(val) {
        this.commitFormOrder('customer_email', val)
      },
      get: function() {
        return this.$store.state.order.formOrder.customer_email
      }
    },
    shipping_method: {
      set: function(val) {
        this.commitFormOrder('shipping_method', val)
      },
      get: function() {
        return this.$store.state.order.formOrder.shipping_method
      }
    },
    listCodOptions() {
      if(this.config && this.config.can_cod) {
        return this.config.cod_list.map(el => ({ label: `${el.subdistrict_name} ${el.type} ${el.city} ${el.province}`, value: el.id, ...el}))
      }
      return []
    },
    canCod() {
      if(this.config && this.config.cod_list.length) {
        return true
      }
      return false
    },
    codListString() {
      if(this.canCod) {
        let list = this.config.cod_list.map(el => {
          return el.subdistrict_name
        })
        if(list.length > 1) {
          let first = list.slice(0, list.length -1).join(', ')

          return first + ' atau ' + list[list.length-1]

        }
        return list.join(', ')
      }
      return ''
    },
    errors() {
      return this.$store.state.errors
    },
    formOrder() {
      return this.$store.state.order.formOrder
    },
    user() {
      return this.$store.state.user.user
    },
    originAddressFormat() {
      return `${this.config.warehouse_address.city}, ${this.config.warehouse_address.province}`
    },
    couriers() {
      let n = [{label: 'Pilih', value: ''}]

      if(this.config) {
        n = [...n,...this.config.rajaongkir_couriers]
      }
      return n
      
    },
    config() {
      return this.$store.state.config
    },
    carts() {
      return this.$store.state.cart.carts
    },
    loading() {
      return this.$store.state.loading
    },
    canGetCost() {
      if(this.formGetCost.destination && this.formGetCost.courier && this.formGetCost.weight && this.formGetCost.origin){
        return true
      } else {
        return false
      }
    },
    codItem() {
      if(this.formOrder.shipping_destination) {
        if(this.config && this.config.cod_list && this.config.cod_list.length) {
          let h = this.config.cod_list.find(el => el.subdistrict_id == this.formOrder.shipping_destination.subdistrict_id)
          if(h != undefined) {
            return h
          } else {
            return null
          }
        } else {
          return null
        }

      } else {
        return null
      }
    },
    codAvailable() {
      let codStr = []
      if(this.config.cod_list.length){

        this.config.cod_list.forEach(eld => {
          codStr.push(`${eld.subdistrict_name}`)
        })
      }
      return codStr.join(', ')
    }
  },
  mounted() {
    this.setFormGetCost()
    if(this.user) {
      this.commitFormOrder('user_id', this.user.id)
      this.customer_name = this.user.name
      this.customer_email = this.user.email
      this.customer_phone = this.user.phone ? this.user.phone : ''
    }
    if(localStorage.getItem('_nex_user_data')) {
      if(!this.customer_name || !this.customer_phone) {
        this.useDataUserPrompt = true
      }
    }

    if(this.config && !this.config.can_shipping) {
      this.shipping_method = 'COD'
    }
  },
  methods: {
    clearShipping() {
      this.commitFormOrder('shipping_courier_name', '')
      this.commitFormOrder('shipping_courier_service', '')
      this.commitFormOrder('shipping_cost', 0)
      // this.commitFormOrder('shipping_destination', '')
    },
    clearPayment() {
      this.commitFormOrder('payment_method', '')
      this.commitFormOrder('payment_name', '')
      this.commitFormOrder('payment_type', '')
      this.commitFormOrder('payment_code', '')
      this.commitFormOrder('payment_fee', 0)
      this.$emit('removePayment')
    },
    commitFormOrder(key,val) {
      this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val })

      this.saveDataUser()
    },
    destinationAddressFormat(obj) {
      return `${obj.subdistrict_name} - ${obj.type} ${obj.city}, ${obj.province}`
    },
    selectCostCod(item) {
      this.isSelectedCost = null

      this.commitFormOrder('shipping_courier_name', 'COD')
      this.commitFormOrder('shipping_courier_service', 'COD')
      this.commitFormOrder('shipping_cost', item.price? parseInt(item.price) : 0)

      this.isSelectedCostCod = item

    },
    selectCost(item) {

      this.isSelectedCostCod = null
      this.isSelectedCost = item

      this.commitFormOrder('shipping_courier_name', this.shippingCost.name)
      this.commitFormOrder('shipping_courier_service', item.service)
      this.commitFormOrder('shipping_cost', item.cost[0].value)

    },
    changeNewAddress() {
      this.clearAddress()
      this.formGetCost.courier = ''
      this.formGetCost.destination = ''
      this.readyAddressBlock = false
      this.clearSelectedCost()
    },
    closeModalAddress() {
      this.changeNewAddress()
      this.useDataUserPrompt = false
      this.$emit('closeModal')
    },
    clearAddress() {
      this.currentSelelectedCourier = null
      this.searchSubdistrictKey = '';
      this.subdistrictOptionsData = []
      this.searchReady = false
      this.formGetCost.destination = ''
      this.formGetCost.courier = ''
      this.clearSelectedCost()

      this.commitFormOrder('shipping_destination', '')

    },
    selectSubdistrict(item) {

      this.commitFormOrder('shipping_destination', item)
      this.searchSubdistrictKey = ''

      this.formGetCost.origin = this.config.warehouse_address.city_id
      this.formGetCost.destination = item.city_id
      this.formGetCost.weight = this.formOrder.weight

      this.userAddressData.destination = item

      if(this.config.rajaongkir_type == 'pro') {
        
        this.formGetCost.origin = this.config.warehouse_address.subdistrict_id
        this.formGetCost.destination = item.subdistrict_id
        this.formGetCost.destinationType = 'subdistrict'
        this.formGetCost.originType = 'subdistrict'

      }

      this.getCost()
    },
    findSubdistrict() {
      this.subdistrictOptionsData = []
      this.searchAvailable = true
      this.searchReady = false
      if(this.searchSubdistrictKey.length < 3) return
      this.isSearching = true
      Api().get('shipping/findSubdistrict/'+ this.searchSubdistrictKey)
      .then(response => {
        if(response.status == 200) {
          if(response.data.success) {

            this.subdistrictOptionsData = response.data.results
            this.searchAvailable = response.data.results.length ? true : false

          } else {
            this.$q.notify({
              type: 'negative',
              message: response.data.message
            })
          }
        }
      })
      .finally(() => {
         this.isSearching = false
         this.searchReady = true
      })
    },

    setDataUser() {
      
      let data = JSON.parse(localStorage.getItem('_nex_user_data'))

      if(this.config.can_shipping) {
        this.selectSubdistrict(data.shipping_destination)
      }

      this.customer_address = data.customer_address
      this.customer_name = data.customer_name
      this.customer_phone = data.customer_phone
      this.customer_email = data.customer_email ? data.customer_email : ''

      this.useDataUserPrompt = false
      this.readyAddressBlock = true

    },

    saveDataUser() {

      if(this.formOrder.customer_name
      && this.formOrder.customer_phone
      && this.formOrder.customer_address
      && this.formOrder.shipping_destination
      ) {

        let userData = {
          customer_name: this.formOrder.customer_name,
          customer_phone: this.formOrder.customer_phone,
          customer_email: this.formOrder.customer_email,
          customer_address: this.formOrder.customer_address,
          shipping_destination: this.formOrder.shipping_destination
        }
  
        localStorage.setItem('_nex_user_data', JSON.stringify(userData))

      }
    },
    selectCourier(evt) {
      if(!evt) {
        this.clearSelectedCost()
        this.formGetCost.courier = ''
      }
     
      if(evt == 'cod') {

       this.clearSelectedCost()

      } else {
        this.commitFormOrder('shipping_courier_name', this.currentSelelectedCourier.label)
        this.formGetCost.courier = this.currentSelelectedCourier.value

        this.getCost()
      }
    },
    clearSelectedCost() {
      this.shippingCost.code = ''
      this.shippingCost.name = ''
      this.shippingCost.costs = []
      this.shippingCost.ready = false
      this.isSelectedCost = null
      this.isSelectedCostCod = null

      this.clearShipping()
    },
    getCost() {
      this.setFormGetCost()

      if(this.shipping_method == 'COD') return

      this.shippingCost.ready = false
      this.costNotFound = false
      this.clearSelectedCost() 

      if(this.canGetCost) {

        this.jumpTo('shipping')

        this.$store.commit('SET_LOADING', true)
        Api().post('shipping/costs', this.formGetCost).then(response => {
          if(response.status == 200) {

            let data = response.data.results.data[0];
              this.shippingCost.code = data.code
              this.shippingCost.name = data.name
              this.shippingCost.costs = data.costs

            if(!data.costs.length) {
              this.costNotFound = true
            }
             this.$store.commit('SET_LOADING', false)
          }
        }).catch(() => {
          this.$store.commit('SET_LOADING', false)
          this.costNotFound = true
        })
        .finally(() => {
           this.shippingCost.ready = true
        })
      }
    },
    setFormGetCost() {
      this.formGetCost.weight = this.formOrder.weight;

      if(this.formOrder.shipping_destination) {
        this.formGetCost.destination = this.formOrder.shipping_destination.city_id
      }

      if(this.config && this.config.can_shipping){

        this.formGetCost.origin = this.config.warehouse_address.city_id

        if(this.config.rajaongkir_type == 'pro') {
          this.formGetCost.origin = this.config.warehouse_address.subdistrict_id
          this.formGetCost.destinationType = 'subdistrict'
          this.formGetCost.originType = 'subdistrict'

          if(this.formOrder.shipping_destination) {
            this.formGetCost.destination = this.formOrder.shipping_destination.subdistrict_id
          }
        }

      } 
    },
  }
}
</script>
