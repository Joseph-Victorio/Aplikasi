<template>
  <q-page>
     <q-header class="bg-brand">
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Produk
        </q-toolbar-title>
        <q-btn  color="white" text-color="brand" size="13px" class="gt-xs" no-caps icon="eva-plus-circle" :to="{name: 'ProductCreate'}" label="Tambah Produk"/>
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

     <div class="q-pt-sm q-pb-xl">
      <q-list separator>
        <q-item class="item-header">
          <q-item-section>Produk</q-item-section>
        </q-item>
       <q-item v-for="product in products.data" :key="product.id">

         <q-item-section avatar class="q-pr-md" top>
           <q-img v-if="product.featured_image" :src="product.featured_image.src" class="bg-white img-product-admin img-thumbnail" ratio="1" width="55px"/>
        </q-item-section>

        <q-item-section top>
          <div>
            <q-item-label lines="2" class="text-15 text-weight-medium text-grey-9">{{ product.title }}</q-item-label>
            <template v-if="product.varian_items_count > 0">
              <q-item-label caption >{{ renderVarianPrice(product) }}</q-item-label>
              <q-item-label caption >Total Stok : {{ product.varian_items_sum_stock }} ( {{ product.varian_items_count }} varian )</q-item-label>
            </template>
            <template v-else>
              <q-item-label caption >{{ moneyIDR(product.price) }}</q-item-label>
              <q-item-label caption >Stok : {{ product.stock }}</q-item-label>
            </template>

            </div>
        </q-item-section>

        <q-item-section side top>
          <div v-if="!isDesktop">
            <q-fab color="primary" icon="eva-chevron-left" direction="left" glossy padding="sm" unelevated>
              <q-fab-action unelevated @click="remove(product.id)" round icon="eva-trash-2" glossy color="red">
              </q-fab-action>
              <q-fab-action unelevated :to="{ name: 'ProductEdit', params: {id: product.id }}" round glossy color="blue" icon="eva-edit-2">
              </q-fab-action>
              <q-fab-action unelevated :to="{ name: 'ProductShow', params: {slug: product.slug }}" round glossy color="teal" icon="eva-external-link-outline">
              </q-fab-action>
              <q-fab-action v-if="product.varian_items_count > 0" unelevated @click="getDetailVarian(product)" round icon="eva-pantone" glossy color="accent">
              </q-fab-action>
            </q-fab>
          </div>
          <div class="row q-gutter-xs" v-if="isDesktop">
            <q-btn size="11px" v-if="product.varian_items_count > 0" unelevated @click="getDetailVarian(product)" round icon="eva-pantone" glossy color="accent">
              <q-tooltip content-class="bg-dark">Detil Varian</q-tooltip>
            </q-btn>
            <q-btn size="11px" unelevated @click="remove(product.id)" round icon="eva-trash-2" glossy color="red">
              <q-tooltip content-class="bg-dark">Hapus</q-tooltip>
            </q-btn>
            <q-btn size="11px" unelevated :to="{ name: 'ProductEdit', params: {id: product.id }}" round glossy color="blue" icon="eva-edit-2">
              <q-tooltip content-class="bg-dark">Edit</q-tooltip>
            </q-btn>
            <q-btn size="11px" unelevated :to="{ name: 'ProductShow', params: {slug: product.slug }}" round glossy color="teal" icon="eva-external-link-outline">
              <q-tooltip content-class="bg-dark">Lihat</q-tooltip>
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
    <template v-if="!products.available">
      <div class="text-center q-pt-xl">Tdak ada data</div>
    </template>
    <q-page-sticky class="lt-sm" position="bottom-left" :offset="[12, 12]">
      <q-btn fab icon="add" color="primary" :to="{name: 'ProductCreate'}" glossy/>
    </q-page-sticky>
    <q-dialog v-model="varianViewModal" persistent position="bottom">
      <q-card v-if="productSelected" class="max-width q-pb-lg" style="min-height:400px;">
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
        <div >
          <q-list dense separator>
            <q-item v-for="varian in varian_items" :key="varian.id">
              <q-item-section>
                <q-item-label class="q-gutter-x-xs">
                  <template v-if="varian.attribute_value">
                    <span >{{ varian.attribute_label}} {{ varian.attribute_value}}</span>
                    <span >/</span>
                    <span>{{ varian.value }}</span>
                  </template>
                  <span v-else>{{ varian.label }} {{ varian.value }}</span>
                </q-item-label>
              </q-item-section>
              <q-item-section>Stok : {{ varian.stock }}</q-item-section>
              <q-item-section>Harga : {{ moneyIDR(parseInt(varian.price)) }}</q-item-section>
            </q-item>
          </q-list>
        </div>
        <q-inner-loading :showing="is_loading_varian"></q-inner-loading>
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
      isLoadmore: false,
      is_loading_varian: false,
      varian_items: []
    }
  },
  computed: {
    ...mapState({
      products: state => state.product.admin_products,
      loading: state => state.loading
    }),
    isDesktop() {
      return window.innerWidth > 600
    },
  },
  methods: {
    ...mapActions('product', ['getAdminProducts', 'productDelete', 'searchAdminProducts']),
    selectVarian(product) {
      this.varianViewModal = true
      this.productSelected = product
    },
    renderVarianPrice(product) {

      if(product.min_price && product.max_price) {
        let minPrice = parseInt(product.min_price.price)
        let maxPrice = parseInt(product.max_price.price)
  
        if(minPrice < maxPrice) {
          return `${this.moneyIDR(minPrice)} - ${this.moneyIDR(maxPrice)}`
        }
        
        return `@ ${this.moneyIDR(minPrice)}`
      }

      return '';
      
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
    },
    getDetailVarian(product) {
      this.varianViewModal = true
      if(this.productSelected && this.productSelected.id == product.id) return
      this.varian_items = []
      this.is_loading_varian = true
      this.productSelected = product
      Api().get('products/varians/' + product.id).then(res => {
        if(res.status == 200) {
          this.varian_items = res.data.results
        }
      }).finally(() => this.is_loading_varian = false)
    }
  },
  created() {
    if(this.products.data.length <= this.products.per_page) {
      this.getAdminProducts()
    } 
  }
}
</script>
