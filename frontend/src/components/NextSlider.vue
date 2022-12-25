<template>
  <div class="carousel-container">
    <div class="carousel-items" ref="carousel"
    :class="{ 'justify-center' : center, 'justify-between' : between, 'justify-evenly' : evenly }"
      @mousedown="handleMouseDown"
      @mouseleave="handleMouseLeave"
      @mouseup="handleMouseUp"
      @mousemove="handleMouseMove"
    > 
    <slot :width="width"></slot>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'NextSlider',
    props: {
      center: {
        type: Boolean,
        default: false
      },
      between: {
        type: Boolean,
        default: false
      },
      evenly: {
        type: Boolean,
        default: false
      },
      width: {
        default: 220
      }
    },
    data () {
      return {
        carousel: null,
        idDown: false,
        startX: 0,
        scrollLeft: 0,
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
