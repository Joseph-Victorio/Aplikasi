<template>
  <q-page class="bg-white" padding>
    <q-header class="text-grey-9 bg-white box-shadow">
        <q-toolbar>
          <q-btn @click="handleBackButton"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
        </q-toolbar>
    </q-header>
    <div id="checkout" v-if="carts && carts.length" ref="top" class="q-pb-md">
      <q-stepper
        v-model="step"
        keep-alive
        flat
        color="primary"
        alternative-labels
        animated
        >
          <q-step
          :name="1"
          title="Pengiriman"
          :done="step > 1"
          icon="local_shipping"
          active-icon="local_shipping"
          done-icon="local_shipping"
          class="q-pa-none"
        >
          <shipping-address 
            canEmail 
            @removePayment="removePayment"
          />

        </q-step>

          <q-step
          :name="2"
          title="Pembayaran"
          :done="step > 2"
          icon="payments"
          active-icon="payments"
          done-icon="payments"
          >
         
          <payment-list 
            ref="paymentList" 
            :isCod="isCod" 
            :paymentSelected="paymentSelected" 
            :payments="paymentChanels" 
            @onSelectPayment="onSelectPayment"
          />
          </q-step>

          <q-step
          :name="3"
          title="Review"
          :done="step > 3"
          icon="playlist_add_check"
          active-icon="playlist_add_check"
          done-icon="playlist_add_check"
          >
         <review-order :payment="paymentSelected" :noPayment="isCantPaymentStep"/>
          </q-step>

      </q-stepper>
    </div>
    <q-inner-loading :showing="loading">
       
    </q-inner-loading>
    <div class="bg-white q-py-md q-gutter-y-sm column" :class="{'sticky-bottom': isStickyBottom }">
      <q-btn v-if="step != 3 " @click="next" no-caps unelevated label="Langkah Selanjutnya" color="primary"></q-btn>
      <q-btn :disable="loading" v-if="step == 3" @click="submitOrder" no-caps unelevated label="Proses Pesanan" color="primary"></q-btn>
    </div>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
