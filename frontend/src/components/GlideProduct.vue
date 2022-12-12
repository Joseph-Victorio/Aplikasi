<template>
  <div class="overflow-hidden glide-product">
    <vue-glide :options="glideOptions">
      <vue-glide-slide v-for="product in products.items" :key="product.id" class="relative box-shadow">
        <swiper-product-card :product="product" />
      </vue-glide-slide>
      <vue-glide-slide class="relative ">
        <div class="full-height flex column relative text-center justify-center items-center">
          <div>
            <q-btn unelevated icon="eva-arrow-forward" round size="16px" color="primary" :to="{name: 'ProductCategory', params:{ id: products.category_id }}"></q-btn>
            <div class="q-pt-md">Selengkapnya <br>di {{ products.title }}</div>
          </div>
         </div>
      </vue-glide-slide>
    </vue-glide>  
  </div>
</template>

<script>
import SwiperProductCard from 'components/SwiperProductCard'
  export default {
    name: 'GlideProduct',
    props: {
      products: Object
    },
    components: { SwiperProductCard },
    data() {
      return {
        glideOptions: {
          dragThreshold: 20,
          swipeThreshold: 10,
          touchAngle: 20,
          touchRatio: 0.2,
          rewind: false,
          perView: 2,
          gap: 8,
          bound: true,
          peek: {
           before: 0, 
           after: 50
          },
          breakpoints: {
            3000: {
              perView: 3,
              peek: {
                before: 0, 
                after: 50
              },
            },
            900: {
              perView: 2,
              peek: {
                before: 0, 
                after: 50
              },
            },
            400: {
              perView: 2,
              peek: {
                before: 0, 
                after: 0
              },
            },
          }
        }
      }
    }
  }
</script>
