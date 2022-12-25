<template>
  <div id="categories" v-if="categories && categories.length > 1" class="auto-padding block-container">
    <div class="block-heading">
      <div class="block-title"><h2>Kategori</h2></div>
    </div>
    <div class="block-content q-pb-sm">
        <div class="overflow-hidden">
        <vue-glide :options="glideOptions">
          <vue-glide-slide v-for="cat in categories" :key="cat.id">
            <div class="column full-height relative bg-white">
              <q-img v-if="cat.filename" :src="cat.src" ratio="1" @click="openCategory(cat.id)" class="cursor-pointer rounded-borders">
              </q-img>
            </div>
            <div class="text-category-auto text-center q-mt-xs">{{ cat.title }}</div>
          </vue-glide-slide>
        </vue-glide> 
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CategoryCarousel',
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
  computed: {
    categories() {
      return this.$store.state.front.categories
    }
  },
 mounted() {
    this.setGlideOptions()
  },
  methods: {
    setGlideOptions() {
      if(this.categories.length == 4) {
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