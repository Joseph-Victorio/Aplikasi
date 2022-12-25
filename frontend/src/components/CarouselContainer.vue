<template>
  <div class="carousel-container">
    <div class="carousel-items" ref="carousel"
    @mousedown="handleMouseDown"
    @mouseleave="handleMouseLeave"
    @mouseup="handleMouseUp"
    @mousemove="handleMouseMove"
    > 
      <div v-for="product in products" :key="product.id" class="carousel-item" 
      :style="styleWidth"
      >
        <swiper-product-card :product="product" />
      </div>
    </div>
  </div>
</template>

<script>
import SwiperProductCard from 'components/SwiperProductCard'
  export default {
    name: 'SwipperProduct',
    props: {
      products: Array,
    },
    components: { SwiperProductCard },
    data () {
      return {
        carousel: null,
        idDown: false,
        startX: 0,
        scrollLeft: 0,
      }
    },
    computed: {
      page_width() {
        return this.$store.state.page_width
      },
      styleWidth() {

        if(this.page_width > 1024) {
          return 'width: 220px;'
        }

        if(this.page_width > 800) {
          return 'width: 200px;'
        }

        if(this.page_width > 400) {
          return 'width: 160px;'
        }

        return 'width: 150px;'

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

        this.startX = e.pageX - this.carousel.offsetLeft;

        this.scrollLeft = this.carousel.scrollLeft;
      },
      handleMouseLeave(e) {
        this.isDown = false;
        this.carousel.classList.remove('active');
      },
      handleMouseUp(e) {
        this.isDown = false;
        this.carousel.classList.remove('active');
      },
      handleMouseMove(e) {
        if(!this.isDown) return;

        e.preventDefault();

        const x = e.pageX - this.carousel.offsetLeft;
        const walk = (x - this.startX) * 1.2; //scroll-fast
        this.carousel.scrollLeft = this.scrollLeft - walk;
      },
    }
  }
</script>
