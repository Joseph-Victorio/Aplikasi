<template>
<div class="overflow-hidden header-romance">
  <template v-if="sliders.ready && sliders.available">
   <vue-glide :options="glideOptions">
      <vue-glide-slide v-for="(img, index) in sliders.data" :key="index">
         <img :src="img.src" style="width:100%;height:auto;"/>
      </vue-glide-slide>
    </vue-glide> 
  </template>
  <q-skeleton v-else height="320px"></q-skeleton>
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
    }
  }

}
</script>