import { mapActions } from 'vuex'
import ShippingAddress from './ShippingAddress.vue'
import PaymentList from './PaymentList.vue'
import ReviewOrder from './ReviewOrder.vue'
export default {
	components: { ShippingAddress, PaymentList, ReviewOrder },
  name: 'CheckoutPage',
  data () {
    return {
      step: 1,
      formLoading: false,
      paymentSelected: null,
      paymentChanels: {
        localbanks: [],
        paymentGateway: []
      },
    }
  },
  watch: {
    step: function() {
      setTimeout(() => {
        this.jumpTo('checkout')
      }, 300)
    }
  },
  computed: {
    formOrder() {
      return this.$store.getters['order/getFormOrder']
    },
    isOVO() {
      return this.formOrder.payment_method == 'OVO'
    },
    isCod() {
      return this.formOrder.shipping_courier_name == 'COD'
    },
    carts() {
        return this.$store.state.cart.carts
    },
    shop() {
        return this.$store.state.shop
      },
    config() {
        return this.$store.state.config
      },
    loading() {
      return this.$store.state.loading
      },
    isCodPayment() {
      return this.formOrder.payment_method == 'COD'
    },
    isCantPaymentStep() {
      return this.formOrder.shipping_courier_name == 'COD' || !this.config.is_payment_gateway? true : false
    },
    title() {
      if(this.step == 1) return 'Pengiriman'
      if(this.step == 2) return 'Pembayaran'
      if(this.step == 3) return 'Review Order'

      return 'Checkout'
    },
    isStickyBottom() {
      if(this.$q.platform.is.desktop) {
        return true

      }else {
        if(this.step == 2) {
          if(this.paymentSelected) {
            return true
          }
        }
        if(this.step == 3) {
          return true
        }

        return false
      }
      
    },
    session_id() {
      return this.$store.state.session_id
    },
    errors() {
      return this.$store.state.errors
    },
  },
  mounted() {
    if(!this.carts.length) {
      this.$router.push({ name: 'Cart'})
    }
    if(this.config && this.config.is_payment_gateway && ! this.paymentChanels.paymentGateway.length) {
      this.getPaymentChanel()
    }
    if(! this.paymentChanels.localbanks.length) {
      this.getLocalBanks()
    }
    this.collectOrder()
  },
  methods: {
    ...mapActions('order', ['storeOrder']),
    commitFormOrder(key, val) {
      this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val})
    },
    removePayment() {
      this.paymentSelected = null
    },
    onSelectPayment(item) {
      this.paymentSelected = item
    },
    collectOrder() {
      this.commitFormOrder('items', this.carts)
      this.commitFormOrder('subtotal', this.sumSubtotal())
      this.commitFormOrder('quantity', this.sumQty())
      this.commitFormOrder('weight', this.sumWeight())

      if(this.config.is_service_fee) {
        this.commitFormOrder('service_fee', this.config.service_fee)
      }

    },
    handleBackButton() {
      if(this.step > 1) {
        this.step -= 1
      } else {
        this.$router.push({ name: 'Cart'})
      }
    },
    getLocalBanks() {
      Api().get('getBanks').then(response => {
        if(response.status == 200) {
          this.paymentChanels.localbanks = response.data.results
        }
      })
    },
    getPaymentChanel() {
      Api().get('tripay/payment-chanel').then(response => {
        if(response.status == 200) {
          if(response.data.success) {
            this.paymentChanels.paymentGateway = response.data.data
          }
        }
      })
    },
    submitOrder() {
      this.$store.commit('SET_LOADING', true)
      this.storeOrder(this.formOrder)
      .then(response => {
          this.$store.commit('SET_LOADING', false)

          if(response.status == 200) {

            setTimeout(() => {
              this.$store.dispatch('cart/clearCart', this.session_id)
            }, 20000)

            this.$router.push({ name: 'UserInvoice', params: { order_ref: response.data.results.order_ref }, query: { pay: true }})

            this.sendMessageNotification(response.data.results.id)
          }
        })
        .catch(() => {
          this.ready = false
          this.$store.commit('SET_LOADING', false)
        })
    },
    sendMessageNotification(orderId) {
      setTimeout(() => {
        Api().post('notify-order', { order_id: orderId})
      },12000)
    },
    formatAddressCod(addr) {
      let arr = addr.split('<br>')
      return arr.join('\n')
    },
    directChekout(data) {

      let whatsappUrl = 'https://api.whatsapp.com'
      if(this.$q.platform.is.desktop) {
        whatsappUrl = 'https://web.whatsapp.com'
      }
      
      let whatsapp = this.formatPhoneNumber(this.shop.phone)

      let str = `Halo kak, saya mau order:\n\n`
    
      let items = data.items
      let numb = 1;
      items.forEach(el => {
        str +=  `*${numb}. ${el.name}*\n`
        if(el.note){
          str += `[${el.note}]\n`
        }
        str+= `Jumlah: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity*el.price)}\n\n`
        numb ++
      })

      str += `Subtotal: *${this.moneyIDR(data.order_subtotal)}*\nOngkir: *${this.moneyIDR(data.shipping_cost)}\nTotal: *${this.moneyIDR(data.order_total)}*\n------------------------\n\n*Nama:*\n ${data.customer_name} (${data.customer_whatsapp})\n\n*Alamat:*\n${this.formatAddressCod(data.shipping_address)}\n\n`

      str += `Metode Pembayaran: ${data.transaction.payment_name}\n\n`

      str += `Referensi Order:\n${this.getRoutePath(data.order_ref)}`

      let link = whatsappUrl+'/send?phone=' + whatsapp + '&text=' + encodeURI(str);

      window.open(link, '_blank');

    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    getRoutePath(ref) {
      let props = this.$router.resolve({ 
        name: 'UserInvoice',
        params: { order_ref: ref },
      });

      return location.origin + props.href;
    },
    next() {
      this.$store.commit('CLEAR_ERRORS')
        let validationCustomer = ['customer_name', 'customer_phone', 'customer_email', 'customer_address']
        let validationShipping = [ 'shipping_destination', 'shipping_courier_name', 'shipping_courier_service']
        let validationStep2 = ['payment_method']


        if(this.step == 1) {
          for(let x in this.formOrder) {

            if(validationCustomer.includes(x) || validationShipping.includes(x)) {
              if(!this.formOrder[x] || this.formOrder[x] == '') {
                this.$store.commit('PUSH_ERRORS', { key: x, value: true })
              }
            }
          }

        }
        if(this.step == 2) {
          for(let x in this.formOrder) {

             if(validationStep2.includes(x)) {
              if(!this.formOrder[x] || this.formOrder[x] == '') {
                this.$store.commit('PUSH_ERRORS', { key: x, value: true })
              }
            }
          }

        }

      if(Object.keys(this.errors).length > 0) {
        for(let i in this.errors) {
          if(i == 'shipping_destination') {
            this.jumpTo('shipping_destination')
            this.$q.notify({
              type: 'negative',
              message: 'Tujuan pengiriman belum diisi'
            })
            return
          }

          if(i == 'shipping_courier_service') {
            this.jumpTo('courier')
            this.$q.notify({
              type: 'negative',
              message: 'Kurir belum dipilih'
            })
            return
          }

          if(validationCustomer.includes(i)) {
            this.jumpTo('customer')
            this.$q.notify({
              type: 'negative',
              message: 'Data penerima belum lengkap'
            })
            return
          }

          if(i == 'payment_method') {
            this.$q.notify({
              type: 'negative',
              message: 'Metode pembayaran belum dipilih'
            })
            return
          }
        }
        return
      }
      this.step += 1
    },
    prev() {
      this.step -= 1
    },
    sumQty() {
      if(this.carts.length > 1) {
        let q = [];
        this.carts.forEach(el => {
          q.push(parseInt(el.quantity))
        });
        return q.reduce((a,b) => a + b)
      }
      return parseInt(this.carts[0].quantity)
    },
    sumSubtotal() {
      if(this.carts.length > 1) {
        let j = [];
        this.carts.forEach(el => {
          j.push(parseInt(el.quantity)*parseInt(el.price))
        });
        return j.reduce((a,b) => a + b)
      }
      return parseInt(this.carts[0].quantity) * parseInt(this.carts[0].price)
    },
    sumGrandTotal() {
      return this.sumSubtotal() + parseInt(this.formOrder.shipping_cost)

    },
    sumWeight() {
      if(this.carts.length > 1) {
        let w = [];
        this.carts.forEach(el => {
          w.push(parseInt(el.weight)*parseInt(el.quantity))
        });
        return w.reduce((a,b) => a + b)
      }
      return parseInt(this.carts[0].quantity) * parseInt(this.carts[0].weight)
    },
  },
  meta() {
    return {
      title: 'Checkout'
    }
  }
}
</script>
