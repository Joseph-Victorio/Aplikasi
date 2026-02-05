<template>
   <div>
      <div id="checkout" v-if="carts && carts.items.length" ref="top" class="q-pb-xl">
         <div class="">
            <div flat bordered id="customer">
               <fieldset>
                  <legend class="q-mb-sm">Detail Pelanggan</legend>
                  <div class="q-gutter-y-sm">
                     <q-input filled square stack-label label="Nama" v-model="customer_name"
                        :error="errors.customer_name" error-message="nama belum diisi">
                     </q-input>
                     <q-input label="No ponsel / Whatsapp" filled square stack-label v-model="customer_phone"
                        mask="#### #### #####" unmasked-value :error="errors.customer_phone"
                        error-message="no whatsapp belum diisi">
                     </q-input>

                     <q-input label="Alamat" type="textarea" rows="3" filled square stack-label
                        v-model="customer_address">
                     </q-input>
                     <q-input label="Catatan Pesanan" type="textarea" rows="3" filled square stack-label
                        v-model="customer_note">
                     </q-input>
                     <!-- <q-input label="Email (opsional)" filled square stack-label v-model="customer_email" type="email">
                     </q-input> -->

                  </div>
               </fieldset>
            </div>
            <fieldset class="q-mt-lg">
               <legend class="q-pa-sm">Ringkasan Order</legend>
               <div v-if="formOrder.items.length" class="q-mb-md">
                  <q-separator></q-separator>
                  <q-list separator>
                     <q-item class="bg-grey-1 q-px-xs" dense>
                        <q-item-section>
                           <q-item-label>Produk</q-item-label>
                        </q-item-section>
                        <q-item-section side>
                           <q-item-label>Subtotal</q-item-label>
                        </q-item-section>
                     </q-item>
                     <q-item v-for="cart in formOrder.items" :key="cart.id" class="q-px-xs">
                        <q-item-section avatar top>
                           <q-img :src="cart.image_url" style="width:50px;height:50px;"></q-img>
                        </q-item-section>
                        <q-item-section>
                           <div class="col">
                              <div class="text-weight-medium">{{ cart.name }}</div>
                              <div class="text-caption text-grey-7">{{ cart.note }}</div>
                              <div class="text-grey-7">{{ cart.quantity + 'X ' + moneyIDR(cart.price) }}</div>
                           </div>
                        </q-item-section>
                        <q-item-section side>
                           <q-item-label>{{ moneyIDR(cart.price * cart.quantity) }}</q-item-label>
                        </q-item-section>
                     </q-item>
                  </q-list>
               </div>
               <q-separator></q-separator>
               <div class="flex justify-end q-py-md">
                  <table class="table dense">
                     <tr style="border-bottom:1px solid">
                        <td align="right">Total Bayar</td>
                        <td align="right">:</td>
                        <td align="right">{{ moneyIDR(formOrder.total) }}</td>
                     </tr>
                  </table>
               </div>

            </fieldset>
         </div>
      </div>
      <q-footer class="bg-white q-pa-md">
         <q-btn @click="checkout" color="green-6" class="full-width q-mt-sm" no-caps :disable="stateLoading">
            <span class="q-ml-sm">
               {{ isOk ? 'Lanjut Ke Pembayaran' : 'Lengkapi data untuk Order' }}
            </span>
         </q-btn>
      </q-footer>
   </div>

</template>

