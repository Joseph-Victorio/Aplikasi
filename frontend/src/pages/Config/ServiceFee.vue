<template>
  <q-card flat>
    <q-card-section>
      <div class="flex items-center justify-between">
          <div class="text-subtitle1 text-weight-bold">Pengaturan Biaya Layanan</div>
          <div>
            <q-toggle v-model="form.is_service_fee" :label="form.is_service_fee? 'ON' : 'OFF'" left-label color="teal" class="text-grey-8"></q-toggle>
          </div>
        <div class="text-caption text-grey-7">
        Biaya tambahan penggunaan layanan, akan ditambahkan pada pesanan pelanggan
        </div>
      </div>
     <form @submit.prevent="updateData" class="q-mt-md q-gutter-y-sm">
      <q-input
        filled
        v-model="form.service_fee_label"
        required
        label="Label"
        />
      <q-input
        filled
        v-model="form.service_fee"
        type="number"
        required
        min="0"
        label="Biaya Layanan"
        />
      <div class="flex justify-end q-mt-md">
        <q-btn unelevated size="12px" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
      </div>
    </form>
  </q-card-section>
  </q-card>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data() {
    return {
      form: {
        is_service_fee: false,
        service_fee: 0,
        service_fee_label: ''
      }
    }
  },
  computed: {
    config: function() {
      return this.$store.state.config
    }
  },
  mounted() {
    this.setData()
  },
  methods: {
    updateData() {
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
    setData() {
      if(this.config) {
        this.form.is_service_fee = this.config.is_service_fee
        this.form.service_fee = this.config.service_fee
        this.form.service_fee_label = this.config.service_fee_label
      }
    },
  }
}
</script>
