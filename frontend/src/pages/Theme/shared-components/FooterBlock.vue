<template>
  <div class="footer q-px-sm q-py-lg bg-brand text-white">
    <div :class="{ 'row': is_desktop }">

      <div class="col">
        <div class="text-md text-weight-bold q-pa-sm">About Me</div>
        <q-list dense v-if="currentShop">
          <q-item v-if="currentShop.name">
            <q-item-section side>
              <q-icon color="white" name="eva-home-outline" />
            </q-item-section>
            <q-item-section>
              <q-item-section>
                <q-item-label class="text-weight-bold text-md">{{ currentShop.name }}</q-item-label>
              </q-item-section>
            </q-item-section>
          </q-item>
          <q-item v-if="currentShop.phone">
            <q-item-section side>
              <q-icon color="white" name="eva-phone-call-outline" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-weight-medium">{{ currentShop.phone }}</q-item-label>
            </q-item-section>
          </q-item>
          <q-item v-if="currentShop.address">
            <q-item-section side top>
              <q-icon color="white" name="eva-pin-outline" />
            </q-item-section>
            <q-item-section>
              <div class="" v-html="currentShop.address"></div>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
      <div class="col">

        <div class="text-md text-weight-bold q-pa-sm q-mt-sm">Navigation</div>
        <q-list dark dense>
          <!-- <q-item clickable @click="$router.push('/')">
            <q-item-section side top>
              <q-icon color="white" name="eva-home-outline" />
            </q-item-section>
            <q-item-section>
              Beranda
            </q-item-section>
          </q-item> -->
          <q-item clickable @click="$router.push({ name: 'ProductIndex' })">
            <q-item-section side top>
              <q-icon color="white" name="eva-shopping-bag-outline" />
            </q-item-section>
            <q-item-section>
              Produk Katalog
            </q-item-section>
          </q-item>
          <q-item clickable @click="$router.push({ name: 'Cart' })">
            <q-item-section side top>
              <q-icon color="white" name="eva-shopping-cart-outline" />
            </q-item-section>
            <q-item-section>
              Keranjang Belanja
            </q-item-section>
          </q-item>
          <q-item clickable @click="$router.push({ name: 'FrontPostIndex' })">
            <q-item-section side top>
              <q-icon color="white" name="eva-book-open-outline" />
            </q-item-section>
            <q-item-section>
              Artikel Blog
            </q-item-section>
          </q-item>
          <q-item clickable @click="toDashboard">
            <q-item-section side top>
              <q-icon color="white" name="eva-person-outline" />
            </q-item-section>
            <q-item-section>
              <span> {{ currentUser ? 'Dashboard' : 'Login' }}</span>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    is_desktop() {
      return this.$q.screen.width >= 800
    }
  },
  methods: {
    toDashboard() {
      if (this.currentUser) {
        if (this.currentUser.role == 'admin') {
          this.$router.push({ name: 'Settings' })
        } else {
          this.$router.push({ name: 'CustomerAccount' })
        }
      } else {
        this.$router.push({ name: 'Login' })

      }
    }
  }
}
</script>
