<template>
  <section>
    <div :class="page_width >= 768 ? 'row q-px-sm' : 'column q-gutter-y-sm'">
      <template v-if="products.length">
        <product-list v-for="(product, index) in products" :key="index" :product="product" />
      </template>
      <template v-else>
        <product-list-skeleton />
      </template>
    </div>
    <!-- <div class="q-my-lg q-py-lg flex justify-center">
      <q-btn unelevated label="Selengkapnya" icon-right="eva-arrow-forward-outline" color="primary" :to="{name: 'ProductCategory', params:{ id: products.category_id }}"></q-btn>
    </div> -->
  </section>
</template>

<script>
import ProductList from 'components/ProductList.vue'
import ProductListSkeleton from 'components/ProductListSkeleton.vue'
export default {
  name: 'ProductListHome',
  components: { ProductList, ProductListSkeleton },
  props: {
    products: Array,
    title: String,
    path: {
      type: String,
      default: ''
    },
    query: {
      type: String,
      default: ''
    },
    ready: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    page_width() {
      return this.$store.state.page_width
    }
  },
  methods: {
    detail() {
      if(this.query) {
        this.$router.push({name: this.path, query: {q: this.query}})
      } else {
        this.$router.push({name: this.path})
      }
    }
  }
  

}
</script>