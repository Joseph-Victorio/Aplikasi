<template>
  <div>
    <q-input :value="categorySelectedTitle" label="Kategori" @click="categoryModal = true"></q-input>

    <q-dialog v-model="categoryModal">
        <q-card class="card-lg">
          <div class="text-weight-bold text-md q-pa-md">Pilih Kategori</div>
          <q-separator></q-separator>
          <q-list v-if="categories.data.length">
            <q-expansion-item v-for="cat in categories.data" :key="cat.id"
            :label="cat.title"
            group="cat"
            expand-separator
            >
              <q-item class="q-px-lg bg-grey-2" 
              clickable 
              @click="selectCategory(cat.id)"
              @input="selectCategory(cat.id)"
              > 
                <q-item-section side>
                  <q-icon 
                  :name="category_id == cat.id ? 'radio_button_checked' : 'radio_button_unchecked'"
                  :color="category_id == cat.id ? 'green' : 'grey-8'"
                  ></q-icon>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ cat.title }} ( Parent )</q-item-label>
                </q-item-section>
              </q-item>
              <q-item v-for="child in cat.childs" :key="child.id" class="q-px-lg bg-grey-2" 
              clickable 
              @click="selectCategory(child.id)"
              @input="selectCategory(child.id)"
              > 
                <q-item-section side>
                  <q-icon 
                  :name="category_id == child.id ? 'radio_button_checked' : 'radio_button_unchecked'"
                  :color="category_id == child.id ? 'green' : 'grey-8'"
                  ></q-icon>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ child.title }}</q-item-label>
                </q-item-section>
              </q-item>
              <!-- <div v-if="!cat.childs.length" class="text-center q-pa-md bg-grey-2">Tidak ada subkategori</div> -->
            </q-expansion-item>
          </q-list>
          <div class="flex justify-end q-pa-md">
            <q-btn v-close-popup flat label="Close" color="primary"></q-btn>
          </div>
        </q-card>
      </q-dialog>
  </div>
</template>

<script>

export default {
  props: ['category_id'],
  data() {
    return {
      categoryModal: false
    }
  },
  mounted() {
    if(!this.all_categories.data.length) {
      this.$store.dispatch('category/getAllCategories')
    }
    if(!this.categories.data.length) {
      this.$store.dispatch('category/getCategoriesWithChilds')
    }
  },
  computed: {
    all_categories() {
      return this.$store.state.category.all_categories
    },
    categories() {
      return this.$store.state.category.categories
    },
    categorySelectedTitle() { 
      let catTitle = 'Pilih Kategori'
      if(this.all_categories.data.length && this.category_id) {

        let cat =  this.all_categories.data.find(el => el.id == this.category_id)
        if(cat != undefined) {
          catTitle = cat.title
        }
      }

      return catTitle
    },
  },
  methods: {
    selectCategory(id) {
      if(this.category_id == id) {
        this.$emit('onUpdate', '')
      }else {
        this.$emit('onUpdate', id)
      }
    },
  }
}
</script>
