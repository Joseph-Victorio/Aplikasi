<template>
  <q-card flat class="q-pa-sm">
    <q-card-section>
      <div class="text-subtitle1 text-weight-bold">Pengaturan Tema dan Tampilan</div>
      <q-list>
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label class="text-weight-medium">Tema</q-item-label>
            <q-item-label caption>Konfigurasi default tema</q-item-label>
          </q-item-section>
          <q-item-section side>
              <q-select style="min-width:110px;" outlined dense v-model="form.theme" :options="themes"></q-select>
          </q-item-section>
        </q-item>
        
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label class="text-weight-medium">Brand Color</q-item-label>
            <q-item-label caption>Konfigurasi warna Address bar, pwa theme dan heading</q-item-label>
          </q-item-section>
          <q-item-section side>
            <div class="cursor-pointer img-thumbnail" :style="`width:110px;height:30px;background-color:${form.theme_color}`">
              <q-popup-proxy transition-show="scale" transition-hide="scale">
                <q-color no-header v-model="form.theme_color" />
              </q-popup-proxy>
            </div>
          </q-item-section>
        </q-item>
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label class="text-weight-medium">Primary Color</q-item-label>
            <q-item-label caption>Terdapat pada title, button</q-item-label>
          </q-item-section>
          <q-item-section side>
            <div class="cursor-pointer img-thumbnail" :style="`width:110px;height:30px;background-color:${form.primary_color}`">
              <q-popup-proxy transition-show="scale" transition-hide="scale">
                <q-color v-model="form.primary_color" />
              </q-popup-proxy>
            </div>
          </q-item-section>
        </q-item>
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label class="text-weight-medium">Secondary Color</q-item-label>
            <q-item-label caption>Terdapat pada harga produk, badge, diskon</q-item-label>
          </q-item-section>
          <q-item-section side>
            <div class="cursor-pointer img-thumbnail" :style="`width:110px;height:30px;background-color:${form.secondary_color}`">
              <q-popup-proxy transition-show="scale" transition-hide="scale">
                <q-color v-model="form.secondary_color" />
              </q-popup-proxy>
            </div>
          </q-item-section>
        </q-item>
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label class="text-weight-medium">Accent Color</q-item-label>
            <q-item-label caption>Terdapat pada rating, varian, produk review</q-item-label>
          </q-item-section>
          <q-item-section side>
            <div class="cursor-pointer img-thumbnail" :style="`width:110px;height:30px;background-color:${form.accent_color}`">
              <q-popup-proxy transition-show="scale" transition-hide="scale">
                <q-color v-model="form.accent_color" />
              </q-popup-proxy>
            </div>
          </q-item-section>
        </q-item>
        <q-item class="q-mt-md q-px-xs">
          <q-item-section>
            <q-item-label class="text-weight-medium">Auto Approved Produk Review</q-item-label>
          </q-item-section>
          <q-item-section side top>
            <q-toggle v-model="form.review_auto_approved" :label="form.review_auto_approved? 'Active' : 'Disabled'" left-label color="teal"></q-toggle>
          </q-item-section>
        </q-item>
        <q-item class="q-mt-md q-px-xs">
          <q-item-section>
            <div class="text-weight-bold text-subtitle1 ">Social Proof Popup</div>
            <div class="text-caption text-grey-7">
              Pengaturan social proof notifikasi.
            </div>
          </q-item-section>
          <q-item-section side top>
            <q-toggle v-model="form.is_notifypro" :label="form.is_notifypro? 'Active' : 'Disabled'" left-label color="teal"></q-toggle>
          </q-item-section>
        </q-item>
        <q-item v-if="form.is_notifypro" class="q-px-none">
          <q-item-section>
            <q-input filled label="Jeda Waktu Tayang" mask="###" :hint="'Popup akan tayang setiap ' + form.notifypro_interval +' Detik'" v-model="form.notifypro_interval"></q-input>
          </q-item-section>
          <q-item-section>
            <q-input filled label="Durasi Penayangan" mask="###" :hint="'Popup akan tayang selama ' + form.notifypro_timeout +' Detik'" v-model="form.notifypro_timeout"></q-input>
          </q-item-section>
        </q-item>
        <div class="q-mt-lg">
         <div class="text-subtitle1 text-weight-bold">Produk Beranda</div>
         <div class="text-caption text-grey-7">Pengaturan tampilan produk beranda</div>
         <q-item class="q-px-none q-mt-xs">
           <q-item-section>
             <q-item-label>
               <q-input filled v-model="form.home_product_limit" label="Produk ditampilkan"></q-input>
             </q-item-label>
           </q-item-section>
           <q-item-section>
             <q-item-label>
               <q-select filled v-model="form.home_product_sort" label="Urutan Produk" 
               :options="sorts" 
               emit-value map-options
               ></q-select>
             </q-item-label>
           </q-item-section>
           <q-item-section>
             <q-item-label>
               <q-select filled v-model="form.home_view_mode" label="Tampilan Mode" :options="[{ value: 'grid', label: 'Grid Mode'}, {value: 'list', label: 'List Mode'}]" emit-value map-options></q-select>
             </q-item-label>
           </q-item-section>
         </q-item>
       </div>
       <div class="q-mt-md">
        <div class="text-subtitle1 text-weight-bold">Produk Katalog</div>
          <div class="text-caption text-grey-7">Pengaturan tampilan produk katalog</div>
          <q-item class="q-px-none q-mt-xs">
            <q-item-section>
              <q-item-label>
                <q-input filled v-model="form.catalog_product_limit" label="Produk ditampilkan"></q-input>
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                <q-select filled v-model="form.catalog_product_sort" label="Urutan Produk" 
                :options="sorts" 
                emit-value map-options
                ></q-select>
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                <q-select filled v-model="form.product_view_mode" label="Tampilan Mode" :options="[{ value: 'grid', label: 'Grid Mode'}, {value: 'list', label: 'List Mode'}]" emit-value map-options></q-select>
              </q-item-label>
            </q-item-section>
          </q-item>
        </div>
      </q-list>
    </q-card-section>
    <q-card-section class="flex justify-end">
      <q-btn unelevated size="12px" label="Simpan Pengaturan" color="primary" @click="saveTampilan"></q-btn>
    </q-card-section>
  </q-card>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data () {
    return {
      sorts: [
        { value: 'DESC', label: 'Terbaru'},
        { value: 'ASC', label: 'Terlama'},
        { value: 'RANDOM', label: 'Acak'},
      ],
      form: {
        theme: '',
        theme_color: '',
        primary_color: '',
        secondary_color: '',
        accent_color: '',
        home_view_mode:'',
        product_view_mode: '',
        is_notifypro: false,
        notifypro_interval: 20,
        notifypro_timeout: 4,
        review_auto_approved: false,
        catalog_product_limit: 10,
        catalog_product_sort: 'DESC',
        home_product_limit: 10,
        home_product_sort: 'DESC',
        review_auto_approved: false
      }
    }
  },
  computed: {
    config: function() {
      return this.$store.state.config
    },
    themes() {
      return this.$store.state.themes
    },
  },
   watch: {
    'form.theme_color': function(val) {
      this.$store.commit('SET_THEME_COLOR', val)
    },
    'form.primary_color': function(val) {
      this.$store.commit('SET_PRIMARY_COLOR', val)
    },
    'form.secondary_color': function(val) {
      this.$store.commit('SET_SECONDARY_COLOR', val)
    },
    'form.accent_color': function(val) {
      this.$store.commit('SET_ACCENT_COLOR', val)
    },
  },
  mounted() {
    this.form.product_view_mode = this.config.product_view_mode
    this.form.home_view_mode = this.config.home_view_mode
    this.form.is_notifypro = this.config.is_notifypro
    this.form.notifypro_interval = this.config.notifypro_interval
    this.form.notifypro_timeout = this.config.notifypro_timeout
    this.form.theme = this.config.theme
    this.form.theme_color = this.config.theme_color
    this.form.primary_color = this.config.primary_color
    this.form.secondary_color = this.config.secondary_color
    this.form.accent_color = this.config.accent_color
    this.form.review_auto_approved = this.config.review_auto_approved
    this.form.catalog_product_limit = this.config.catalog_product_limit
    this.form.catalog_product_sort = this.config.catalog_product_sort
    this.form.home_product_limit = this.config.home_product_limit
    this.form.home_product_sort = this.config.home_product_sort
  },
  methods: {
    changeHomeViewMode(str) {
      this.form.home_view_mode = str
    },
    changeProductViewMode(str) {
      this.form.product_view_mode = str
    },
    handleChangeColor() {
      this.$refs.color.click()
    },
    saveTampilan() {
      localStorage.removeItem('__nextshop_theme')
      
      Api().post('config', this.form).then(response => {
        if(response.status == 200) {
          this.$store.dispatch('getAdminConfig')
        }
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })

      }).catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Gagal memperbarui data'
        })
      })
    }
  }
}
</script>
