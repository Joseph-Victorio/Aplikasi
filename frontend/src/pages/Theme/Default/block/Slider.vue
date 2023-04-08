<template>
  <div class="overflow-hidden" v-if="sliders.ready && sliders.available">
    <vue-glide :options="glideOptions">
        <vue-glide-slide v-for="(img, index) in sliders.data" :key="index">
          <div class="slider-padding">
            <img alt="Slider" :src="img.src" style="border-radius:6px;" class="img-slider"/>
          </div>
        </vue-glide-slide>
      </vue-glide>  
  </div>
</template>
<script>
export default {
  name: 'FrontSlider',
  data () {
    return {
      glideOptions: {
        type: 'carousel',
        gap:10,
        perView: 1,
        animationDuration: 1000,
        autoplay: 6000,
        bullet: true
      },
    }
  },
  computed: {
    sliders() {
      return this.$store.state.front.sliders
    },
    page_width() {
      return this.$store.state.page_width
    },
    sliderHeight() {
      if(this.page_width < 768) {
        return `${this.page_width/2.2}px`
      }else {
        return `${768/2.2}px`
      }
    }
  },
  watch: {
    sliderCount(val) {
      if(val > 0 && !this.sliders.ready) {
        this.$store.dispatch('front/getSliders')
      }
    }
  }

}
</script>