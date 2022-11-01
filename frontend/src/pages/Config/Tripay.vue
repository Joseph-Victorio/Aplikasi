<template>
  <div class="q-pa-md bg-white box-shadow">
    <div class="flex items-center justify-between q-mb-xs">
      <div class="text-md text-weight-bold">Tripay Payment Gateway</div>
      <div>
        <q-toggle class="text-grey-8" v-model="form.is_payment_gateway" :label="form.is_payment_gateway? 'Active' : 'Inactive'" left-label @input="checkIsReady" color="blue"></q-toggle>
      </div>
    </div>
    <form @submit.prevent="updateDate">
      <div class="q-mb-md text-grey-7 text-caption">
        <div>Pengaturan Tripay payment Gateway, Silahkan daftar di TRIPAY untuk mendapatkan Kredensial, Anda dapat mendaftar melalui link berikut <span class="cursor-pointer bg-green-1 text-dark q-px-xs text-weight-medium q-px-xs" @click="daftarTripay">https://tripay.co.id/register</span></div>
      </div>
      <div class="q-pt-sm">
        <div class="q-gutter-y-sm">
          
          <q-select label="ENV MODE" filled :options="tripayModes" v-model="form.tripay_mode"></q-select>
          <q-input
          filled
          v-model="form.tripay_merchant_code"
          label="Tripay MERCHANT CODE"
          />
          <q-input
          filled
          v-model="form.tripay_api_key"
          label="Tripay API KEY"
          />
          <q-input
          filled
          v-model="form.tripay_private_key"
          label="Tripay PRIVATE KEY"
          />

          <div v-if="config.is_payment_gateway">
            <q-input filled :value="config.tripay_callback" readonly>
            <template v-slot:append>
              <q-btn icon="content_copy" @click="handleClickTripayCallback" unelevated size="18px" padding="4px" flat></q-btn>
            </template>
          </q-input>
            <div class="text-xs text-grey-8 q-pa-xs">Salin URL callback ke dashboard merchant tripay</div>
          </div>
        </div>
      </div>
      <div class="flex justify-end q-pt-lg">
        <q-btn unelevated size="12px" type="submit" label="Simpan Pengaturan" color="blue-7"></q-btn>
      </div>
    </form>
  </div> 
</template>

<script>
import { Api } from 'boot/axios'
import { copyToClipboard } from 'quasar'
export default {
  data () {
    return {
      tripayModes: ['sanbox', 'production'],
      form: {
        tripay_mode: '',
        tripay_api_key: '',
        tripay_private_key: '',
        tripay_merchant_code: '',
        is_payment_gateway: false
      },
    }
  },
  computed: {
    config: function() {
      return this.$store.state.config
    }
  },
  mounted() {
    if(this.config) {
      this.setConfig()
    }
  },
  methods: {
    setConfig() {
      if(this.config) {
        this.form.tripay_mode = this.config.tripay_mode
        this.form.tripay_api_key = this.config.tripay_api_key
        this.form.tripay_private_key = this.config.tripay_private_key
        this.form.tripay_merchant_code = this.config.tripay_merchant_code
        this.form.is_payment_gateway = this.config.is_payment_gateway
      }
      
    },
    daftarTripay() {
      let url = 'https://tripay.co.id/?ref=TP10450'
      window.open(url, '_blank')
    },
    handleClickTripayCallback() {
      copyToClipboard(this.config.tripay_callback)
        .then(() => {
          this.$q.notify({
            type: 'positive',
            message: 'berhasil menyalin url'
          })
        })
        .catch(() => {
          // fail
        })
    },
    updateDate() {
      let data = {...this.config, ...this.form}
      Api().post('config',  this.form).then(() => {
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })
        this.$store.dispatch('getAdminConfig')
      }).catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Gagal memperbarui data'
        })  
      })
    },
    checkIsReady(evt) {
      if(evt) {
        if(!this.isReady()) {
          this.form.is_payment_gateway = false
          this.$q.notify({
            type: 'negative',
            message: 'Silahkan lengkapi data kredensial untuk mengaktifkan payment gateway.'
          })
        }
      }
    },
    isReady() {
      return this.form.tripay_mode && this.form.tripay_api_key && this.form.tripay_private_key && this.form.tripay_merchant_code ? true : false
    },
  }
}
</script>