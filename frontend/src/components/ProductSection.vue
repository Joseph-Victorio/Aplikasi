<template>
  <section>
    <div class="row auto-padding-side q-pt-md q-pb-sm justify-between items-center"
      :class="{ 'header-absolute': products.ready && !products.available }" v-if="$route.name != 'ProductSearch'">
      <div class="">
        <q-btn @click="handleMenuCategory" outline color="grey-7" padding="3px 10px" no-caps>
          <q-icon name="eva-keypad" size="15px"></q-icon>
          <span class="q-pl-xs">Kategori</span>
        </q-btn>
      </div>
      <div class="q-gutter-x-xs" v-if="config">
        <q-btn @click="changeViewMode('grid')" :outline="config.product_view_mode != 'grid'" unelevated size="12px"
          :color="config.product_view_mode == 'grid' ? 'secondary' : 'grey-7'" dense icon="eva-grid"></q-btn>
        <q-btn @click="changeViewMode('list')" :outline="config.product_view_mode != 'list'" unelevated size="12px"
          :color="config.product_view_mode == 'list' ? 'secondary' : 'grey-7'" dense icon="eva-list"></q-btn>
      </div>
    </div>
    <div :class="page_width >= 668 ? 'row q-px-sm' : 'column q-gutter-y-sm'"
      v-if="config && config.product_view_mode == 'list'">
      <template v-if="products.ready">
        <ProductList v-for="(product, index) in products.data" :key="index" :product="product" />
      </template>
      <template v-else>
        <div :class="{ 'col-6 q-pa-xs': page_width >= 668 }" v-for="a in 6" :key="a">
          <ProductListSkeleton />
        </div>
      </template>
    </div>
    <div class="row items-stretch auto-padding-side q-mt-sm" v-else>
      <template v-if="products.ready">
        <div v-for="(product, index) in products.data" :key="index" :product="product" class="q-px-xs q-pb-xs q-mb-sm"
          :class="page_width >= 800 ? 'col-4' : 'col-6'">
          <ProductCard :product="product" />

        </div>
      </template>
      <template v-else>
        <div v-for="a in 6" :key="a" :class="page_width >= 768 ? 'col-4' : 'col-6'" class="q-pa-xs">
          <ProductCardSkeleton :width="skeletonWidth" />
        </div>
      </template>
    </div>
    <q-dialog v-model="isMenuCategory" position="bottom">
      <CategoryMenu />
    </q-dialog>
  </section>
</template>

<script>
import ProductCard from 'components/ProductCard.vue'
import ProductCardSkeleton from 'components/ProductCardSkeleton.vue'
import ProductList from 'components/ProductList.vue'
import ProductListSkeleton from 'components/ProductListSkeleton.vue'
import CategoryMenu from 'components/CategoryMenu.vue'

export default {
  name: 'ProductSection',
  components: {
    ProductCard,
    ProductList,
    ProductCardSkeleton,
    ProductListSkeleton,
    CategoryMenu
  },
  props: {
    products: Object,
    title: String,
  },
  data() {
    return {
      viewMode: 'grid',
    }
  },
  computed: {
    config() {
      return this.$store.state.config
    },
    page_width() {
      return this.$store.state.page_width
    },
    skeletonWidth() {
      if (this.page_width >= 768) {
        return 768 / 3.5
      }

      return this.page_width / 3
    },
    isMenuCategory: {
      get() {
        return this.$store.state.isMenuCategory
      },
      set(status) {
        this.$store.commit('SET_MENU_CATEGORY', status)
      }
    }
  },
  methods: {
    changeViewMode(str) {
      this.$store.commit('SET_PRODUCT_VIEW_MODE', str)
    },
    handleMenuCategory() {
      this.isMenuCategory = !this.isMenuCategory
    }

  }


}
</script>
