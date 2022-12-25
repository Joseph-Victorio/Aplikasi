<template>
  <div class="bg-white max-width q-pb-lg" style="min-height:280px">
    <q-list separator>
      <q-item>
        <q-item-section side>
          <q-icon name="eva-keypad"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-md">Kategori</q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn flat icon="eva-close" dense @click="closeCategory"></q-btn>
        </q-item-section>
      </q-item>
      <q-item v-for="category in categories" :key="category.id" clickable @click="handleShowCategory(category.id)">
        <q-item-section avatar>
          <q-avatar>
            <q-img :src="category.src"></q-img>
          </q-avatar>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ category.title }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
    <q-inner-loading :showing="loading"></q-inner-loading>
    </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false
    }
  },
  computed: {
    categories() {
      return this.$store.state.front.categories
    }
  },
  methods: {
    handleShowCategory(id) {
      this.closeCategory()
      if(id != this.$route.params.id) {
        this.$store.dispatch('product/getProductsByCategory', id)
        this.$router.push({ name: 'ProductCategory', params: { id: id }})
      }
    },
    closeCategory() {
      this.$store.commit('SET_MENU_CATEGORY', false)
    }
  },
  mounted() {
    if(!this.categories.length) {
      this.loading = true
      this.$store.dispatch('front/getCategories').then(res => {
        this.$store.commit('front/SET_CATEGORIES', res.data.results)
      }).finally(() => this.loading = false)
    }
  }
}
</script>
