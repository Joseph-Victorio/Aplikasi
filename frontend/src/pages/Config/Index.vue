<template>
  <q-page class="bg-grey-2 q-pb-lg">
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Konfigurasi Aplikasi
        </q-toolbar-title>
      </q-toolbar>
      <div class="bg-white box-shadow text-dark">
        <q-tabs v-model="tab" outside-arrows>
          <q-tab v-for="item in tabs" :key="item.value" :name="item.value" :label="item.label" no-caps></q-tab>
        </q-tabs>
      </div>
      </q-header>
        <q-tab-panels v-model="tab" animated class="">
          <q-tab-panel name="Tampilan" >
              <tampilan />
          </q-tab-panel>

          <q-tab-panel name="Ekspedisi">
            <shipping />
          </q-tab-panel>
          <q-tab-panel name="Local">
            <LocalShipping />
          </q-tab-panel>

          <q-tab-panel name="Notifikasi">
            <notification />
          </q-tab-panel>
          <q-tab-panel name="Tripay">
            <tripay />
          </q-tab-panel>
          <q-tab-panel name="Checkout">
              <checkout-config  />
          </q-tab-panel>
          <q-tab-panel name="Fee">
             <service-fee />
          </q-tab-panel>
          <q-tab-panel name="System">
            <system-update />
          </q-tab-panel>
        </q-tab-panels>
      <!-- <div class="q-gutter-y-lg q-pt-md" v-if="config">
        
        
        
       
      </div> -->
      <q-inner-loading :showing="loading">
      </q-inner-loading>
  </q-page>
</template>

<script>
import Shipping from './Shipping.vue'
import Tampilan from './Tampilan.vue'
// import Theming from './Theming.vue'
import CheckoutConfig from './CheckoutConfig.vue'
import Notification from './Notification.vue'
import Tripay from './Tripay.vue'
import SystemUpdate from './SystemUpdate.vue'
import LocalShipping from './LocalShipping.vue'
import ServiceFee from './ServiceFee.vue'
 export default {
  name: 'AdminConfig',
  components: { Shipping, Tampilan, Notification, Tripay, SystemUpdate, CheckoutConfig, LocalShipping, ServiceFee },
  data() {
    return {
      tab: 'Tampilan',
      tabs: [ 
        { value: 'Tampilan', label: 'Basic'},
        { value: 'Ekspedisi', label: 'Ekspedisi'}, 
        { value: 'Local', label: 'Pengiriman Lokal'}, 
        { value:'Notifikasi', label: 'Notifikasi'}, 
        { value: 'Tripay', label: 'Tripay' }, 
        { value: 'Checkout', label: 'Checkout'}, 
        { value: 'Fee', label: 'Fee Aplikasi'}, 
        { value: 'System', label: 'System Update'}
      ]
    }
  },
  computed: {
    loading() {
      return this.$store.state.loading
    },
    config() {
      return this.$store.state.config
    }
  },
  created() {
    if(!this.config) {
      this.$store.dispatch('getAdminConfig')
    }
  },
}
</script>
