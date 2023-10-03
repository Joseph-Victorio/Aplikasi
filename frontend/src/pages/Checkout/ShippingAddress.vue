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
              <q-input label="No ponsel / Whatsapp" filled square stack-label v-model="customer_phone" type="number"
                :error="errors.customer_phone" debounce="1000">
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
                <q-btn v-if="user_address.length" no-caps label="Pilih Alamat" color="teal" unelevated class="full-width"
                  @click="handleOpenAddressModal"></q-btn>
                <div class="text-center" v-else>
                  <div class="text-md q-mb-md">Belum ada alamat tersimpan</div>
                  <q-btn no-caps label="Tambah Alamat" color="primary" unelevated @click="handleAddAddress"></q-btn>
                </div>
              </div>

            </div>
          </fieldset>
          <div class="text-red q-pa-xs text-xs" v-if="errors.shipping_destination">Pengiriman belum dipilih</div>
        </div>
        <div class="q-mt-md" flat bordered>
          <fieldset v-if="courierAvailable">
            <legend class="q-mb-sm">Kurir</legend>
            <div>
              <q-select id="inputCourier" filled square stack-label label="Pilih Kurir" :options="couriers"
                v-model="currentSelelectedCourier" :error="errors.shipping_courier_service" @input="selectCourier">
                <template v-slot:error>Kurir belum dipilih</template>
              </q-select>

            </div>

            <q-list v-if="shippingCost.ready">
              <template v-if="shippingCost.costs.length">
                <q-item v-for="item in shippingCost.costs" :key="item.service" v-ripple @click="selectCost(item)"
                  clickable class="bg-grey-1">
                  <q-item-section avatar>
                    <q-icon
                      :name="isSelectedCost && isSelectedCost.service == item.service ? 'radio_button_checked' : 'radio_button_unchecked'"
                      :color="isSelectedCost && isSelectedCost.service == item.service ? 'primary' : 'grey-6'"></q-icon>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>
                      {{ item.description }} (<span class="text-weight-bold">{{ item.service }}</span>)
                    </q-item-label>
                    <q-item-label>Ongkos kirim {{ moneyIDR(item.cost[0].value) }}</q-item-label>
                    <q-item-label caption v-if="item.cost[0].etd">
                      Etd {{ item.cost[0].etd }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
              <q-item v-else>
                <q-item-section>
                  <q-item-label class="text-red-5 q-pa-lg">Ongkos kirim tidak ditemukan, silahkan ganti dengan kurir yang
                    lain</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
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

          <div v-else>
            <div class="q-pa-lg text-center">
              <!-- <div class="text-lg text-weight-bold">Mohon Maaf</div> -->
              <q-icon name="error" size="xl" color="red"></q-icon>
              <div class="text-md text-weight-bold text-red">Alamat tidak dalam jangkauan kurir</div>
              <div class="q-mt-md q-mb-xs">Berikut alamat yg didukung kurir kami</div>

              <q-list v-if="currentConfig.cod_list.length" dense separator bordered>
                <q-item v-for="(an, i) in currentConfig.cod_list" :key="i">
                  <q-item-section class="text-green-8">{{
                    destinationAddressFormat(an)
                  }} ({{ moneyIDR(an.price) }})</q-item-section>
                </q-item>
              </q-list>

            </div>
          </div>

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
    <UserAddressForm autoSelectModal ref="userAddressForm" @onSelectAddress="handleSelectAddress" />
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
      formAddressModal: false,
      addressModal: false,
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
        code: '',
        name: '',
        costs: [],
        ready: false
      },
      searchSubdistrictKey: '',
      isSearching: false,
      searchAvailable: true,
      searchReady: false,
      codSelected: '',
      currentSelelectedCourier: null,
    }
  },
  computed: {
    user_address() {
      return this.$store.getters['user/getAllAddress']
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
    listCodOptions() {
      if (this.canCod) {
        return this.currentConfig.cod_list.map(el => ({ label: `${el.subdistrict_name} ${el.type} ${el.city} ${el.province}`, value: el.id, ...el }))
      }
      return []
    },
    canCod() {
      if (this.currentConfig && this.currentConfig.can_cod) {
        return true
      }
      return false
    },
    errors() {
      return this.$store.state.errors
    },
    formOrder() {
      return this.$store.state.order.formOrder
    },
    originAddressFormat() {
      return `${this.currentConfig.warehouse_address.city}, ${this.currentConfig.warehouse_address.province}`
    },
    couriers() {
      let n = []

      if (this.currentConfig && this.currentConfig.can_shipping) {
        n = [...this.currentConfig.rajaongkir_couriers]
      }

      if (this.formOrder.shipping_destination && this.canCod) {
        const item = this.currentConfig.cod_list.find(el => el.id == this.formOrder.shipping_destination.address.id)
        if (item != undefined) {
          let price = parseInt(item.price) > 0 ? `Rp.${item.price}` : 'Gratis'
          n.unshift({ label: `Antar Kurir Toko (${price})`, value: 'COD', price: item.price })
        }
      }
      return n
    },
    courierAvailable() {
      if (this.formOrder.shipping_destination && !this.couriers.length) {
        return false
      }
      return true
    },
    loading() {
      return this.$store.state.loading
    },
    canGetCost() {
      if (this.formGetCost.destination && this.formGetCost.courier && this.formGetCost.weight && this.formGetCost.origin) {
        return true
      } else {
        return false
      }
    },
    codItem() {
      if (this.formOrder.shipping_destination) {
        if (this.currentConfig && this.currentConfig.cod_list && this.currentConfig.cod_list.length) {
          let h = this.currentConfig.cod_list.find(el => el.subdistrict_id == this.formOrder.shipping_destination.address.subdistrict_id)
          if (h != undefined) {
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
    }
  },
  mounted() {
    this.setFormGetCost()
    if (this.currentUser) {
      this.commitFormOrder('user_id', this.currentUser.id)
      this.customer_name = this.currentUser.name
      this.customer_email = this.currentUser.email
      this.customer_phone = this.currentUser.phone ? this.currentUser.phone : ''
    } else {

      if (localStorage.getItem('__nextshop_current_user')) {
        if (!this.customer_name || !this.customer_phone) {
          this.useDataUserPrompt = true
        }
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
    clearPayment() {
      this.commitFormOrder('payment_method', '')
      this.commitFormOrder('payment_name', '')
      this.commitFormOrder('payment_type', '')
      this.commitFormOrder('payment_code', '')
      this.commitFormOrder('payment_fee', 0)
      this.$emit('removePayment')
    },
    commitFormOrder(key, val) {
      this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val })

      this.saveDataUser()
    },
    destinationAddressFormat(obj) {
      if (!obj) {
        return ''
      }
      return `${obj.subdistrict_name} - ${obj.type} ${obj.city}, ${obj.province}`
    },
    selectCostCod(item) {
      this.isSelectedCost = null

      this.commitFormOrder('shipping_courier_name', 'COD')
      this.commitFormOrder('shipping_courier_service', 'COD')
      this.commitFormOrder('shipping_cost', item.price ? parseInt(item.price) : 0)

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
    handleSelectAddress(item) {
      if (!item) {
        this.shipping_destination = null
        this.customer_address = null
        this.clearAddress()

        return

      }
      this.shipping_destination = item
      let addr = `${item.address_street}\n${item.address.label}`

      this.customer_address = addr

      setTimeout(() => {

        if (this.couriers.length == 1) {
          this.currentSelelectedCourier = this.couriers[0]
          this.selectCourier(this.currentSelelectedCourier)
        }
      }, 300)


      this.clearSelectedCost()
      this.getCost()

    },
    clearAddress() {
      this.currentSelelectedCourier = null
      this.searchSubdistrictKey = '';
      this.searchReady = false
      this.formGetCost.destination = ''
      this.formGetCost.courier = ''
      this.clearSelectedCost()

      this.commitFormOrder('shipping_destination', '')

    },

    setDataUser() {

      let data = JSON.parse(localStorage.getItem('__nextshop_current_user'))

      this.customer_name = data.customer_name
      this.customer_phone = data.customer_phone
      this.customer_email = data.customer_email ? data.customer_email : ''

      this.useDataUserPrompt = false
      this.readyAddressBlock = true

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

        localStorage.setItem('__nextshop_current_user', JSON.stringify(userData))

      }
    },
    selectCourier(evt) {
      if (!evt) {
        this.clearSelectedCost()
        this.formGetCost.courier = ''
      }

      if (evt.value == 'COD') {

        this.clearSelectedCost()

        this.commitFormOrder('shipping_cost', evt.price)
        this.commitFormOrder('shipping_courier_name', 'COD')
        this.commitFormOrder('shipping_courier_service', 'Diantar kurir toko')

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

      this.shippingCost.ready = false
      this.costNotFound = false
      this.clearSelectedCost()

      if (this.canGetCost) {

        this.jumpTo('shipping')

        this.$store.commit('SET_LOADING', true)
        Api().post('shipping/costs', this.formGetCost).then(response => {
          if (response.status == 200) {

            let data = response.data.results.data[0];
            this.shippingCost.code = data.code
            this.shippingCost.name = data.name
            this.shippingCost.costs = data.costs

            if (!data.costs.length) {
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

      if (this.formOrder.shipping_destination) {
        this.formGetCost.destination = this.formOrder.shipping_destination.address.city_id
      }

      if (this.currentConfig && this.currentConfig.can_shipping) {

        this.formGetCost.origin = this.currentConfig.warehouse_address.city_id

        if (this.currentConfig.rajaongkir_type == 'pro') {
          this.formGetCost.origin = this.currentConfig.warehouse_address.subdistrict_id
          this.formGetCost.destinationType = 'subdistrict'
          this.formGetCost.originType = 'subdistrict'

          if (this.formOrder.shipping_destination) {
            this.formGetCost.destination = this.formOrder.shipping_destination.address.subdistrict_id
          }
        }

      }
    }
  }
}
</script>
 