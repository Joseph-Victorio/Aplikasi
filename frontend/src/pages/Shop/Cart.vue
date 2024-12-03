<template>
   <q-page class="bg-grey-2" :class="{ 'flex flex-center': !carts.items.length }">
      <q-header class="text-grey-9 bg-white">
         <q-toolbar>
            <q-btn :to="{ name: 'ProductIndex' }" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">Keranjang Belanja</q-toolbar-title>
         </q-toolbar>
      </q-header>
      <q-card v-if="carts.items.length" flat square>
         <q-card-section>
            <div>
               <table class="table bordered table-sm">
                  <tr class="item-header">
                     <th class="text-left">PRODUK</th>
                     <th class="gt-xs" align="middle">QTY</th>
                     <th side class="text-grey-10" align="right">SUBTOTAL</th>
                  </tr>
                  <tr v-for="cart in carts.items" :key="cart.sku">
                     <td>
                        <div class="row">
                           <q-img :src="cart.image_url" style="width:70px;height:70px;"
                              class="img-thumbnail q-mr-sm"></q-img>
                           <div class="col overflow-hidden full-width" style="max-width:370px">
                              <q-item-label class="text-weight-medium text-md ellipsis-2-lines">{{ cart.name
                                 }}</q-item-label>
                              <div class="text-grey-7 text-caption" v-if="cart.note">{{ cart.note }}</div>
                              <div class="text-grey-9 text-no-wrap q-mt-xs">{{ cart.quantity }}X {{ moneyIDR(cart.price)
                                 }}</div>
                              <div class="flex items-center q-mt-sm lt-sm">
                                 <div class="q-gutter-x-sm items-center items-center row">
                                    <q-btn unelevated color="grey-3" text-color="dark" padding="3px"
                                       icon="eva-minus-outline" size="11px" @click="decrementQty(cart)"></q-btn>
                                    <q-btn flat dense>{{ cart.quantity }}</q-btn>
                                    <q-btn unelevated color="grey-3" text-color="dark" padding="3px"
                                       icon="eva-plus-outline" size="11px" @click="incrementQty(cart)"></q-btn>
                                 </div>
                              </div>
                           </div>

                        </div>
                     </td>
                     <td class="gt-xs middle" align="middle">
                        <div class="q-gutter-x-sm text-no-wrap row items-center justify-center no-wrap">
                           <q-btn unelevated color="grey-3" text-color="dark" padding="4px" icon="eva-minus-outline"
                              size="11px" @click="decrementQty(cart)"></q-btn>
                           <q-btn unelevated padding="4px">{{ cart.quantity }}</q-btn>
                           <q-btn unelevated color="grey-3" text-color="dark" padding="4px" icon="eva-plus-outline"
                              size="11px" @click="incrementQty(cart)"></q-btn>
                        </div>
                     </td>
                     <td class="text-grey-10" align="right">
                        <div class="text-weight-bold q-mb-sm text-no-wrap">{{ moneyIDR(cart.quantity * cart.price) }}
                        </div>
                        <q-btn @click="removeCart(cart)" outline size="10px" color="red" no-caps
                           padding="2px 6px">remove</q-btn>
                     </td>
                  </tr>
               </table>
            </div>

         </q-card-section>
      </q-card>
      <div v-if="!carts.items.length" class="column items-center">
         <p class="text-grey-8 text-weight-bold text-center">Keranjang belanja anda masih kosong!</p>
         <q-btn unelevated :to="{ name: 'ProductIndex' }" rounded text-color="white" color="primary"
            icon="eva-arrow-back" label="kembali berbelanja" no-caps />
      </div>
      <q-footer v-if="isCanCheckoutDirectWithShipping" class="bg-white">
         <div class="row item-stretch">


            <div class="cart-total col-auto bg-white column" style="min-width:30%">
               <!-- <div class="cart-label text-grey-10 text-weight-bold">Total</div> -->
               <div class="cart-amount text-weight-bold text-secondary q-my-auto">{{ moneyIDR(carts.subtotal) }}</div>
            </div>

            <div class="col bg-primary q-px-xs flex item-stretch">

               <q-btn unelevated @click="checkout" color="primary" class="full-width cart-button" no-caps size="lg">
                  <span class="q-ml-sm">
                     Checkout {{ $q.screen.width > 700 ? 'Sekarang' : '' }}
                  </span>
               </q-btn>
            </div>


         </div>

      </q-footer>
   </q-page>
</template>

<script>
export default {
   name: 'CartPage',
   computed: {
      carts() {
         return this.$store.getters['cart/getCarts']
      },
      isCanCheckoutDirectWithShipping() {
         if (this.carts.items.length && this.currentShop.phone) {
            return true
            if (this.currentConfig.can_cod || this.currentConfig.can_shipping) {
               return true
            }
         }

         return false
      },
   },
   created() {
      if (!this.currentShop) {
         this.$store.dispatch('getShop')
      }
      // this.$store.dispatch('getConfig')
   },
   methods: {
      checkout() {
         this.$router.push({ name: 'Checkout' })
      },
      incrementQty(cart) {
         if (parseInt(cart.quantity) >= parseInt(cart.product_stock)) return
         let qty = parseInt(cart.quantity) + 1
         if (!this.currentSessionId) return
         this.$store.dispatch('cart/updateCart', {
            sku: cart.sku,
            quantity: qty,
            session_id: this.currentSessionId
         })
      },
      decrementQty(cart) {
         if (parseInt(cart.quantity) <= 1) return
         let qty = parseInt(cart.quantity) - 1
         if (!this.currentSessionId) return
         this.$store.dispatch('cart/updateCart', {
            sku: cart.sku,
            quantity: qty,
            session_id: this.currentSessionId
         })
      },
      removeCart(cart) {
         this.$store.dispatch('cart/removeCart', {
            sku: cart.sku,
            session_id: cart.currentSessionId
         })
      },
   },
   meta() {
      return {
         title: 'Keranjang Belanja'
      }
   }
}
</script>
