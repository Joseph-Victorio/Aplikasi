<template>
  <div :id="'block-' + category.id" style="min-height:200px;" v-if="success">
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
            <product-list-section 
            :products="products" />
          </div>
          <div v-else class="auto-padding-side">
            <CarouselContainer 
            :products="products" 
            :loadmore="{ title: category.title, category: category.id }"
            />
          </div>
        </div>
      </div>
  </div>
</template>

<script>
import CarouselContainer from 'components/CarouselContainer.vue'
import ProductListSection from 'components/ProductListSectionHome.vue'
import { Api } from 'boot/axios'
export default {
  props: ['category'],
  components: { 
    CarouselContainer,
    ProductListSection
   },
  data() {
    return {
      success: true
    }
  },
  computed: {
    config() {
      return this.$store.state.config
    },
    categories(){
      return this.$store.state.front.categories
    },
    products() {
      return this.$store.getters['front/getProductItemByCategoryId'](parseInt(this.category.id))
    }
  },
  mounted() {
    if(!this.products.length) {
      this.intersecObserve()
    }
  },
  methods: {
    intersecObserve() {
      let el = document.getElementById('block-' + this.category.id)

      let opts = {
        rootMargin: '0px',
        threshold: 0,
      }

      let clb = (entries) => {

      entries.forEach(entry => {

        if(!entry.isIntersecting) {

          return
          
        } else {

          this.getProducts()

          observer.unobserve(entry.target)

        }
      });
    };

      let observer = new IntersectionObserver(clb, opts);

       observer.observe(el)

    },
    async getProducts() {
      let response = await Api().get('getProductsByCategory/' + this.category.id +'?limit=10')

     if(response.status == 200) {
      let data = {
        category_id: this.category.id,
        product_items: response.data.data
      }
      this.$store.commit('front/SET_PRODUCT_CATEGORY', data)
      this.success = true
     }else {
       this.success = false
     }
    }
  }
}
</script>
