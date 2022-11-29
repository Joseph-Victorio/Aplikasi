<template>
  <q-page>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Edit Produk
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div class="q-px-md q-py-md">
      <div class="">
        <div class="text-weight-medium text-md q-mb-sm text-grey-7">Gambar Produk</div>
        <div class="row q-gutter-sm">

          <div class="box-image bordered cursor-pointer flex justify-center items-center" @click="selectNewImage">
            <q-icon name="add_a_photo" size="lg" color="grey"></q-icon>
          </div>
          <div 
          class="box-image relative cursor-pointer" 
          :class="{'feature-image-selected' : form.featured_index == index }"
          v-for="(image, index) in imagePreview" :key="index">
            <img :src="image" class="bg-white" @click="changeFeaturedImage(image, index)"/>
            <q-tooltip v-if="form.featured_index == index" class="bg-green text-white">Featured</q-tooltip>
            <div class="absolute-top-right">
              <q-btn dense @click.prevent="removeImage(image, index)" size="10px" unelevated icon="close" color="red" padding="1px"/>
            </div>
          </div>
        </div>
        <div class="text-xs q-py-sm text-grey" v-if="imagePreview.length">Untuk memilih featured image klik pada gambar</div>
      </div>
    </div>
    <form @submit.prevent="submit">
      <div class="q-px-md q-gutter-y-md">
         <q-input  
         outlined
          type="text" 
          v-model="form.title" 
          label="Nama Produk"
          required
        />
        <div class="row items-start q-gutter-x-sm">
          <div class="col">
            <money-formatter outlined v-model="form.price" prefix="Rp"/>
          </div>
          <div class="col">
            <money-formatter outlined v-model="form.stock" label="Stok"/>
          </div>
          <div class="col">
            <money-formatter outlined v-model="form.weight" label="Berat" suffix="Gram"/>
          </div>
        </div>

          <q-select
          outlined 
            v-model="form.category_id"
            :options="categories"
            label="Kategori"
            emit-value
            map-options
           class="q-pb-md"
            
          />
        <div class="q-mt-md q-mb-sm">
          <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
          <q-editor v-model="form.description"
          />
          <div class="text-xs text-red" v-if="errors.description"> {{ errors.description[0]}}</div>
        </div>
      </div>
        <input type="file" class="hidden" ref="image" @change="updateImagePreview" multiple>
    <!-- Start Product Variants -->
       <div id="variants" style="min-height:200px;">
          <div class="row items-center justify-between q-mt-xl q-pa-md bg-green-1">
            <div class="text-md2 text-weight-medium">Produk Variasi</div>
            <q-btn v-if="canAddVarian" label="Tambah Variasi" @click="varianModal = true" color="accent" size="12px"></q-btn>
          </div>
          <div v-if="form.varians.length">
            <div v-if="form.varians[0].has_subvarian">

              <div v-for="(varian, varIndex) in form.varians" :key="varIndex">
                <div class="row items-start justify-between bg-grey-2 q-pa-md q-pt-lg">
                    <div  class="text-weight-bold text-md">{{ form.varians[varIndex].label}} {{ form.varians[varIndex].value }}</div>
                  <div class="q-gutter-x-sm">
                    <q-btn unelevated size="10px" color="red" @click="deleteVarian(varIndex)">Hapus {{ form.varians[varIndex].value }}</q-btn>
                    <q-btn unelevated size="10px" color="teal" @click="pushSubVarian(varIndex)">Tambah Item</q-btn>
                  </div>
                </div>
              <div class="">
                <q-list class="bg-white q-pa-sm q-mt-xs" v-if="form.varians[varIndex].subvarian.length">
                  <q-item class="q-px-sm" v-for="(subvarian, subIndex) in form.varians[varIndex].subvarian" :key="subIndex">
                    <q-item-section side>
                        <q-btn round unelevated padding="2px" icon="remove" size="9px" color="red" @click="deleteSubvarian(varIndex, subIndex)"></q-btn>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label class="q-mb-xs">
                        <q-input stack-label filled square required v-model="form.varians[varIndex].subvarian[subIndex].value" dense :label="form.varians[varIndex].subvarian[subIndex].label"></q-input>
                      </q-item-label>
                      <q-item-label>
                      <money-formatter stack-label dense filled required v-model="form.varians[varIndex].subvarian[subIndex].weight"  label="Berat" suffix="Gram"/>
                      </q-item-label>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label class="q-mb-xs">
                        <money-formatter stack-label dense filled required v-model="form.varians[varIndex].subvarian[subIndex].price" prefix="Rp" label="Harga Jual"/>
                        
                      </q-item-label>
                      <q-item-label>
                      <money-formatter stack-label dense filled required v-model="form.varians[varIndex].subvarian[subIndex].stock"  label="Stok"/>

                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </div>
            </div>

            </div>
            <div v-else>
              <div class="row items-start justify-between bg-grey-2 q-pa-md q-pt-lg">
                    <div  class="text-weight-bold text-md">{{ form.varians[0].label}} </div>
                  <div class="q-gutter-x-sm">
                    <q-btn unelevated size="10px" color="teal" @click="pushVarian">Tambah Item</q-btn>
                  </div>
                </div>
              <q-list>
                <q-list class="bg-white q-pa-sm q-mt-xs">
                  <q-item  v-for="(varian, vIndex) in form.varians" :key="vIndex">
                    <q-item-section side>
                      <q-btn round unelevated padding="2px" icon="remove" size="9px" color="red" @click="deleteVarian(vIndex)"></q-btn>
                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required v-model="form.varians[vIndex].value" dense :label="form.varians[vIndex].label"></q-input>
    
                    </q-item-section>
                    <q-item-section>
                      <money-formatter stack-label dense filled required v-model="form.varians[vIndex].price" prefix="Rp" label="Harga Jual"/>
                    </q-item-section>
                    <q-item-section>
                      <money-formatter stack-label dense filled required v-model="form.varians[vIndex].stock" label="Stok"/>
                    </q-item-section>
                    <q-item-section>
                      <money-formatter stack-label dense filled required v-model="form.varians[vIndex].weight" label="Berat" suffix="Gram"/>
                    </q-item-section>

                  </q-item>
                </q-list>
              </q-list>
            </div>
          </div>
        </div>
      <!-- End Product Variants -->
    <q-footer class="bg-white q-pa-md">
       <q-btn color="primary" type="submit" :loading="loading" class="full-width" label="Simpan Data">
           <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
        </q-btn>
    </q-footer>
    </form>
    <q-dialog v-model="varianModal">
      <q-card class="card-medium">
        <div class="card-heading">Tambah varian</div>
        <form @submit.prevent="addVarianProduk">
          <q-card-section>
            
            <div>
              <div class="text-md">Varian</div>
              <q-input label="Label" v-model="tempVarian.label" placeholder="contoh: Ukuran"></q-input>
              <q-input label="Item" v-model="tempVarian.value" placeholder="contoh: Small, Medium, Large"></q-input>
              <div class="text-grey-7 text-xs q-py-xs">Untuk multiple item, gunakan sparator koma</div>
              
            </div>
            <div v-if="canToggleSubvarian">
              <q-checkbox v-model="form.has_subvarian" label="Subvarian?"></q-checkbox>
            </div>
            <div class="q-mt-md" v-if="mustHaveSubvarian"> 
              <div class="text-md">Subvarian</div>
              <q-input label="Label" v-model="tempSubvarian.label" placeholder="contoh: Warna"></q-input>
              <q-input label="Item" v-model="tempSubvarian.value" placeholder="contoh: Merah, Biru, Ungu"> </q-input>
              <div class="text-grey-7 text-xs q-py-xs">Untuk multiple item, gunakan sparator koma</div>
              
            </div>
            <div class="flex justify-end q-mt-md q-gutter-sm">
              <q-btn label="Tutup" v-close-popup flat color="primary"></q-btn>
              <q-btn unelevated label="Tambah" type="submit" color="primary"></q-btn>
            </div>
          </q-card-section>
        </form>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'ProductFormEdit',
  data () {
    return {
      varianModal: false,
      tempVarian: {
        label: '',
        value: '',
      },
      tempSubvarian: {
        label: '',
        value: '',
        stock: '',
        is_preorder: false,
        price: '',
        sku: '',
        items: ''
      },
      requiredRules: [
        val => (val && val.length > 0) || 'Field harus diisi.'
      ],
      product: null,
      form: {
        id: '',
        title: '',
        price: '',
        weight: '',
        stock: '',
        category_id: '',
        description: '',
        varians: [],
        images: [],
        del_images: [],
        has_subvarian: false,
        remove_varian:[],
        remove_subvarian:[],
        featured_index: 0,
        featured_asset: null

      },
      imagePreview: [],
      oldImages: [],
      variantModalForm: false,
      productOldImages: [],
      chatOptions: []
    }
  },
  computed: {
    errors: function() {
      return this.$store.state.errors
    },
    loading: function() {
      return this.$store.state.loading
    },
    categories() {
      return this.$store.getters['category/getValueOptions']
    },
    canToggleSubvarian() {
      if(this.form.varians.length) {
        if(this.form.varians[0].has_subvarian) {
          return false
        }
      }
      return true
    },
    mustHaveSubvarian() {
      if(this.form.varians.length) {
        if(this.form.varians[0].has_varian) {
          return true
        }
      }
      if(this.form.has_subvarian) {
        return true
      }
      return false
    },
    canAddVarian() {
      if(this.form.varians.length) {
        if(!this.form.varians[0].has_subvarian) {
          return false
        }
      }
      return true
    }
  },
  methods: {
    ...mapActions('product', ['productUpdate', 'getProductById']),
    ...mapActions('category', ['getCategories']),
     onUpdateImage(data) {
      this.form.product_images.push(data)
    },
    changeFeaturedImage(img, index) {
      this.form.featured_index = index

      if(img.startsWith('http')) {
        let asset = img.split('/')
        this.form.featured_asset = asset[asset.length-1]
      }else {
        this.form.featured_asset = null
      }

    },
    onDeleteProductOldImage(filename) {
      this.productOldImages = this.productOldImages.filter(k => k.filename != filename)
      this.form.del_product_images.push(filename)
    },
    onDeleteImage(idx) {
      this.form.product_images.splice(idx, 1)
    },
    deleteVarian(varIndex) {
      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Yakin akan menghapus varian',
        cancel: true
      }).onOk(() => {
        this.form.varians.splice(varIndex,1)
      })
    },
    deleteSubvarian(varIndex,subIndex) {

      this.form.varians[varIndex].subvarian.splice(subIndex,1)

      if(!this.form.varians[varIndex].subvarian.length) {
        this.form.varians.splice(varIndex, 1)
      }
    },
    pushSubVarian(varIndex) {
      let varian = this.form.varians[varIndex]

      let tpl = { label: varian.subvarian[0].label, value: '', stock: 0, price: varian.price ?? 0, weight: varian.weight?? 0 }

      this.form.varians[varIndex].subvarian.push(tpl)
    },
    pushVarian() {
      this.form.varians.push({ has_subvarian: false,  label: this.form.varians[0].label, value: '', stock: 0, price: this.form.price ?? 0, weight: this.form.weight ?? 0 })

    },
    addVarianProduk() {

      let defaultPrice = this.form.price ?? 0;
      let weight = this.form.weight ?? 0;
      let stock = this.form.stock ?? 0;

      let varianArr = this.tempVarian.value.split(',')

      if(this.form.has_subvarian) {
        
        varianArr.forEach(v => {
          let varian = null
           varian = { has_subvarian: true, label: this.tempVarian.label, value: v, subvarian: [] }
  
          let subArr = this.tempSubvarian.value.split(',')
  
            subArr.forEach(el => {
              let sub = { label: this.tempSubvarian.label, value: el, stock: stock, price: defaultPrice, weight: weight }
              varian.subvarian.push(sub)
            })
  
          this.form.varians.push(varian)
  
        })
      } else {

         varianArr.forEach(v => {
         
         let varian = null
           varian = { has_subvarian: false,  label: this.tempVarian.label, value: v, stock: stock, price: defaultPrice, weight: weight }
 
          this.form.varians.push(varian)
  
        })
      }


      this.varianModal = false
      this.tempVarian.value = ''
      this.tempSubvarian.value = ''
    },

    handleAddVarian() {
      this.varianModal = true
    },
    // submit
    submit() {
      let formData = new FormData();

      formData.append('id', this.form.id)
      formData.append('title', this.form.title)
      formData.append('price', this.form.price)
      formData.append('weight', this.form.weight)
      formData.append('has_subvarian', this.form.has_subvarian)
      formData.append('stock', this.form.stock)
      formData.append('description', this.form.description)
      formData.append('featured_index', this.form.featured_index)
      if(this.form.featured_asset) {
        formData.append('featured_asset', this.form.featured_asset)
      }

      if(this.form.category_id) {
        formData.append('category_id', this.form.category_id)
      }
      if(this.form.varians.length) {
        formData.append('varians', JSON.stringify(this.form.varians))
      }

      if(this.form.remove_varian.length) {
        formData.append('remove_varian', JSON.stringify(this.form.remove_varian))
      }
      if(this.form.remove_subvarian.length) {
        formData.append('remove_subvarian', JSON.stringify(this.form.remove_subvarian))
      }

      if(this.form.images.length > 0) {

        for( var i = 0; i < this.form.images.length; i++ ){
          let file = this.form.images[i];

          formData.append('images[' + i + ']', file);
        }
      }
      if(this.form.del_images.length > 0) {

        for( var j = 0; j < this.form.del_images.length; j++ ){
          let file = this.form.del_images[j];

          formData.append('del_images[' + j + ']', file);
        }
      }
      this.productUpdate(formData)
    },
    selectNewImage() {
        this.$refs.image.click();
    },

    updateImagePreview() {

      let img = this.$refs.image.files

      for(let i=0;i<img.length;i++){

        this.form.images.unshift(img[i])

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imagePreview.unshift(e.target.result);
        };

        reader.readAsDataURL(img[i]);
        }
    },
    removeImage(img, index) {

      if(this.form.featured_index == index) {
        if(index != 0) {
          this.form.featured_index--
        }else {
          this.form.featured_index = 0
        }
      }

     this.imagePreview.splice(index,1)

     if(img.startsWith('http')) {
       let asset = img.split('/')
       this.form.del_images.push(asset[asset.length-1])
       let cr = this.imagePreview[this.form.featured_index].split('/')
       this.form.featured_asset = cr[cr.length-1]
     }

    },
    setData() {
      this.form.id = this.product.id
      this.form.title = this.product.title
      this.form.price = this.product.price
      this.form.weight = this.product.weight
      this.form.stock = this.product.stock
      this.form.category_id = this.product.category_id
      this.form.description = this.product.description
      this.form.varians = this.product.varians
      this.form.has_subvarian = this.product.varians.length ? this.product.varians[0].has_subvarian : false
      
      this.imagePreview = this.product.assets.map(el => el.src)

      this.form.featured_index =  this.product.assets.findIndex(el => el.variable == 'featured')

    },
  },
  mounted() {
    this.getProductById(this.$route.params.id).then((response) => {
      this.product = response.data.results
      this.setData() 
    })
    this.getCategories()
  },
}
</script>
