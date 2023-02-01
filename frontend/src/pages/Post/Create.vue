<template>
  <q-page padding>
    <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Tambah Post
        </q-toolbar-title>
        
      </q-toolbar>
    </q-header>
    <form @submit.prevent="submitPost" class="q-gutter-y-md">
      <q-input filled v-model="form.title" label="Title" required></q-input>
      <q-input filled v-model="form.tags" label="Kategori"></q-input>
      <q-list>
        <q-item class="q-px-xs">
          <q-item-section side>
            <q-item-label>
              <q-toggle v-model="form.is_promote">Tampil Diberanda</q-toggle>
            </q-item-label>
          </q-item-section>
          <q-item-section>
            <q-item-label>
              <q-toggle v-model="form.is_listing">Tampil Dihalaman</q-toggle>
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>

      <div>
        <div class="label-text">
          Konten
        </div>
        <ContentEditor @update="(val) => form.body = val" :content="form.body"/>
      </div>

    <div style="min-height: 100px;">
      <q-btn label="Upload Gambar" size="sm" color="primary" icon="eva-upload" class="mt-2 mr-2" type="button" @click.prevent="selectNewImage" />
        <q-list v-if="imagePreview" class="q-py-md">
          <q-item>
            <q-item-section>
              <img :src="imagePreview" class="shadow-4 q-pa-xs bg-white" style="width:100px;height:70px;object-fit:cover;"/>
            </q-item-section>
            <q-space />
            <q-item-section side>
                <q-btn @click="removeImage" size="sm" color="red" glossy round icon="eva-trash-2" />
            </q-item-section>
          </q-item>
        </q-list> 
      </div>
     <q-footer class="bg-white q-pa-md">
        <q-btn :loading="loading" label="Simpan Data" type="submit" color="primary" class="full-width"></q-btn>
      </q-footer>
    </form>
    <input type="file" class="hidden" ref="image" @change="updateImagePreview" />
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ContentEditor from 'components/ContentEditor.vue'
export default {
  name: 'PostCreate',
  components: { ContentEditor },
  data() {
    return {
      form: {
        title: '',
        tags: '',
        body: '',
        image: '',
        is_listing: true,
        is_promote: true
      },
      imagePreview: ''
    }
  },
  computed: {
    loading() {
      return this.$store.state.loading
    }
  },
  methods: {
    ...mapActions('post', ['addPost']),
    submitPost() {
      this.$store.commit('SET_LOADING', true)
      this.addPost(this.form)
    },
    updateImagePreview() {
     this.form.image = this.$refs.image.files[0]
     if(!this.form.image) return

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imagePreview = e.target.result;
        };

        reader.readAsDataURL(this.$refs.image.files[0]);
    },
    selectNewImage () {
      this.$refs.image.click()
    },
    removeImage() {
      this.imagePreview = ''
      this.form.image = ''
    }
  }
}
</script>
