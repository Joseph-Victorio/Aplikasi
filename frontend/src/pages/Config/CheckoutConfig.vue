<template>
  <q-card flat class="q-pa-sm">
    <q-card-section>
      <div class="flex items-center justify-between">
        <div class="text-subtitle1 text-weight-bold">Pengaturan Checkout</div>
      </div>
      <q-list class="q-mt-md">
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label>Whatsapp Checkout</q-item-label>
            <q-item-label caption>Memungkinan user untuk bisa checkout langsung via whatsapp ( Order tidak tersimpan di database )</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-toggle v-model="form.is_whatsapp_checkout" :label="form.is_whatsapp_checkout? 'Active' : 'Disabled'" left-label color="teal"></q-toggle>
          </q-item-section>
        </q-item>
        <q-item class="q-px-xs">
          <q-item-section>
            <q-item-label>Guest Checkout</q-item-label>
            <q-item-label caption>Memunginkan checkout tanpa harus login / register</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-toggle v-model="form.is_guest_checkout" :label="form.is_guest_checkout? 'Active' : 'Disabled'" left-label color="teal"></q-toggle>
          </q-item-section>
        </q-item>
      </q-list>
      <div class="flex justify-end q-mt-md">
        <q-btn unelevated size="12px" @click="updateData" label="Simpan Pengaturan" color="primary"></q-btn>
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data() {
    return {
      form: {
        is_whatsapp_checkout: false,
        is_guest_checkout: true
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
    setData() {
      if(this.config) {
        this.form.is_whatsapp_checkout = this.config.is_whatsapp_checkout
        this.form.is_guest_checkout = this.config.is_guest_checkout
      }
    },
  }
}
</script>
