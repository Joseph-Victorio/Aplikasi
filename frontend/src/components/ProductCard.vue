<template>
  <div class="q-px-xs q-pb-xs q-mb-sm" :class="pageWidth >= 800 ? 'col-4' : 'col-6'">
    <div class="column full-height relative bg-white box-shadow">
      <q-img v-if="product.asset" :src="product.asset.src" ratio="1" @click="show(product.slug)" class="cursor-pointer">
         <template v-slot:error>
          <div class="absolute-full flex flex-center bg-grey-6 text-white">
            Cannot load image
          </div>
        </template>
      </q-img>
      <q-img v-else src="/static/no_image.png" ratio="1" @click="show(product.slug)" class="cursor-pointer"></q-img>
      <div class="relative col column justify-between q-pa-sm border-box bg-white">
        <div class="row justify-between">
          <div>
            <q-rating 
            readonly
            v-model="rating"
            color="accent"
            icon="eva-star-outline"
            icon-selected="eva-star"
            icon-half="eva-star"
            size="1.1rem"
          />
          </div>
         <favorite-button :product_id="product.id" />
        </div>
        <div class="text-subtitle2 ellipsis-2-lines">{{ product.title }}</div>
       <div class="card-price-container">
          <div class="card-price text-secondary">
            <!-- <span class="text-xs text-grey-8 text-weight-regular">Mulai </span> -->
            <span class="prefix">Rp</span>
            <span class="amount">{{ $money(product.pricing.current_price) }}</span>
          </div>
          <div v-if="product.pricing.is_discount" class="card-discount text-strike text-grey-8">
            <span class="prefix">Rp</span>
            <span class="amount">{{ $money(product.pricing.default_price) }}</span>
            </div>
        </div>
      </div>
       <div v-if="product.pricing.is_discount" class="absolute top-0 z-50 bg-red-6 text-white" style="padding:2px;font-size:13px;">{{ product.pricing.discount_percent }}%</div>
    </div>
    </div>
</template>

<script>
import CartButton from 'components/CartButton.vue'
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
  name: 'ProductCard',
  props: { product: Object},
  components: { FavoriteButton },
  data() {
    return {
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0,
      pageWidth: 800
    }
  },
  methods: {
    show(slug) {
      this.$router.push({name: 'ProductShow', params: {slug: slug}})
    },
    pageResize() {
      this.pageWidth = window.innerWidth
    }
  },
  created() {
    this.pageWidth = window.innerWidth
    window.addEventListener('resize', this.pageResize)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.pageResize)
  }
}
</script>