<template>
  <div class="overflow-hidden" v-if="sliderCount > 0">
    <template v-if="sliders.ready">
      <vue-glide :options="glideOptions">
          <vue-glide-slide v-for="(img, index) in sliders.data" :key="index">
            <div class="slider-padding">
              <img alt="Slider" :src="img.src" style="border-radius:6px;" class="img-slider"/>
            </div>
          </vue-glide-slide>
        </vue-glide>  
    </template>
    <div class="slider-padding" v-else>
      <q-skeleton :height="sliderHeight"></q-skeleton>
    </div>
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
      console.log(val);
      if(val > 0 && !this.sliders.ready) {
        this.$store.dispatch('front/getSliders')
      }
    }
  }

}
</script>