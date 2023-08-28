<template>
  <q-page class="bg-white" padding>
    <q-header class="text-grey-9 bg-white box-shadow">
      <q-toolbar>
        <q-btn @click="handleBackButton" flat round dense icon="eva-arrow-back" />
        <q-toolbar-title class="text-weight-bold brand">Pemesanan</q-toolbar-title>
      </q-toolbar>
    </q-header>
    <form @submit.prevent="submitOrder">
      <div id="checkout" v-if="carts && carts.length" ref="top" class="q-pb-md">
        <shipping-address canEmail @removePayment="removePayment" />
      </div>
      <q-inner-loading :showing="loading">

      </q-inner-loading>
      <div class="bg-white q-py-md q-gutter-y-sm column">
        <q-btn :disable="loading" no-caps unelevated label="Proses Pesanan" color="primary"></q-btn>
      </div>
    </form>
  </q-page>
</template>

<script>
import ShippingAddress from './ShippingAddress.vue'
export default {
  components: { ShippingAddress },
  name: 'CheckoutPage',
  data() {
    return {
      formLoading: false,
    }
  },
  computed: {
    formOrder() {
      return this.$store.getters['order/getFormOrder']
    },
    isCod() {
      return this.formOrder.shipping_courier_name == 'COD'
    },
    carts() {
      return this.$store.state.cart.carts
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
    session_id() {
      return this.$store.state.session_id
    },
    errors() {
      return this.$store.state.errors
    },
  },
  mounted() {
    if (!this.carts.length) {
      this.$router.push({ name: 'Cart' })
    }
    this.collectOrder()
  },
  methods: {
    commitFormOrder(key, val) {
      this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val })
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
    },
    handleBackButton() {
      this.$router.push({ name: 'Cart' })
    },

    submitOrder() {
      this.$store.commit('SET_LOADING', true)
      this.$store.dispatch('order/storeOrder', this.formOrder)
        .then(response => {
          this.$store.commit('SET_LOADING', false)

          if (response.status == 200) {

            setTimeout(() => {
              this.$store.dispatch('cart/clearCart', this.session_id)
            }, 20000)

            let order = response.data.results

            this.directChekout(order)

            this.$router.push({ name: 'UserInvoice', params: { order_ref: order.order_ref } })

          }
        })
        .catch(() => {
          this.ready = false
          this.$store.commit('SET_LOADING', false)
        })
    },
    formatAddressCod(addr) {
      let arr = addr.split('<br>')
      return arr.join('\n')
    },
    directChekout(data) {

      let whatsappUrl = 'https://api.whatsapp.com'
      if (this.$q.platform.is.desktop) {
        whatsappUrl = 'https://web.whatsapp.com'
      }

      let whatsapp = this.formatPhoneNumber(this.currentShop.phone)

      let str = `Halo kak, saya mau order:\n\n`

      let items = data.items
      let numb = 1;
      items.forEach(el => {
        str += `*${numb}. ${el.name}*\n`
        if (el.note) {
          str += `[${el.note}]\n`
        }
        str += `Jumlah: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity * el.price)}\n\n`
        numb++
      })

      str += `Subtotal: *${this.moneyIDR(data.order_subtotal)}*\nOngkir: *${this.moneyIDR(data.shipping_cost)}\nTotal: *${this.moneyIDR(data.order_total)}*\n------------------------\n\n*Nama:*\n ${data.customer_name} (${data.customer_phone})\n\n*Alamat:*\n${this.formatAddressCod(data.shipping_address)}\n\n`

      str += `Referensi Order:\n${this.getRoutePath(data.order_ref)}`

      let link = whatsappUrl + '/send?phone=' + whatsapp + '&text=' + encodeURI(str);

      window.open(link, '_blank');

    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g, '')

      if (formatted.startsWith('0')) {
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
    sumQty() {
      if (this.carts.length > 1) {
        let q = [];
        this.carts.forEach(el => {
          q.push(parseInt(el.quantity))
        });
        return q.reduce((a, b) => a + b)
      }
      return parseInt(this.carts[0].quantity)
    },
    sumSubtotal() {
      if (this.carts.length > 1) {
        let j = [];
        this.carts.forEach(el => {
          j.push(parseInt(el.quantity) * parseInt(el.price))
        });
        return j.reduce((a, b) => a + b)
      }
      return parseInt(this.carts[0].quantity) * parseInt(this.carts[0].price)
    },
    sumGrandTotal() {
      return this.sumSubtotal() + parseInt(this.formOrder.shipping_cost)

    },
    sumWeight() {
      if (this.carts.length > 1) {
        let w = [];
        this.carts.forEach(el => {
          w.push(parseInt(el.weight) * parseInt(el.quantity))
        });
        return w.reduce((a, b) => a + b)
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
