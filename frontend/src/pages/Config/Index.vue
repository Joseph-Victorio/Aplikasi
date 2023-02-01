<template>
  <q-page class="bg-grey-1 q-pb-lg">
    <q-header class="bg-brand">
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Konfigurasi Aplikasi
        </q-toolbar-title>
      </q-toolbar>
      <div class="bg-white box-shadow text-dark">
        <q-tabs v-model="tab" outside-arrows mobile-arrows active-color="primary">
          <q-tab v-for="item in tabs" :key="item.value" :name="item.value" :label="item.label" no-caps></q-tab>
        </q-tabs>
      </div>
      </q-header>
        <q-tab-panels v-model="tab" animated class="bg-transparent">
          <q-tab-panel class="q-pa-none" name="BasicConfig" >
            <BasicConfig />
          </q-tab-panel>

          <q-tab-panel class="q-pa-none" name="ShippingConfig">
            <ShippingConfig />
          </q-tab-panel>
          <q-tab-panel class="q-pa-none" name="Local">
            <LocalShipping />
          </q-tab-panel>

          <q-tab-panel class="q-pa-none" name="Notifikasi">
            <notification />
          </q-tab-panel>
          <q-tab-panel class="q-pa-none" name="Order">
            <OrderConfig />
          </q-tab-panel>
          <q-tab-panel class="q-pa-none" name="Checkout">
              <checkout-config  />
          </q-tab-panel>
          <q-tab-panel class="q-pa-none" name="Fee">
             <service-fee />
          </q-tab-panel>
          <q-tab-panel class="q-pa-none" name="System">
            <system-update />
          </q-tab-panel>
        </q-tab-panels>
      <q-inner-loading :showing="loading">
      </q-inner-loading>
  </q-page>
</template>

<script>
import ShippingConfig from './ShippingConfig.vue'
import BasicConfig from './BasicConfig.vue'
import CheckoutConfig from './CheckoutConfig.vue'
import Notification from './NotificationConfig.vue'
import OrderConfig from './OrderConfig.vue'
import SystemUpdate from './SystemUpdate.vue'
import LocalShipping from './LocalShippingConfig.vue'
import ServiceFee from './ServiceFeeConfig.vue'
 export default {
  name: 'AppConfigIndex',
  components: { ShippingConfig, BasicConfig, Notification, OrderConfig, SystemUpdate, CheckoutConfig, LocalShipping, ServiceFee },
  data() {
    return {
      tab: 'BasicConfig',
      tabs: [ 
        { value: 'BasicConfig', label: 'Basic'},
        { value: 'ShippingConfig', label: 'Ekspedisi'}, 
        { value: 'Local', label: 'Pengiriman Lokal'}, 
        { value:'Notifikasi', label: 'Notifikasi'}, 
        { value: 'Order', label: 'Order Config' }, 
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
