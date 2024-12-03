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
                     <q-input label="Catatan Pesanan" type="textarea" rows="3" filled square stack-label
                        v-model="customer_note">
                     </q-input>
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
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50"
               width="20px" height="20px">
               <g id="surface1441897">
                  <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;"
                     d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 " />
               </g>
            </svg>
            <span class="q-ml-sm">
               {{ isOk ? 'Proses Order' : 'Lengkapi data untuk Order' }}
            </span>
         </q-btn>
      </q-footer>

   </div>
</template>

<script>
export default {
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
      if (!this.carts.items.length) {
         this.$router.back()
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

         this.$store.dispatch('order/storeOrder', this.formOrder).then(res => {
            if (res.status == 200) {
               let order = res.data.results

               setTimeout(() => {
                  this.$router.push({ name: 'UserInvoice', params: { order_ref: order.order_ref } })
               }, 500)
               this.redirectWhatsapp(order)
            }
         }).catch(() => {
            this.$q.loading.hide()
         }).finally(() => {
            setTimeout(() => {
               this.$store.commit('SET_LOADING', false)
            }, 3000)
         })


      },
      redirectWhatsapp(order) {

         let routeInvoiceLink = this.getRoutePath(order.order_ref)

         let whatsappUrl = 'https://api.whatsapp.com'
         if (this.$q.platform.is.desktop) {
            whatsappUrl = 'https://web.whatsapp.com'
         }

         let whatsapp = this.formatPhoneNumber(this.currentShop.phone)

         let str = `Halo kak, saya mau order:\n\n`

         let items = this.formOrder.items
         let numb = 1;
         items.forEach(el => {
            str += `*${numb}). ${el.name}*\n`
            if (el.note) {
               str += `${el.note}\n`
            }
            str += `Qty: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity * el.price)}\n\n`
            numb++
         })

         str += `Total: *${this.moneyIDR(this.formOrder.total)}*\n`
         str += `-----------------------------------\n\n`
         str += `*Nama:*\n ${this.formOrder.customer_name} (${this.formOrder.customer_phone})\n\n`
         str += `Ref Order: ${routeInvoiceLink}\n`

         if (this.formOrder.customer_note) {
            str += `\n\nCatatan:\n${this.formOrder.customer_note}`
         }

         let link = whatsappUrl + '/send?phone=' + whatsapp + '&text=' + encodeURI(str);

         setTimeout(() => {
            this.$q.loading.hide()
         }, 1000)

         setTimeout(() => {
            this.$store.dispatch('cart/clearCart', this.currentSessionId)
         }, 5000)
         window.open(link, '_blank');
      }
   }
}
</script>
