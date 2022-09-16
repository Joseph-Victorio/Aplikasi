<template>
  <div class="overflow-hidden">
    <vue-glide :options="glideOptions">
      <vue-glide-slide v-for="cat in datas" :key="cat.id">
        <div class="column full-height text-center">
          <q-img v-if="cat.filename" :src="cat.src" ratio="1"  @click="openCategory(cat.id)" class="cursor-pointer rounded-borders bg-white">
          </q-img>
           <div class="text-category-auto text-center q-mt-xs">{{ cat.title }}</div>
        </div>
      </vue-glide-slide>
    </vue-glide> 
  </div>
</template>

<script>
export default {
  name: 'CategoryCarousel',
  props: {
    datas: Array
  },
  data () {
    return {
      glideOptions: {
        rewind: false,
        perView: 5,
        gap: 16,
        bound: true,
        breakpoints: {
          600: {
            perView: 4,
            gap: 12,
          },
          360: {
            perView: 3,
            gap: 8
          }
        }
      }
    }
  },
  created() {
    this.setGlideOptions()
  },
  methods: {
    setGlideOptions() {
      if(this.datas.length == 4) {
        this.glideOptions.perView = 4
      }
    },
    openCategory(id) {
      if(id) {
        this.$router.push({name: 'ProductCategory', params: {id:id}})
      }
    }
  }

}
</script>
