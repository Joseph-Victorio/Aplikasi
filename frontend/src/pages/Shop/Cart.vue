<template>
  <q-page class="bg-grey-2 q-pa-sm" :class="{ 'flex flex-center': !carts.items.length }">
    <q-header class="text-grey-9 bg-white">
      <q-toolbar>
        <q-btn :to="{ name: 'ProductIndex' }" flat round dense icon="eva-arrow-back" />
        <q-toolbar-title class="text-weight-bold brand">Keranjang Belanja</q-toolbar-title>
      </q-toolbar>
    </q-header>
    <q-card v-if="carts.items.length" class="q-mt-sm" flat bordered>
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
                  <q-img :src="cart.image_url" style="width:70px;height:70px;" class="img-thumbnail q-mr-sm"></q-img>
                  <div class="col overflow-hidden full-width" style="max-width:370px">
                    <q-item-label class="text-weight-medium text-md ellipsis-2-lines">{{ cart.name }}</q-item-label>
                    <div class="text-grey-7 text-caption" v-if="cart.note">{{ cart.note }}</div>
                    <div class="text-grey-9 text-no-wrap q-mt-xs">{{ cart.quantity }}X {{ moneyIDR(cart.price) }}</div>
                    <div class="flex items-center q-mt-sm lt-sm">
                      <div class="q-gutter-x-sm items-center items-center row">
                        <q-btn unelevated color="grey-3" text-color="dark" padding="3px" icon="eva-minus-outline"
                          size="11px" @click="decrementQty(cart)"></q-btn>
                        <q-btn flat dense>{{ cart.quantity }}</q-btn>
                        <q-btn unelevated color="grey-3" text-color="dark" padding="3px" icon="eva-plus-outline"
                          size="11px" @click="incrementQty(cart)"></q-btn>
                      </div>
                    </div>
                  </div>

                </div>
              </td>
              <td class="gt-xs middle" align="middle">
                <div class="q-gutter-x-sm text-no-wrap row items-center justify-center no-wrap">
                  <q-btn unelevated color="grey-3" text-color="dark" padding="4px" icon="eva-minus-outline" size="11px"
                    @click="decrementQty(cart)"></q-btn>
                  <q-btn unelevated padding="4px">{{ cart.quantity }}</q-btn>
                  <q-btn unelevated color="grey-3" text-color="dark" padding="4px" icon="eva-plus-outline" size="11px"
                    @click="incrementQty(cart)"></q-btn>
                </div>
              </td>
              <td class="text-grey-10" align="right">
                <div class="text-weight-bold q-mb-sm text-no-wrap">{{ moneyIDR(cart.quantity * cart.price) }}</div>
                <q-btn @click="removeCart(cart)" outline size="10px" color="red" no-caps padding="2px 6px">remove</q-btn>
              </td>
            </tr>
          </table>
        </div>

      </q-card-section>
    </q-card>
    <div v-if="!carts.items.length" class="column items-center">
      <p class="text-grey-8 text-weight-bold text-center">Keranjang belanja anda masih kosong!</p>
      <q-btn unelevated :to="{ name: 'ProductIndex' }" rounded text-color="white" color="primary" icon="eva-arrow-back"
        label="kembali berbelanja" no-caps />
    </div>
    <q-footer v-if="isCanCheckoutDirectWithShipping" class="bg-white">
      <div class="row item-stretch">


        <div class="cart-total col-auto bg-white" style="min-width:30%">
          <div class="cart-label text-grey-10 text-weight-bold">Total</div>
          <div class="cart-amount text-weight-bold text-secondary">{{ moneyIDR(carts.subtotal) }}</div>
        </div>

        <div class="col bg-primary q-px-xs flex item-stretch">

          <q-btn unelevated @click="checkout" color="primary" class="full-width cart-button" no-caps size="lg">
            <!-- <svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#"
              xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg"
              xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20" width="20" id="svg2" version="1.1">
              <defs id="defs6" />
              <path id="path817"
                d="M 6.829268,18.2927 C 6.206549,17.66998 6.208549,16.962199 6.834768,16.335948 7.295075,15.87564 7.299158,15.833489 6.895748,15.70635 4.7751346,15.038014 4.5526598,14.587213 4.3890898,10.627041 4.2795602,7.9752375 4.1370612,7.0800955 3.5418488,5.3048955 L 2.8262839,3.1707495 H 1.7964173 C 0.8348259,3.1707495 0,2.7173665 0,2.1951395 c 0,-0.557993 0.8368698,-0.97561 1.9550402,-0.97561 1.8447535,0 2.2088159,0.47497 3.7390088,4.878049 l 1.313822,3.780488 h 4.072009 4.072008 l 1.485653,-3.353659 1.485657,-3.353658 h 0.9384 c 0.516122,0 0.938402,0.04665 0.938402,0.103676 0,0.355734 -3.738563,8.3325965 -3.976041,8.4835585 -0.160739,0.102181 -2.405057,0.187463 -4.987374,0.189518 l -4.695122,0.0037 v 0.827563 c 0,0.529633 0.138893,0.880862 0.385803,0.97561 0.21219,0.08143 2.517068,0.148046 5.121951,0.148046 h 4.736149 v 0.97561 c 0,0.813008 -0.0813,0.97561 -0.487805,0.97561 h -0.487805 l 0.487805,0.487805 c 0.268293,0.268292 0.487805,0.707317 0.487805,0.975609 0,0.268293 -0.219512,0.707317 -0.487805,0.97561 -0.268293,0.268293 -0.707317,0.487805 -0.97561,0.487805 -0.628185,0 -1.463414,-0.835228 -1.463414,-1.463415 0,-0.268292 0.219512,-0.707317 0.487804,-0.975609 l 0.487805,-0.487805 H 11.463415 8.292683 l 0.487805,0.487805 c 0.268292,0.268292 0.487805,0.707317 0.487805,0.975609 0,0.628187 -0.83523,1.463415 -1.463415,1.463415 -0.268293,0 -0.707317,-0.219512 -0.97561,-0.487805 z M 8.04878,17.31709 c 0,-0.134146 -0.109756,-0.243902 -0.243902,-0.243902 -0.134146,0 -0.243902,0.109756 -0.243902,0.243902 0,0.134146 0.109756,0.243903 0.243902,0.243903 0.134146,0 0.243902,-0.109757 0.243902,-0.243903 z m 7.317074,0 c 0,-0.134146 -0.109756,-0.243902 -0.243903,-0.243902 -0.134146,0 -0.243902,0.109756 -0.243902,0.243902 0,0.134146 0.109756,0.243903 0.243902,0.243903 0.134147,0 0.243903,-0.109757 0.243903,-0.243903 z M 11.707317,6.0339515 V 5.1219685 H 9.756098 7.804878 v -0.97561 -0.975609 h 1.95122 1.951219 v -0.911983 -0.911983 l 1.399788,1.399788 1.399788,1.399787 -1.399788,1.399788 -1.399788,1.399788 z"
                style="fill:currentColor;stroke-width:0.24390244" />
            </svg> -->
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
