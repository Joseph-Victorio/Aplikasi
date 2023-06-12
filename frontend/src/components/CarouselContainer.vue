<template>
  <div class="carousel-container">
    <div class="carousel-items" ref="carousel" v-if="products.length"
      @mousedown="handleMouseDown"
      @mouseleave="handleMouseLeave"
      @mouseup="handleMouseUp"
      @mousemove="handleMouseMove"
    > 
      <div v-for="product in products" :key="product.id" class="carousel-item" 
      :style="`width: ${cardWidth}px`"
      >
        <swiper-product-card :product="product" :grabbing="is_grab"/>
      </div>
      <div class="carousel-item" v-if="loadmore"
      :style="`width: ${cardWidth}px`"
      >
        <div class="full-height flex column relative text-center justify-center items-center">
          <div>
            <q-btn unelevated icon="eva-arrow-forward" round size="16px" color="primary" :to="{name: 'ProductCategory', params:{ id: loadmore.category }}"></q-btn>
            <div class="q-pt-md">Selengkapnya <br>di {{ loadmore.title }}</div>
          </div>
         </div>
      </div>
    </div>
    <div class="carousel-items" ref="carousel" v-if="!products.length">
      <div v-for="i in 4" :key="i" class="carousel-item" 
     :style="cardWidthString"
      >
        <ProductCardSkeleton :width="cardWidth"/>
      </div>
    </div>
  </div>
</template>

<script>
import SwiperProductCard from 'components/SwiperProductCard'
import ProductCardSkeleton from 'components/ProductCardSkeleton'
  export default {
    name: 'SwipperProduct',
    components: { SwiperProductCard, ProductCardSkeleton },
    props: {
      products: Array,
      loadmore: Object,
      ready: Boolean
    },
    data () {
      return {
        isGrab: false,
        carousel: null,
        isDown: false,
        startX: 0,
        scrollLeft: 0,
        movementX: 0,
        pageX: 0
      }
    },
    computed: {
      page_width() {
        return this.$store.state.page_width
      },
      is_grab() {
        return  this.movementX != 0
      },
      cardWidth() {
         if(this.page_width > 1024) {
          return 225
        }

        if(this.page_width > 800) {
          return 210
        }

        if(this.page_width > 400) {
          return 180
        }

        return 160
      },
      cardWidthString() {
        return `width: ${this.cardWidth}px`
      }
    },
    mounted() {
       this.$nextTick(() => {
        this.carousel = this.$refs.carousel
      })

    },
    methods: {
      handleMouseDown(e) {
        this.isDown = true;

        this.carousel.classList.add('active');

        this.startPageX = e.pageX
        this.startX = e.pageX - this.carousel.offsetLeft;

        this.scrollLeft = this.carousel.scrollLeft;
      },
      handleMouseLeave(e) {
        this.isDown = false;
        this.carousel.classList.remove('active');
        this.movementX = 0
      },
      handleMouseUp(e) {
        this.isDown = false;
        this.carousel.classList.remove('active');
        setTimeout(() => {
          this.movementX = 0
        }, 100);
      },
      handleMouseMove(e) {
        if(!this.isDown) return;

        this.movementX = e.movementX

        e.preventDefault();
        this.pageX = e.pageX - this.carousel.offsetLeft;
        const walk = (this.pageX - this.startX) * 1.2; //scroll-fast
        this.carousel.scrollLeft = this.scrollLeft - walk;
      },
    }
  }
</script>
