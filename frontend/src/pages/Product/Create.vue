<template>
  <q-page class="q-pb-xl">
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Tambah Produk
        </q-toolbar-title>
       
      </q-toolbar>
      </q-header>
      <form @submit.prevent="submit"> 
        <div class="q-pa-md q-gutter-y-sm">
            <q-input  
            filled 
            type="text" 
            v-model="form.title" 
            label="Nama Produk"
            :rules="requiredRules"
            ></q-input>
            <div class="text-xs text-red" v-if="errors.title"> {{ errors.title[0]}}</div>

            <div class="row items-center q-gutter-x-sm">
              <div class="col">
                <money-formatter v-model="form.price" prefix="Rp"/>
              </div>
              <div class="col">
                <money-formatter v-model="form.stock" label="Stok"/>
              </div>
              <div class="col">
                <money-formatter v-model="form.weight" label="Berat" suffix="GRAM"/>
              </div>
            </div>
              <q-select
              filled 
                v-model="form.category_id"
                :options="categories"
                label="Kategori"
                emit-value
                map-options
                class="q-pb-md"
              />
            <div class="q-mt-md q-mb-sm">
              <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
              <q-editor 
              v-model="form.description"
              />
              <div class="text-xs text-red" v-if="errors.description"> {{ errors.description[0]}}</div>
            </div>
            <section id="image-section" class="q-my-lg q-gutter-y-sm">
              <div>
                <q-btn label="Upload Gambar Produk" size="sm" color="primary" icon="eva-upload" class="mt-2 mr-2" type="button" @click.prevent="selectNewImage">
                </q-btn>
              </div>
                  
              <input type="file" class="hidden"
                                  ref="image"
                                  @change="updateImagePreview" multiple>
              <!-- <jet-input-error :message="form.errors.images" class="mt-2" /> -->

                  <q-list separator v-if="imagePreview.length" class="q-mt-md">
                    <q-item dense>
                      <q-item-section>
                        Gambar Utama
                      </q-item-section>
                    </q-item>
                  <q-item  v-for="(image, index) in imagePreview" :key="index">

                    <q-item-section>
                      <img :src="image" class="bg-white" style="width:100px;height:70px;object-fit:contain;"/>
                    </q-item-section>

                    <q-item-section side>
                        <q-btn @click.prevent="removeImage(index)" size="sm" round icon="eva-trash-2" glossy color="red"/>
                    </q-item-section>
                  </q-item>
                </q-list>
            </section>
        </div>
           <!-- Start Product Variants -->
        <div id="variants">
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
                      <q-input stack-label filled square required v-model="form.varians[varIndex].subvarian[subIndex].value" dense :label="form.varians[varIndex].subvarian[subIndex].label"></q-input>
    
                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required v-model="form.varians[varIndex].subvarian[subIndex].price" dense label="Tambahan Harga" mask="###########"></q-input>
                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required v-model="form.varians[varIndex].subvarian[subIndex].stock" dense label="Stok"  mask="#######"></q-input>
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
                      <q-input stack-label filled square required v-model="form.varians[vIndex].price" dense label="Tambahan Harga"  mask="###########"></q-input>
                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required v-model="form.varians[vIndex].stock" dense label="Stok"  mask="######"></q-input>
                    </q-item-section>

                  </q-item>
                </q-list>
              </q-list>
            </div>
          </div>
        </div>
      <!-- End Product Variants -->
        <!-- <textarea v-model="text" style="white-space:pre-wrap"/> -->
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
  name: 'ProductFormCreate',
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
        price: '',
        items: '',
        sku: ''
      },
      requiredRules: [
        val => (val && val.length > 0) || 'Field harus diisi.'
      ],
      form: {
        title: '',
        price: '',
        weight: '',
        stock: '',
        description: '',
        category_id:'',
        varians: [],
        images: [],
        sku: '',
        has_subvarian: false
      },
      imagePreview: [],
      variantModalForm: false,
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
    ...mapActions('product', ['productStore']),
    ...mapActions('category', ['getCategories']),
    ...mapActions('customerService', ['getCustomerServices']),
    onUpdateImage(data) {
      this.form.product_images.push(data)
    },
    onDeleteImage(idx) {
      this.form.product_images.splice(idx, 1)
    },
    onSubmitForm(data) {
      this.form.variants = data
      this.variantModalForm = false
    },
    deleteVarian(varIndex) {
      this.$q.dialog({
        title: 'Yakin akan menghapus data?',
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

      let tpl = { label: varian.subvarian[0].label, value: '', stock: 0, price: 0 }

      this.form.varians[varIndex].subvarian.push(tpl)
    },
    pushVarian() {
      this.form.varians.push({ has_subvarian: false,  label: this.form.varians[0].label, value: '', stock: 0, price: 0 })

    },
    handleAddVarian() {
      this.varianModal = true
    },
    addVarianProduk() {

      let varianArr = this.tempVarian.value.split(',')

      if(this.form.has_subvarian) {
        
        varianArr.forEach(v => {
          let varian = null
           varian = { has_subvarian: true, label: this.tempVarian.label, value: v, subvarian: [] }
  
          let subArr = this.tempSubvarian.value.split(',')
  
            subArr.forEach(el => {
              let sub = { label: this.tempSubvarian.label, value: el, stock: 0, price: 0  }
              varian.subvarian.push(sub)
            })
  
          this.form.varians.push(varian)
  
        })
      } else {

         varianArr.forEach(v => {
         
         let varian = null
           varian = { has_subvarian: false,  label: this.tempVarian.label, value: v, stock: 0, price: 0  }
 
          this.form.varians.push(varian)
  
        })
      }


      this.varianModal = false
    },
    // Submit Product
    submit() {

      let formData = new FormData();

      formData.append('title', this.form.title)
      formData.append('price', this.form.price)
      formData.append('weight', this.form.weight)
      formData.append('has_subvarian', this.form.has_subvarian)
      formData.append('stock', this.form.stock)
      formData.append('description', this.form.description)
      
      if(this.form.category_id) {
        formData.append('category_id', this.form.category_id)
      }

      if(this.form.varians.length > 0) {
        formData.append('varians', JSON.stringify(this.form.varians))
      }

      for(var j = 0; j < this.form.images.length; j++ ){
        let file = this.form.images[j];

        formData.append('images[' + j + ']', file);
      }

      this.productStore(formData)
    },
    selectNewImage() {
        this.$refs.image.click();
    },

    updateImagePreview() {

      let img = this.$refs.image.files

      for(let i=0;i<img.length;i++){

        this.form.images.push(img[i])

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imagePreview.push(e.target.result);
        };

        reader.readAsDataURL(img[i]);
        }
    },
    removeImage(index) {

      this.imagePreview = this.imagePreview.filter(function(el,i) {
        return i !== index;
      })
      this.form.images = this.form.images.filter(function(el,i) {
        return i !== index;
      })
    },
 
  },
  mounted() {
    this.getCategories()
  }
}
</script>
