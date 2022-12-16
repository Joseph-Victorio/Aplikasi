<template>
 <div class="q-px-xs q-pb-xs q-mb-sm" :class="page_width >= 800 ? 'col-4' : 'col-6'">
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
            data-nosnippet="true"
            readonly
            v-model="rating"
            color="accent"
            icon="ion-star-outline"
            icon-selected="ion-star"
            icon-half="ion-star-half"
            size="1.1rem"
          />
          </div>
         <favorite-button :product_id="product.id" />
        </div>
        <div class="text-subtitle2 ellipsis-2-lines">{{ product.title }}</div>
       <div class="card-price-container">
          <div class="card-price text-secondary">
            <span class="prefix">Rp</span>
            <span class="amount">{{ $money(parseInt(product.pricing.default_price)- getDIscountAmount) }}</span>
          </div>
          <div v-if="getDiscountPercent" class="card-discount text-strike text-grey-8">
            <span class="prefix">Rp</span>
            <span class="amount">{{ $money(parseInt(product.pricing.default_price)) }}</span>
            </div>
        </div>
      </div>
       <div v-if="getDiscountPercent" class="absolute top-0 z-50 bg-red-6 text-white" style="padding:2px;font-size:13px;">{{ getDiscountPercent }}%</div>
    </div>
    </div>
</template>

<script>
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
  name: 'ProductCard',
  props: { product: Object},
  components: { FavoriteButton },
  data() {
    return {
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0,
    }
  },
  computed: {
    page_width() {
      return this.$store.state.page_width
    },
    getDIscountAmount() {
      if(this.product.pricing.is_discount) {
        if(this.product.pricing.discount_type == 'PERCENT') {
          return (parseInt(this.product.pricing.default_price)*parseInt(this.product.pricing.discount_amount))/100
        }else {
          return parseInt(this.product.pricing.discount_amount)
        }
      }
      return 0
    },
    getDiscountPercent() {
       if(this.product.pricing.is_discount) {
        if(this.product.pricing.discount_type == 'PERCENT') {
          return parseInt(this.product.pricing.discount_amount)
        }else {
          return parseInt((parseInt(this.product.pricing.discount_amount) / parseInt(this.product.pricing.default_price))*100)
        }
      }
      return 0
    }
  },
  methods: {
    show(slug) {
      this.$router.push({name: 'ProductShow', params: {slug: slug}})
    },
  },
}
</script>