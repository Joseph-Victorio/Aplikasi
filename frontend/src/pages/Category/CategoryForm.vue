<template>
  <q-page class="">
    <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-btn v-go-back.single flat round dense icon="eva-arrow-back" />
        <q-toolbar-title>
          {{ formType }} Kategori
        </q-toolbar-title>

      </q-toolbar>
    </q-header>
    <form @submit.prevent="submit">
      <div class="q-pa-md q-gutter-y-md">

        <q-select :options="parentCategoryOptionsFiltered" map-options emit-value required filled label="Parent"
          v-model="form.category_id" />
        <q-input required filled label="Title" v-model="form.title" />
        <q-input required filled mask="####" label="Urutan" v-model="form.weight" />
        <q-input filled type="textarea" rows="3" label="Deskripsi" v-model="form.description" />
        <div class="border rounded q-pa-sm">
          <div>
            <q-toggle v-model="form.is_front" label="Tampilkan produk di beranda"></q-toggle>
            <div class="text-xs text-grey-6 q-pa-xs">
              Pilihan apakah produk dibawah kategori ini akan di listing di halaman beranda
            </div>
          </div>
        </div>

        <section id="image-banner" v-if="!form.category_id">
          <div class="row items-center justify-between">
            <q-btn label="Upload Ikon" size="sm" color="primary" icon="eva-upload" class="mt-2 mr-2" type="button"
              @click.prevent="selectNewImage" />
            <q-btn label="Upload Banner" size="sm" color="teal" icon="eva-upload" class="mt-2 mr-2" type="button"
              @click.prevent="selectNewBanner" />
          </div>

          <div class="text-xs text-red q-my-md" v-if="errors.images"> {{ errors.images[0] }}</div>
          <div>
            <input type="file" class="hidden" ref="image" @change="updateImagePreview">
            <input type="file" class="hidden" ref="banner" @change="updateBannerPreview">
            <div class="q-my-lg" v-if="imagePreview">
              <div class="text-weight-bold text-md">Ikon</div>
              <q-list bordered>
                <q-item>
                  <q-item-section>
                    <img :src="imagePreview" class="shadow-4 q-pa-xs bg-white"
                      style="width:80px;height:auto;object-fit:contain;" />
                  </q-item-section>
                  <q-space />
                  <q-item-section side>
                    <q-btn @click="removeImage" size="sm" color="red" glossy round icon="eva-trash-2" />
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
          <div class="q-my-lg" v-if="bannerPreview">
            <div class="text-weight-bold text-md">Banner</div>
            <q-list bordered>
              <q-item>
                <q-item-section>
                  <img :src="bannerPreview" class="shadow-4 q-pa-xs bg-white"
                    style="width:200px;height:auto;object-fit:contain;" />
                </q-item-section>
                <q-space />
                <q-item-section side>
                  <q-btn @click="removeBanner" size="sm" color="red" glossy round icon="eva-trash-2" />
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </section>
      </div>
      <q-footer class="bg-white q-pa-md">
        <q-btn :loading="loading" class="full-width" type="submit" label="Simpan Data" color="primary">
          <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
        </q-btn>
      </q-footer>
    </form>
  </q-page>
</template>

<script>
import { mapActions, mapState } from 'vuex'
export default {
  data() {
    return {
      category: this.$route.query.edit ? this.$store.getters['category/getById'](parseInt(this.$route.query.edit)) : '',
      formType: 'Add',
      form: {
        id: '',
        category_id: '',
        images: '',
        title: '',
        description: '',
        is_front: true,
        weight: 1,
        banner: '',
        remove_banner: false,
        remove_image: false,
      },
      modal: false,
      imagePreview: '',
      bannerPreview: '',
    }
  },
  watch: {
    'form.category_id'(val) {
      if (val) {
        this.unsetDataCategory()
      }
    }
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      errors: state => state.errors
    }),
    parentCategoryOptions() {
      return this.$store.getters['category/getParentCategoryOptions']
    },
    parentCategoryOptionsFiltered() {
      if (this.category) {
        return this.parentCategoryOptions.filter(el => el.value != this.category.id)
      }
      return []
    },
  },

  methods: {
    ...mapActions('category', ['categoryStore', 'getCategories', 'getCategory', 'categoryUpdate']),
    submit() {
      let formData = new FormData();
      formData.append('title', this.form.title)
      formData.append('category_id', this.form.category_id)
      formData.append('description', this.form.description)
      formData.append('images', this.form.images);
      formData.append('banner', this.form.banner);
      formData.append('weight', this.form.weight);
      formData.append('is_front', this.form.is_front);

      if (this.formType == 'Edit') {
        formData.append('remove_banner', this.form.remove_banner);
      }
      if (this.formType == 'Edit') {
        formData.append('remove_image', this.form.remove_image);
      }

      if (this.formType == 'Add') {
        formData.append('_method', 'POST')
        this.categoryStore(formData)
      }
      if (this.formType == 'Edit') {
        formData.append('_method', 'PUT')
        this.categoryUpdate({ id: this.form.id, data: formData })
      }
    },
    selectNewImage() {
      this.$refs.image.click()
    },
    selectNewBanner() {
      this.$refs.banner.click()
    },
    updateImagePreview() {

      let file = this.$refs.image.files[0]
      if (!file) return

      this.form.images = file

      const reader = new FileReader();

      reader.onload = (e) => {
        this.imagePreview = e.target.result;
      };

      reader.readAsDataURL(file);

    },
    updateBannerPreview() {
      let file = this.$refs.banner.files[0]
      if (!file) return

      this.form.banner = file

      const reader = new FileReader();

      reader.onload = (e) => {
        this.bannerPreview = e.target.result;
      };

      reader.readAsDataURL(file);

    },
    setData() {
      this.form.id = this.category.id
      this.form.title = this.category.title
      this.form.description = this.category.description ? this.category.description : ''
      this.form.weight = this.category.weight
      this.form.category_id = this.category.category_id ?? ''
      this.form.is_front = this.category.is_front
      this.form.is_special = this.category.is_special
      this.imagePreview = this.category.image ? this.category.image.src : ''
      this.bannerPreview = this.category.banner ? this.category.banner.src : ''

    },
    clearForm() {
      this.form.id = ''
      this.form.title = ''
      this.form.description = ''
      this.form.weight = 1,
        this.form.is_front = true
      this.form.is_special = ''
      this.form.images = '',
        this.form.banner = '',
        this.form.remove_banner = false

      this.imagePreview = ''
      this.bannerPreview = ''
    },
    removeImage() {
      this.imagePreview = ''
      this.form.images = ''
      this.form.remove_image = true
    },
    removeBanner() {
      this.bannerPreview = ''
      this.form.banner = ''
      this.form.remove_banner = true
    },
    unsetDataCategory() {
      if (this.form.banner) {
        this.removeBanner()
      }
      if (this.form.images) {
        this.removeImage()
      }
    }
  },
  created() {
    this.clearForm()

    if (this.$route.name == 'CategoryFormEdit') {
      this.formType = 'Edit'

      if (!this.category) {
        this.getCategory(this.$route.params.category_id).then((response) => {
          this.category = response.data.results
          this.setData()
        })
      } else {
        this.setData()
      }
    } else {
      this.formType = 'Add'
      this.category = {}
      this.clearForm()
    }

    if (this.parentCategoryOptions.length <= 1) {
      return this.$store.dispatch('category/getCategoriesWithChilds')
    }
  },
}
</script>
