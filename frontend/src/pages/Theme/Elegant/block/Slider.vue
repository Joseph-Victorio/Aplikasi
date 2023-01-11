<template>
  <div class="header_elegant">
    <div class="header_elegant--inner q-pt-sm q-pb-md q-mb-md" v-if="sliders.ready && sliders.available">
      <div class="overflow-hidden">
        <vue-glide :options="glideOptions">
            <vue-glide-slide v-for="(img, index) in sliders.data" :key="index">
              <div class="auto-padding p-lg">
                <img :src="img.src" style="border-radius:6px;" class="img-slider"/>
              </div>
            </vue-glide-slide>
            <template slot="control" data-glide-el="controls[nav]" v-if="sliders.data.length > 1">
              <div class="glide__bullets" data-glide-el="controls[nav]">
                <button v-for="(d,i) in sliders.data.length" :key="i" class="glide__bullet" :data-glide-dir="'='+ i"></button>
              </div>
            </template>
          </vue-glide>  
        </div>
      </div>
      <div class="header_elegant--inner q-pt-sm q-pb-md q-mb-md" v-if="!sliders.ready">
        <div class="q-pa-lg q-pb-xl">
          <q-skeleton :height="sliderHeight"></q-skeleton>
        </div>
      </div>
  </div>
</template>

<script>
export default {
  name: 'SliderElegant',
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
    sliderHeight() {
      if(this.page_width < 768) {
        return `${this.page_width/2.2}px`
      }else {
        return `${768/2.2}px`
      }
    }
  }

}
</script>