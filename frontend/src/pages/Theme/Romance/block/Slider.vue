<template>
  <div class="overflow-hidden header-romance" v-if="sliderCount > 0">
    <template v-if="sliders.ready">
      <vue-glide :options="glideOptions">
          <vue-glide-slide v-for="(img, index) in sliders.data" :key="index">
            <img alt="Slider" :src="img.src" class="img-slider"/>
          </vue-glide-slide>
        </vue-glide> 
    </template>
    <q-skeleton v-else :height="sliderHeight"></q-skeleton>
  </div>
</template>

<script>
export default {
  name: 'FrontSlider',
  data () {
    return {
      glideOptions: {
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
    sliderCount() {
      return this.$store.getters['front/getSliderCount']
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
