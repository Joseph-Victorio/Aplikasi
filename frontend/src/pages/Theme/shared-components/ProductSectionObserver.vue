<template>
  <div id="product-block" v-if="categories.length">
    <template v-for="category in categories">
      <div :key="category.id" v-if="category.is_front && category.products_count > 0" 
        class="block-container bg-linear">
        <ProductBlockObserver :category="category" />
      </div>
    </template>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import ProductBlockObserver from './ProductBLockObserver.vue'
export default {
  props: ['products'],
  components: { ProductBlockObserver },
  computed: {
    ...mapState({
      categories: state => state.front.categories
    })
  }
}
</script>