<script>
export default {
   data() {
      return {
         showQris: false,
         qrisUrl: null
      }
   },

   computed: {

      carts() {
         return this.$store.getters['cart/getCarts']
      },
      formOrder() {
         return this.$store.getters['order/getFormOrder']
      },
      errors() {
         return this.$store.state.errors
      },
      isOk() {
         if (this.formOrder.customer_name
            && this.formOrder.customer_phone
         ) {
            return true
         }

         return false
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
      customer_address: {
         set(val) {
            this.commitFormOrder('customer_address', val)
         },
         get() {
            return this.$store.state.order.formOrder.customer_address
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
      customer_note: {
         set: function (val) {
            this.commitFormOrder('customer_note', val)
         },
         get: function () {
            return this.$store.state.order.formOrder.customer_note
         }
      },
   },
   mounted() {
      console.log('Mounted PaymentQris, order_ref:', this.$route.params.order_ref)

      if (!this.carts?.items?.length) {
         this.$router.replace({ name: 'Cart' })
         return
      }
      this.collectOrder()
      this.setDataUser()
   },
   methods: {
      collectOrder() {
         this.$store.commit('order/COLLECT_ORDER', this.carts)
      },
      commitFormOrder(key, val) {
         this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val })

         this.saveDataUser()
      },
      handleBackButton() {
         this.$router.push({ name: 'Cart' })
      },
      formatPhoneNumber(number) {
         let formatted = number.replace(/\D/g, '')

         if (formatted.startsWith('0')) {
            formatted = '62' + formatted.substr(1)
         }

         return formatted;
      },
      getRoutePath(orderRef) {
         let props = this.$router.resolve({
            name: 'UserInvoice',
            params: { order_ref: orderRef },
         });

         return location.origin + props.href;
      },
      saveDataUser() {

         if (this.formOrder.customer_name
            && this.formOrder.customer_phone
         ) {

            let userData = {
               customer_name: this.formOrder.customer_name,
               customer_phone: this.formOrder.customer_phone,
            }

            localStorage.setItem('_nextshop_wa_current_user', JSON.stringify(userData))

         }
      },
      setDataUser() {
         if (localStorage.getItem('_nextshop_wa_current_user')) {

            let data = JSON.parse(localStorage.getItem('_nextshop_wa_current_user'))

            this.customer_name = data.customer_name
            this.customer_phone = data.customer_phone
            this.customer_email = data.customer_email ? data.customer_email : ''
         }


      },
      formatAddressCod(addr) {
         let arr = addr.split('<br>')
         return arr.join('\n')
      },
      validateData() {
         this.$store.commit('CLEAR_ERRORS')
         let validationCustomer = ['customer_name', 'customer_phone']

         for (let x in this.formOrder) {
            if (validationCustomer.includes(x)) {
               if (!this.formOrder[x] || this.formOrder[x] == '') {
                  this.$store.commit('PUSH_ERRORS', { key: x, value: true })
               }
            }
         }
         if (Object.keys(this.errors).length > 0) {
            for (let i in this.errors) {

               if (validationCustomer.includes(i)) {
                  this.jumpTo('customer')
                  this.$q.notify({
                     type: 'negative',
                     message: 'Data penerima belum lengkap'
                  })
                  return false
               }

            }

            return false
         }
         return true
      },
      checkout() {
         if (!this.validateData()) return
         if (!this.isOk) return

         this.$store.commit('SET_LOADING', true)

         this.$q.loading.show({
            message: 'Order sedang diproses. Silahkan tunggu...',
         })

         this.$store.dispatch('order/storeOrder', this.formOrder)
            .then(res => {
               console.log(res)
               if (res.status === 200 && res.data.results?.order_ref) {
                  const orderRef = res.data.results.order_ref
                  console.log('Redirect ke QRIS:', orderRef)
                  this.$router.push({
                     name: 'Qris',
                     params: { order_ref: orderRef }
                  })
               } else {
                  this.$q.notify({ type: 'negative', message: 'Gagal membuat order' })
               }
            })
            .catch(err => {
               console.error(err)
               this.$q.notify({ type: 'negative', message: 'Gagal membuat order' })
            })
            .finally(() => {
               this.$q.loading.hide()
               this.$store.commit('SET_LOADING', false)
            })


      },
      // redirectWhatsapp(order) {

      //    let routeInvoiceLink = this.getRoutePath(order.order_ref)

      //    let whatsappUrl = 'https://api.whatsapp.com'
      //    if (this.$q.platform.is.desktop) {
      //       whatsappUrl = 'https://web.whatsapp.com'
      //    }

      //    let whatsapp = this.formatPhoneNumber(this.currentShop.phone)

      //    let str = `Halo kak, saya mau order:\n\n`

      //    let items = this.formOrder.items
      //    let numb = 1;
      //    items.forEach(el => {
      //       str += `*${numb}). ${el.name}*\n`
      //       if (el.note) {
      //          str += `${el.note}\n`
      //       }
      //       str += `Qty: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity * el.price)}\n\n`
      //       numb++
      //    })

      //    str += `Total: *${this.moneyIDR(this.formOrder.total)}*\n`
      //    str += `-----------------------------------\n\n`
      //    str += `*Nama:*\n ${this.formOrder.customer_name}\n`
      //    str += `*Whatsapp:*\n ${this.formOrder.customer_phone}\n`

      //    // if (this.formOrder.customer_email) {
      //    //    str += `*Email:*\n ${this.formOrder.customer_email}\n`
      //    // }
      //    if (this.formOrder.customer_note) {
      //       str += `Catatan Pembeli:\n${this.formOrder.customer_note}\n`
      //    }
      //    str += `\nRef Order: ${routeInvoiceLink}`


      //    let link = whatsappUrl + '/send?phone=' + whatsapp + '&text=' + encodeURI(str);

      //    setTimeout(() => {
      //       this.$q.loading.hide()
      //    }, 1000)

      //    setTimeout(() => {
      //       this.$store.dispatch('cart/clearCart', this.currentSessionId)
      //    }, 5000)
      //    window.open(link, '_blank');
      // }
   }

}
</script>
