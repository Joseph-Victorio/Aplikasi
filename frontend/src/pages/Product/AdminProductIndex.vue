<template>
  <q-page>
     <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Produk
        </q-toolbar-title>
        <q-btn class="gt-xs" no-caps outline icon="eva-plus-circle" :to="{name: 'ProductCreate'}" label="Tambah Produk"/>
      </q-toolbar>
    </q-header>
    <div class="border q-pa-md row item-center q-gutter-x-sm">
      <div class="col">
        <q-input :loading="loading" ref="input" outlined dense color="grey-2" v-model="search" @keyup.enter="searchProduct" placeholder="ketik nama produk"
        >
        </q-input>
      </div>
        <q-btn unelevated label="Cari" @click="searchProduct" color="primary"></q-btn>
        <q-btn outline label="Reset" @click="getAdminProducts" color="primary"></q-btn>
      </div>
    <div class="q-px-md q-pb-sm text-md text-weight-bold">Produk</div>
    <q-separator></q-separator>
    <template v-if="products.available">
     <div class="q-pt-sm q-pb-xl">
      <q-list separator>
       <q-item v-for="product in products.data" :key="product.id">

         <q-item-section avatar class="q-pr-md" top>
           <q-img v-if="product.assets" :src="product.assets[0].src" class="bg-white img-product-admin" ratio="1" width="55px"/>
        </q-item-section>

        <q-item-section top>
          <div class="">
            <q-item-label lines="2" class="text-subtitle2">{{ product.title }}</q-item-label>
            
            <q-item-label caption>Harga Dasar {{ moneyIDR(product.price) }}</q-item-label>
            <q-item-label caption v-if="!product.varians.length">Stok {{ product.stock }}</q-item-label>

            <q-item-label>
              <q-slide-transition>
              <div v-show="showListId == product.id">
                <div class="text-weight-bold q-pa-xs q-mt-sm">Detil Varian</div>
                <div v-for="varian in product.varians" :key="varian.id" class="q-mb-xs">
                  <q-list v-if="varian.has_subvarian" dense bordered separator>
                    <q-item v-for="subvarian in varian.subvarian" :key="subvarian.id">
                      <q-item-section>{{ varian.label }} {{ varian.value }} </q-item-section>
                      <q-item-section>{{ subvarian.label }} {{ subvarian.value }}</q-item-section>
                      <q-item-section>Stok {{ subvarian.stock }}</q-item-section>
                      <q-item-section>Harga {{ moneyIDR(product.price+subvarian.price) }}</q-item-section>
                    </q-item>
                  </q-list>
                  <q-list v-if="!varian.has_subvarian" dense separator bordered>
                    <q-item>
                      <q-item-section>{{ varian.label }} {{ varian.value }} </q-item-section>
                      <q-item-section>Stok {{ varian.stock }}</q-item-section>
                      <q-item-section>Harga {{ moneyIDR(product.price+varian.price) }}</q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </q-slide-transition>
            </q-item-label>
            </div>
        </q-item-section>

        <q-item-section side top>
          <div v-if="!isDesktop">
            <q-fab color="primary" icon="eva-chevron-left" direction="left" glossy padding="sm" unelevated>
              <q-fab-action unelevated @click="remove(product.id)" round icon="eva-trash-2" glossy color="red">
                <q-tooltip content-class="bg-red">Hapus</q-tooltip>
              </q-fab-action>
              <q-fab-action unelevated :to="{ name: 'ProductEdit', params: {id: product.id }}" round glossy color="info" icon="eva-edit-2">
                <q-tooltip content-class="bg-info">Edit</q-tooltip>
              </q-fab-action>
              <q-fab-action unelevated :to="{ name: 'ProductShow', params: {slug: product.slug }}" round glossy color="teal" icon="eva-external-link-outline">
                <q-tooltip content-class="bg-teal">Lihat</q-tooltip>
              </q-fab-action>

              <q-fab-action v-if="product.varians.length" unelevated @click="selectVarian(product)" round icon="eva-pantone" glossy color="accent">
                <q-tooltip content-class="bg-accent">Detil Varian</q-tooltip>
              </q-fab-action>
            </q-fab>
          </div>
          <div class="row q-gutter-xs" v-if="isDesktop">
            <q-btn size="11px" v-if="product.varians.length" unelevated @click="selectVarian(product)" round icon="eva-pantone" glossy color="accent">
              <q-tooltip content-class="bg-accent">Detil Varian</q-tooltip>
            </q-btn>
            <q-btn size="11px" unelevated @click="remove(product.id)" round icon="eva-trash-2" glossy color="red">
              <q-tooltip content-class="bg-red">Hapus</q-tooltip>
            </q-btn>
            <q-btn size="11px" unelevated :to="{ name: 'ProductEdit', params: {id: product.id }}" round glossy color="info" icon="eva-edit-2">
              <q-tooltip content-class="bg-info">Edit</q-tooltip>
            </q-btn>
            <q-btn size="11px" unelevated :to="{ name: 'ProductShow', params: {slug: product.slug }}" round glossy color="teal" icon="eva-external-link-outline">
              <q-tooltip content-class="bg-teal">Lihat</q-tooltip>
            </q-btn>

          </div>
        </q-item-section>
      </q-item>
    </q-list>
    <div class="q-my-md q-gutter-sm text-center" v-if="products.next_page_url">
      <q-btn :loading="isLoadmore" no-caps outline color="primary" @click="paginate(products.next_page_url)">
        <span>Loadmore...</span>
      </q-btn>
    
    </div>
    </div>
    </template>
    <template v-else >
      <div class="text-center q-pt-xl">Tdak ada data</div>
    </template>
    <q-page-sticky class="lt-sm" position="bottom-left" :offset="[12, 12]">
      <q-btn fab icon="add" color="primary" :to="{name: 'ProductCreate'}" glossy/>
    </q-page-sticky>
    <q-dialog v-model="varianViewModal" persistent position="bottom">
      <q-card v-if="productSelected" class="max-width" style="min-height:300px;">
        <q-list>
          <q-item>
            <q-item-section>
              <q-item-label lines="1" class="text-weight-medium">
                <div>{{ productSelected.title }}</div>
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn icon="eva-close" flat padding="xs" round v-close-popup></q-btn>
            </q-item-section>
          </q-item>
        </q-list>
        <q-separator></q-separator>
        <div v-for="varian in productSelected.varians" :key="varian.id">
          <q-list v-if="varian.has_subvarian" dense>
            <q-item v-for="subvarian in varian.subvarian" :key="subvarian.id">
              <q-item-section>{{ varian.label }} {{ varian.value }} - {{ subvarian.label }} {{ subvarian.value }}</q-item-section>
              <q-item-section>Stok : {{ subvarian.stock }}</q-item-section>
              <q-item-section>Harga : {{ moneyIDR(productSelected.price+subvarian.price) }}</q-item-section>
            </q-item>
          </q-list>
          <q-list v-if="!varian.has_subvarian" dense>
            <q-item>
              <q-item-section>{{ varian.label }} {{ varian.value }} </q-item-section>
              <q-item-section>Stok : {{ varian.stock }}</q-item-section>
              <q-item-section>Harga : {{ moneyIDR(productSelected.price+varian.price) }}</q-item-section>
            </q-item>
          </q-list>
        </div>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { Api } from 'boot/axios'
