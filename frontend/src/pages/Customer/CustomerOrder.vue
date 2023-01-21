<template>
  <q-page class="q-pb-xl">
    <q-header class="bg-primary" dark>
      <q-toolbar>
        <q-toolbar-title>
          <q-btn :to="{ name: 'CustomerAccount' }"
          flat round
          icon="eva-arrow-back" />
        Order
        </q-toolbar-title> 
      </q-toolbar>
    </q-header>
    <template v-if="orders.data.length">
    <q-list separator>
      <q-item class="bg-grey-2">
        <q-item-section side>#</q-item-section>
        <q-item-section>Invoice</q-item-section>
        <q-item-section class="mobile-hide">Total</q-item-section>
        <q-item-section>Status</q-item-section>
        <q-item-section side>Detail</q-item-section>
      </q-item>
      <q-item v-for="(order, index) in orders.data" :key="order.id">
        <q-item-section side top>
          {{ index+1 }}
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ order.order_ref }}</q-item-label>
          <q-item-label class="desktop-hide">{{ moneyIDR(order.grand_total) }}</q-item-label>
          <q-item-label caption>{{ order.created }}</q-item-label>
        </q-item-section>
        <q-item-section class="mobile-hide">{{ moneyIDR(order.grand_total) }}</q-item-section>
        <q-item-section>
          <div class="row">
              <q-badge class="text-center justify-center" style="min-width:90px;padding:4px;" :color="getOrderStatusColor(order.order_status)">{{ order.customer_status_label }}</q-badge>  
          </div>
        </q-item-section>
        <q-item-section side>
          <q-btn icon="eva-external-link-outline" round flat :to="{name: 'UserInvoice', params: {order_ref: order.order_ref}, query: { _rdr: $route.path }}"></q-btn>
        </q-item-section>
      </q-item>
    </q-list>
   <div class="flex justify-center q-py-xl">
     <q-btn outline :loading="orders.isLoadMore" v-if="orders.count > orders.data.length"  label="loadmore..." @click="loadMore" unelevated color="primary"></q-btn>
   </div>
    </template>
     <template v-else>
      <div class="text-center q-pt-xl q-mt-xl">Tidak ada data</div>
    </template>
    <q-inner-loading :showing="loading">
       
    </q-inner-loading>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
export default {
  name: 'CustomerOrderIndex',
  computed: {
    ...mapState({
      orders: state => state.order.customer_order,
      loading: state => state.loading
    }),
  },
  mounted() {
    if(!this.orders.data.length || this.orders.data.length <= this.orders.limit) {
      this.getCustomerOrders()
    }
  },
  methods: {
    ...mapActions('order', ['getCustomerOrders', 'getPaginateCustomerOrder']),
    loadMore() {
      this.getPaginateCustomerOrder(this.orders.data.length)
    }
  }
}
</script>
