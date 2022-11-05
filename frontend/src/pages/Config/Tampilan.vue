<template>
  <q-card flat>
    <q-card-section>
      <div class="text-subtitle1 text-weight-bold">Pengaturan Tampilan</div>
    </q-card-section>
    <q-list>
      <q-item>
        <q-item-section>
          <q-item-label>Tema</q-item-label>
        </q-item-section>
        <q-item-section side>
            <q-select outlined dense v-model="form.theme" :options="themes"></q-select>
        </q-item-section>
      </q-item>
      <q-item>
        <q-item-section>
          <q-item-label>Base Color</q-item-label>
        </q-item-section>
        <q-item-section side>
            <input ref="color" type="color" v-model="form.theme_color" style="width:110px;height:20px;"/>
        </q-item-section>
      </q-item>
      <q-item>
        <q-item-section>
          <q-item-label>
            Tampilan produk beranda
          </q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn-group unelevated>
            <q-btn size="12px" @click="changeHomeViewMode('grid')" unelevated 
            color="primary" 
            :outline="form.home_view_mode != 'grid'" 
            icon="grid_view">
              <q-tooltip>
                Grid Mode
              </q-tooltip>
            </q-btn>
            <q-btn size="12px" @click="changeHomeViewMode('list')" unelevated 
            color="primary" 
            :outline="form.home_view_mode != 'list'" 
            icon="eva-list">
              <q-tooltip>
                List Mode
              </q-tooltip>
            </q-btn>
          </q-btn-group>
        </q-item-section>
      </q-item>
      <q-item>
        <q-item-section>
          <q-item-label>
          Tampilan produk katalog
          </q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn-group unelevated>
            <q-btn size="12px" @click="changeProductViewMode('grid')" no-caps unelevated 
            color="primary" 
            :outline="form.product_view_mode != 'grid'" 
             icon="grid_view">
              <q-tooltip>
                Grid Mode
              </q-tooltip>
            </q-btn>
            <q-btn size="12px" @click="changeProductViewMode('list')" no-caps unelevated 
            color="primary" 
            :outline="form.product_view_mode != 'list'" 
            icon="eva-list">
              <q-tooltip>
                List Mode
              </q-tooltip>
            </q-btn>
          </q-btn-group>
        </q-item-section>
      </q-item>
      <q-item class="q-mt-md">
        <q-item-section>
          <q-item-label class="text-weight-medium">Social Proof Popup</q-item-label>
          <div class="q-mb-sm text-caption q-mt-sm">
            Pengaturan social proof notifikasi.
          </div>
        </q-item-section>
        <q-item-section side top>
          <q-toggle v-model="form.is_notifypro" :label="form.is_notifypro? 'Active' : 'Inactive'" left-label color="green"></q-toggle>
        </q-item-section>
      </q-item>
      <q-item v-if="form.is_notifypro">
        <q-item-section>
          <q-input outlined dense label="Jeda Waktu Tayang" mask="###" :hint="'Popup akan tayang setiap ' + form.notifypro_interval +' Detik'" v-model="form.notifypro_interval"></q-input>
        </q-item-section>
        <q-item-section>
          <q-input outlined dense label="Durasi Penayangan" mask="###" :hint="'Popup akan tayang selama ' + form.notifypro_timeout +' Detik'" v-model="form.notifypro_timeout"></q-input>
        </q-item-section>
      </q-item>
    </q-list>
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
      form: {
        theme: '',
        theme_color: '',
        home_view_mode:'',
        product_view_mode: '',
        is_notifypro: false,
        notifypro_interval: 20,
        notifypro_timeout: 4,
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
      this.setColor(val)
    }
  },
  mounted() {
    this.form.product_view_mode = this.config.product_view_mode
    this.form.home_view_mode = this.config.home_view_mode
    this.form.is_notifypro = this.config.is_notifypro
    this.form.notifypro_interval = this.config.notifypro_interval
    this.form.notifypro_timeout = this.config.notifypro_timeout
    this.form.theme = this.config.theme
    this.form.theme_color = this.config.theme_color
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
    setColor(clr) {
      this.$store.commit('SET_THEME_COLOR', clr)
    },
    saveTampilan() {
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
