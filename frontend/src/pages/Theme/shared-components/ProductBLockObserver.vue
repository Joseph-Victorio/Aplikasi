<template>
  <BlockObserver @onObserve="handleonObserve">
    <div class="block-container bg-linear">
      <div class="auto-padding q-mb-xs">
        <div class="row items-end justify-between">
          <div class="block-title"><h2>{{ category.title }}</h2></div>
          <q-btn flat no-caps color="primary" padding="4px" :to="{name: 'ProductCategory', params:{ id: category.id }}">
            <span>Lihat Semua</span>
            <q-icon name="eva-arrow-forward" size="16px"></q-icon>
          </q-btn>
        </div> 
        <div v-if="category.description" class="block-subtitle q-mt-sm">{{ category.description }}</div>
        <div class="banner" v-if="category.banner_src">
          <router-link :to="{name: 'ProductCategory', params:{ id: category.id }}">
            <img :src="category.banner_src" :alt="category.title" class="cursor-pointer q-mt-sm" />
            </router-link>
          </div>
        </div>
        <div class="block-content">
          <div v-if="config && config.home_view_mode == 'list'">
            <ProductListSectionHome :products="products" />
          </div>
          <div v-else class="auto-padding-side">
            <CarouselContainer :products="products" :loadmore="{ title: category.title, category: category.id }"
            />
          </div>
        </div>
    </div>
  </BlockObserver>
</template>

<script>
import CarouselContainer from 'components/CarouselContainer.vue'
import ProductListSectionHome from 'components/ProductListSectionHome.vue'
import BlockObserver from 'src/components/BlockObserver.vue'
import { Api } from 'boot/axios'
export default {
  props: ['category'],
  components: { 
    CarouselContainer,
    ProductListSectionHome,
    BlockObserver
   },
  data() {
    return {
      success: true,
      is_load_observer: true
    }
  },
  computed: {
    config() {
      return this.$store.state.config
    },
    products() {
      return this.$store.getters['front/getProductItemByCategoryId'](parseInt(this.category.id))
    }
  },
  methods: {
    handleonObserve() {
       if(!this.products.length) {
        this.getProducts()
       }
    },
    async getProducts() {
      let response = await Api().get(`product-category?category_id=${this.category.id}&per_page=${this.config.home_product_limit}&order_by=${this.config.home_product_sort}`)

     if(response.status == 200) {
      let data = {
        category_id: this.category.id,
        product_items: response.data.results.data
      }
      this.$store.commit('front/SET_PRODUCT_CATEGORY', data)
     }
    }
  }
}
</script>
