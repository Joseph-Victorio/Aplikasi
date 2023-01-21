<template>
  <q-page class="q-pb-xl">
    <q-header>
      <q-toolbar>
        <q-toolbar-title>
          Dashboard
        </q-toolbar-title>
         <q-btn :to="{ name: 'Home', query: { load: 'true'}}"
          flat icon-right="eva-arrow-forward" label="Lihat Toko" no-caps/>
      </q-toolbar>
    </q-header>

    <div class="overflow-x-hidden relative">
      <q-list separator>
        <q-item clickable v-ripple v-for="(item, i) in menus" :key="i" :to="{name: item.path}">
          <q-item-section avatar>
            <q-avatar :color="colors[i]" text-color="white" :icon="item.icon" size="md"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ item.label }}</q-item-label>
            <q-item-label caption lines="1">{{ item.caption }}</q-item-label>
            </q-item-section>
          <q-item-section side>
            <q-icon name="eva-chevron-right" />
          </q-item-section>
        </q-item>
        <q-item clickable v-ripple @click.prevent="logout">
          <q-item-section avatar>
            <q-avatar color="red" text-color="white" icon="logout" size="md"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>Logout</q-item-label>
            <q-item-label caption lines="1">Keluar Sesi</q-item-label>
            </q-item-section>
          <q-item-section side>
            <q-icon name="eva-chevron-right" />
          </q-item-section>
        </q-item>
      </q-list>
    </div>
    <!-- <q-footer class="bg-white q-pa-sm">
      <div class="text-xs text-grey-6 text-center">
        &copy; Copyright {{ getYear() }} Allrigts Reserved {{ shop.name }}
      </div>
    </q-footer> -->
  </q-page>
</template>

<script>
export default {
  data () {
    return {
      isShowen: false,
      colors: ['green', 'purple', 'blue', 'deep-orange', 'teal', 'amber-7', 'green', 'purple', 'blue', 'deep-orange', 'teal', 'amber-7', 'green', 'purple', 'blue', 'deep-orange', 'teal', 'amber-7' ],
      menus: [
        { label: 'Akun', caption: 'Pengaturan Akun', path: 'Account', icon: 'person', color: 'green'},
        { label: 'Toko', caption: 'Pengaturan Tampilan toko', path: 'Shop', icon: 'store', color: 'blue'},
        { label: 'Order', caption: 'Kelola pesanan', path: 'OrderIndex', icon: 'receipt', color: 'green'},
        { label: 'Produk', caption: 'Tambah, edit dan hapus produk', path: 'AdminProductIndex', icon: 'inventory_2', color: 'deep-orange'},
        { label: 'Promo', caption: 'Kelola produk promo', path: 'PromoIndex', icon: 'local_offer', color: 'blue-7'},
        { label: 'Kategori', caption: 'Kelola kategori produk', path: 'CategoryIndex', icon: 'category', color: 'amber-7'},
        { label: 'Slider', caption: 'Kelola slideshow', path: 'Slider', icon: 'view_carousel', color: 'teal'},
        { label: 'Block', caption: 'Kelola banner, partner dan featured', path: 'AdminBlockIndex', icon: 'space_dashboard', color: 'amber-7'},
        { label: 'Artikel', caption: 'Kelola Artikel / blog', path: 'AdminPostIndex', icon: 'article', color: 'deep-orange'},
        { label: 'Akun Bank', caption: 'Kelola Akun Bank', path: 'BankIndex', icon: 'account_balance', color: 'purple'},
        { label: 'Manage User', caption: 'kelola user', path: 'UserList', icon: 'group', color: 'teal'},
        { label: 'Produk Reviews', caption: 'Manage Reviews Produk', path: 'ReviewsIndex', icon: 'comment', color: 'purple'},
        { label: 'Pengaturan', caption: 'Pengaturan Website dan pengiriman', path: 'Config', icon: 'settings', color: 'blue'},
      ]
    }
  },
  computed: {
    shop() {
      return this.$store.state.shop
    }
  },
  methods: {
    getYear() {
      let date = new Date()
      return date.getFullYear()
    },
    go() {
      window.open('https://nextshop.my.id', '_blank')
    },
    logout() {
      this.$store.dispatch('user/logout')
    },
    exitApp() {
      this.$store.dispatch('user/exitApp', navigator)
    },
  }
}
</script>