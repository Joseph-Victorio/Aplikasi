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
    }
  }

}
</script>
