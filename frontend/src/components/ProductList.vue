<template>
  <q-list class="relative" :class="page_width >= 800 ? 'col-6 q-pa-xs' : 'bg-white'">
    <q-item class="q-pa-md relative bg-white">
      <q-item-section avatar top @click.prevent="$router.push({name: 'ProductShow', params:{ slug: product.slug }})" class="cursor-pointer column items-center q-gutter-y-sm">
        <q-img v-if="product.asset" :src="product.asset.src" ratio="1" class="image-list rounded-borders">
          <template v-slot:error>
          <div class="absolute-full flex flex-center bg-grey-6 text-white text-center">
            Cannot load image
          </div>
        </template>
        </q-img>
        <q-img v-else src="/static/no_image.png" ratio="1" class="image-list rounded-borders"/>
      </q-item-section>
      <q-item-section top>
        <div class="cursor-pointer" @click.prevent="$router.push({name: 'ProductShow', params:{slug: product.slug}})">
          <q-item-label class="ellipsis-2-lines text-subtitle2 text-weight-medium">{{ product.title }}</q-item-label>
          <div class="row justify-between items-center">
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
            <favorite-button :product_id="product.id" />
          </div>
            <!-- <q-item-label caption class="ellipsis q-mt-xs" v-html="getTeaser(product.description)"></q-item-label> -->
          <div class="flex items-center q-gutter-x-md q-mt-auto q-pt-sm">
            <div class="text-subtitle1 text-secondary text-weight-bold">{{ moneyIDR(parseInt(product.pricing.default_price)- getDIscountAmount) }}</div>
            <div v-if="getDiscountPercent" class="text-subtitle2 text-weight-medium text-strike text-grey-8">{{ moneyIDR(product.pricing.default_price) }}</div>
          </div>
        </div>
      </q-item-section>
    </q-item>
  </q-list>
</template>
<script>
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
  name: 'ProductCard',
  props: { product: Object},
  components: { FavoriteButton },
  data() {
    return {
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0
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
    show(id) {
      this.$router.push({name: 'ProductShow', params: {id: id}})
    },
    getTeaser(html) {
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 120)
      } else {
        return ''
      }
    },
  }
}
</script>