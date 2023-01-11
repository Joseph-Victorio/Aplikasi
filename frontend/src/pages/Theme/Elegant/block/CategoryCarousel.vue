<template>
  <div>
    <div id="categories" v-if="categories.ready && categories.data.length > 1" class="auto-padding block-container">
      <div class="block-heading q-mt-md">
        <div class="block-title"><h2>Kategori</h2></div>
      </div>
      <div class="block-content q-pb-sm">
        <div class="overflow-hidden block-category">
          <vue-glide :options="glideOptions">
            <vue-glide-slide v-for="cat in categories.data" :key="cat.id">
              <div @click="openCategory(cat.id)" class="cursor-pointer column items-center">
                <div class="image">
                  <img v-if="cat.filename" :src="cat.src" />
                </div>
                <div class="text-category-auto text-center q-mt-xs">{{ cat.title }}</div>
              </div>
            </vue-glide-slide>
          </vue-glide> 
        </div>
      </div>
    </div>
    <CategorySkeleton v-if="!categories.ready"/>
  </div>
</template>

<script>
import CategorySkeleton from '../../shared-components/CategorySkeleton.vue'
export default {
  name: 'CategoryCarousel',
  components: { CategorySkeleton },
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
  computed: {
    categories() {
      return this.$store.state.front.categories
    },
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