export default {
  name: 'AdminProductList',
  data() {
    return {
      productSelected: null,
      varianViewModal: false,
      pageNumber: 1,
      search: '',
      productSearch: [],
      showListId: null,
      isLoadmore: false
    }
  },
  computed: {
    ...mapState({
      products: state => state.product.admin_products,
      loading: state => state.loading
    }),
    isDesktop() {
      return window.innerWidth > 600
    }
  },
  methods: {
    ...mapActions('product', ['getAdminProducts', 'productDelete', 'searchAdminProducts']),
    selectVarian(product) {
      this.varianViewModal = true
      this.productSelected = product
    },
    searchProduct() {
      this.$store.commit('SET_LOADING', true)
      this.$refs.input.blur()
      this.searchAdminProducts(this.search).then(response => {
        if(response.status == 200) {
          this.$store.commit('product/SET_ADMIN_PRODUCTS', response.data.results)
        }
      }).finally(() => {
        this.$store.commit('SET_LOADING', false)
      })
    },
    showList(id) {
      if(this.showListId == id) {
        this.showListId = null
      }else {
        this.showListId = id
      }
    },
    remove(id) {
      this.$q.dialog({
        title: 'Konfirmasi Penghapusan Item',
        message: 'Yakin akan menghapus data?',
        ok: {label: 'Hapus', flat: true, 'no-caps': true},
        cancel: {label: 'Batal', flat: true, 'no-caps': true},
      }).onOk(() => {
        this.productDelete(id)
      }).onCancel(() => {
        console.log('Cancel')
      }).onDismiss(() => {
      })
    },
    getMargin(product) {
      if(!product.buy_price || !product.price) {
        return 0
      } else {
        return product.price-product.buy_price
      }
    },
    paginate(url) {
      this.isLoadmore = true
      Api().get(url).then(response => {
        if(response.status == 200) {
          this.$store.commit('product/PAGINATE_ADMIN_PRODUCTS', response.data.results)
        }
      }).finally(() => this.isLoadmore = false)
    }
  },
  created() {
    if(this.products.data.length <= this.products.per_page) {
      this.getAdminProducts()
    } 
  }
}
</script>
