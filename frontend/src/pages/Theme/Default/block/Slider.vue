<template>
  <div class="overflow-hidden">
    <template v-if="sliders.ready && sliders.available">
      <vue-glide :options="glideOptions">
          <vue-glide-slide v-for="(img, index) in sliders.data" :key="index">
            <div class="slider-padding">
              <img :src="img.src" style="width:100%;height:auto;border-radius:6px;"/>
            </div>
          </vue-glide-slide>
        </vue-glide>  
    </template>
    <div class="slider-padding" v-if="!sliders.ready">
      <q-skeleton :height="sKeletonHeight"></q-skeleton>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DefaultSlider',
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
    page_width() {
      return this.$store.state.page_width
    },
    sKeletonHeight() {
      if(this.page_width < 768) {
        return `${this.page_width/2.2}px`
      }else {
        return `${768/2.2}px`
      }
    }
  }

}
</script>
