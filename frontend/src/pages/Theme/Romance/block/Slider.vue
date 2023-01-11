<template>
  <div class="overflow-hidden header-romance">
    <template v-if="sliders.ready && sliders.available">
      <q-carousel
        v-model="slide"
        transition-prev="slide-right"
        transition-next="slide-left"
        swipeable
        animated
        infinite
        keep-alive
        :autoplay="5500"
        height="auto"
        class="bg-grey-2 text-white"
        >
        <q-carousel-slide v-for="(img, index) in sliders.data" :key="index" :name="index" class="q-pa-none">
          <img :src="img.src" alt="Slider" class="img-slider">
          </q-carousel-slide>
      </q-carousel>
    </template>
    <q-skeleton v-if="!sliders.ready" height="320px"></q-skeleton>
  </div>
</template>

<script>
export default {
  name: 'FrontSlider',
  data () {
    return {
      slide: 0,
    }
  },
  computed: {
    sliders() {
      return this.$store.state.front.sliders
    }
  }

}
</script>
