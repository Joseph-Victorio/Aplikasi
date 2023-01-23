<template>
  <q-page padding>
    <q-header class="bg-brand">
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Edit Post
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
        <q-editor v-model="form.body"
        :toolbar="[
          ['left','center','right','justify'],
          ['bold','italic','underline','strike'],
          ['quote', 'unordered', 'ordered', 'outdent', 'indent', 'link', 'removeFormat', 'subscript', 'superscript',
          {
              icon: $q.iconSet.editor.formatting,
              list: 'no-icons',
              options: [
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'p',
                'code'
              ]
            }, 'fullscreen', 'viewsource',
          ],
        ]"
        ></q-editor>
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
    <q-inner-loading :showing="loading">
       
    </q-inner-loading>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'PostCreate',
  data() {
    return {
      loading: false,
      form: {
        id: '',
        title: '',
        tags: '',
        body: '',
        image: '',
        del_image: false,
        is_listing: false,
        is_promote: true,
        category: ''
      },
      imagePreview: ''
    }
  },
  methods: {
    ...mapActions('post', ['updatePost', 'getSinglePost']),
    submitPost() {
      this.updatePost(this.form)
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
      this.form.del_image = true
    },
    getPostData() {
      this.loading = true
      this.getSinglePost(this.$route.params.id).then(response => {
        this.loading = false
        if(response.status == 200) {
          let responseData = response.data.results
          this.form.id = responseData.id
          this.form.title = responseData.title
          this.form.category = responseData.category ? responseData.category : ''
          this.form.tags = responseData.tags
          this.form.is_promote = responseData.is_promote
          this.form.is_listing = responseData.is_listing
          this.form.body = responseData.body
          this.imagePreview = responseData.image_url
        }
      }).catch(() => {
        this.loading = false
      })

    }
  },
  created() {
    this.getPostData()
  }
}
</script>
