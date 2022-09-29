<template>
  <q-list class="relative" :class="pageWidth >= 768 ? 'col-6 q-pa-xs' : 'bg-white'">
    <q-item class="q-pa-md relative bg-white full-height">
      <q-item-section avatar top @click.prevent="$router.push({name: 'ProductShow', params:{ slug: product.slug }})" class="cursor-pointer column items-center q-gutter-y-sm">
        <q-img v-if="product.asset" :src="product.asset.src" ratio="1" class="image-list rounded-borders" width="105px">
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
          <q-item-label class="ellipsis-2-lines text-subtitle2 text-weight-medium" >{{ product.title }}</q-item-label>
         
            <q-item-label caption class="ellipsis-2-lines q-mt-xs" v-html="getTeaser(product.description)" ></q-item-label>
        </div>
        <div style="margin-top:auto;" class="q-pt-xs">
        <div class="flex items-center q-gutter-x-md">
          <div class="text-subtitle1 text-secondary text-weight-bold">{{ moneyIDR(product.pricing.current_price) }}</div>
          <div v-if="product.pricing.is_discount" class="text-subtitle2 text-weight-medium text-strike text-grey-8">{{ moneyIDR(product.pricing.default_price) }}</div>
        </div>
          <div class="flex justify-between items-center">
             <q-rating 
              readonly
              v-model="rating"
              color="accent"
              icon="eva-star-outline"
              icon-selected="eva-star"
              icon-half="eva-star"
              size="1rem"
            />
            <div>
              <favorite-button :product_id="product.id" />
            </div>
          </div>
        </div>
      </q-item-section>
       <div v-if="product.discount" class="absolute top-0 q-pa-xs z-50 bg-red-6 text-white">{{ product.display_discount }}</div>
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
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0,
    }
  },
  computed: {
    pageWidth() {
      return window.innerWidth
    }
  },
  methods: {
    show(id) {
      this.$router.push({name: 'ProductShow', params: {id: id}})
    },
    getTeaser(html) {
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 120) + '...'
      } else {
        return ''
      }
    },
  }
}
</script>