<template>
  <q-page class="q-pb-xl">
    <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Pesanan
        </q-toolbar-title>
      </q-toolbar>
      <div class="box-shadow bg-white text-dark">
        <q-tabs v-model="filter" active-color="primary" outside-arrows>
          <q-tab v-for="option in options" :key="option.value" :name="option.value" :label="option.label" no-caps></q-tab>
        </q-tabs>
      </div>
    </q-header>
  <div class="q-py-md auto-padding-side">
      <div class="q-gutter-x-sm q-mt-sm">
          <q-input v-model="search" placeholder="Ketik no invoice atau ponsel" dense borderless clearable class="bg-grey-3 q-px-sm" @keypress.enter="handleSearchOrder" @clear="clearSearch">
            <template v-slot:append>
              <q-icon name="eva-search" class="cursor-pointer" @click="handleSearchOrder"></q-icon>
            </template>
          </q-input>
      </div>
    </div>
    <template v-if="orders.count > 0">
      <div>
        <q-separator></q-separator>
        <q-list separator>
          <q-item class="bg-grey-2">
            <q-item-section side class="gt-xs">
            #
            </q-item-section>
            <q-item-section>
              <q-item-label>Detail</q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-item-label>Actions</q-item-label>
            </q-item-section>
          </q-item>
          <q-item v-for="(order, index) in orders.data" :key="index">
            <q-item-section side top class="gt-xs">
              {{ index+1 }}
            </q-item-section>
            <q-item-section top>
              <div>
                <table class="dense">
                  <tr>
                    <td>Pelanggan</td>
                    <td>{{ order.customer_name }}</td>
                  </tr>
                  <tr>
                    <td>Invoice</td>
                    <td>{{ order.order_ref}}</td>
                  </tr>
                  <tr>
                    <td>Tanggal</td>
                    <td>{{ order.created }}</td>
                  </tr>
                  <tr>
                    <td>Total</td>
                    <td>{{ moneyIDR(order.grand_total) }}</td>
                  </tr>
                  
                  <tr>
                    <td>Status</td>
                    <td>
                      <q-badge :color="getOrderStatusColor(order.order_status)">{{ order.status_label }}</q-badge>
                    </td>
                  </tr>
                  
                  <tr>
                    <td>Pembayaran</td>
                    <td> 
                      <q-badge :color="getOrderStatusColor(order.transaction.status)">{{ order.transaction.status_label }} </q-badge>
                      
                      <span class="text-xs q-pl-xs">({{ order.transaction? order.transaction.payment_method.split('_').join(' ') : '' }})</span>
                    </td>
                  </tr>
                  <tr>
                    <td>Pengiriman</td>
                    <td>{{ order.shipping_courier_name == 'COD' ? 'Antar Kurir Pribadi' : order.shipping_courier_name }}</td>
                  </tr>
                  
                  <tr v-if="order.shipping_courier_code ">
                    <td>No Resi</td>
                    <td>{{ order.shipping_courier_code }}</td>
                  </tr>
                </table>
              </div>
            </q-item-section>
            <q-item-section side top>
              <div class="q-gutter-xs column order-btn-group">
                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" label="Detail" color="teal" :to="{name: 'AdminOrderShow', params: {order_ref: order.order_ref}}"></q-btn>

                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" @click="handleFollowUp(order)" :label="messageButtonLabel(order.order_status)" color="pink"></q-btn>


                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" v-if="canInputResi(order)" label="Input Resi" color="amber-8" @click="handleInputResi(order)"></q-btn>

                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" v-if="canShip(order)" :label="order.shipping_courier_name == 'COD' ? 'Antar COD' : 'Kirim'" color="blue" @click="handleKirim(order)"></q-btn>

                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" v-if="canComplete(order)" label="Order Selesai" color="green" @click="handleCompletionOrder(order)"></q-btn>

                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" v-if="canConfirm(order)" label="Konfirmasi" color="purple" @click="handleConfirmationOrder(order.id)"></q-btn>

                  <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" v-if="canCancelOrder(order)" label="Batalkan" color="red" @click="handleCancelOrder(order)"></q-btn>

                  <!-- <q-btn unelevated no-caps padding="6px 12px" size="12px" label="Hapus" color="red-7" @click="handleDeleteOrder(order.id)"></q-btn> -->
              </div>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
      <div class="flex justify-center q-py-xl">
        <q-btn outline :loading="orders.isLoadMore" v-if="orders.count > orders.data.length" label="loadmore..." @click="loadMore" unelevated color="primary"></q-btn>
      </div>
    </template>
      <div v-if="orders.ready && orders.count <= 0" class="text-center q-pt-xl">Tidak ada data</div>
    <q-dialog v-model="followUpModal" persistent>
      <follow-up @close="followUpModal= false" :order="currentOrder" />
    </q-dialog>

    <q-dialog v-model="inputResiModal">
      <q-card square style="width:100%;max-width:420px;">
        <div class="q-px-md q-py-sm bg-dark text-white text-weight-bold">Input Resi  <span v-if="orderSelected"># {{ orderSelected.order_ref }}</span></div>
       <form @submit.prevent="submitResi" >
        <q-card-section>
          <div class="text-grey-8">No Resi</div>
            <q-input 
            outlined
            v-model="form.resi"
            :rules="[val => val && val.length > 0 || 'Wajib diisi']"
            />
            <q-checkbox label="Update Status ( Dikirim )" v-model="form.update_to_ship"></q-checkbox>
          <div class="flex justify-end q-mt-sm q-gutter-x-sm">
            <q-btn outline label="Batal" @click.prevent="closeModal" color="primary"></q-btn>
            <q-btn unelevated type="submit" label="Simpan" color="primary"></q-btn>
          </div>
        </q-card-section>
       </form>
      </q-card>
    </q-dialog>
    <q-inner-loading :showing="loading">
       
    </q-inner-loading>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import FollowUp from './FollowUp.vue'
export default {
  name: 'OrderIndex',
  components: { FollowUp },
  data() {
    return {
      isFilter: true,
      options: [
       { value: 'ALL', label: 'Semua'},
       { value: 'PENDING', label: 'Pending' },
       { value: 'TOSHIP', label: 'Perlu Dikirim' },
       { value: 'SHIPPING', label: 'Dikirim'}, 
       { value: 'COMPLETE', label: 'Selesai'},
       { value: 'CANCELED', label: 'Batal'}
        ],
      inputResiModal: false,
      orderSelected: '',
      followUpModal: false,
      currentOrder: null,
      search: '',
      filter: 'ALL',
      form: {
        order_id: '',
        resi: '',
        status: '',
        update_to_ship: false
      },
    }
  },
  watch: {
    'filter': function(newVal, oldVal) {
      if(newVal != oldVal) {
        this.form.status = newVal
        localStorage.setItem('order_filter', newVal)
        this.filterOrder(newVal)
      }
    },
  },
  computed: {
    ...mapState({
      orders: state => state.order.orders,
      loading: state => state.loading
    }),
    isMobile() {
      return window.innerWidth <= 800
    }
  },
  created() {
    if(this.orders.data.length <= this.orders.limit) {

      if(this.$route.query.filter) {
        this.setFilter(this.$route.query.filter)
        this.filterOrder(this.filter)
  
      } else if(localStorage.getItem('order_filter')) {

        this.setFilter(localStorage.getItem('order_filter'))
        this.filterOrder(this.filter)

      }else {
        this.getOrders()
      }

    }
  },
  methods: {
    ...mapActions('order', ['getOrders', 'getPaginateOrder', 'getPaginateFilterOrder', 'destroyOrder', 'acceptPayment', 'inputResi', 'updateStatusOrder', 'searchOrder', 'filterOrder', 'cancelOrder']),
    loadMore() {
      this.getPaginateOrder({ filter: this.filter, skip: this.orders.data.length })
    },
    clearSearch() {
      this.setFilter('ALL')
      this.filterOrder(this.filter)
    },
    setFilter(val) {
      this.filter = val
      localStorage.setItem('order_filter', val)
    },
    handleSelectMode(evt) {
      this.isFilter = !this.isFilter
      this.search = ''
      this.setFilter('ALL')
    },
    changeTab(evt) {
      this.$router.push({ name: 'OrderIndex', query: { filter: evt }})
      this.search = ''
      this.filterOrder(evt)
    },
    handleKirim(order) {
      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Akan mengirim pesanan sekarang?, ini akan merubah status pesanan menjadi "sedang dikirim"',
        cancel: true,
      }).onOk(() => {
        this.form.status = 'SHIPPING'
        this.form.order_id = order.id
        this.handleUpdateStatusOrder()
      })
    },
    handleUpdateStatusOrder() {
      this.$store.commit('SET_LOADING', true)
      this.updateStatusOrder(this.form).then(() => {
        this.setFilter(this.form.status)
        this.filterOrder(this.filter)
        
      }).finally(() =>  this.$store.commit('SET_LOADING', false))
    },
    handleCancelOrder(order) {
      this.$q.dialog({
        title: 'Konfirmasi Pembatalan order',
        message: 'Akan membatalkan order ini?, perubahan ini tidak dapat dikembalikan',
        cancel: true,
      }).onOk(() => {
        this.form.status = 'CANCELED'
        this.form.order_id = order.id
        this.cancelOrder(order.id).then(() => {
          this.setFilter(this.form.status)
          this.filterOrder(this.filter)
        })
      })
    },
    canShip(order) {
      console.log(order);
      if(order.shipping_courier_name == 'COD') {
        if(order.order_status == 'TOSHIP' || order.order_status == 'UNPAID') {
          return true
        }
        return false
      } else{
        if(order.order_status == 'TOSHIP') {
          return true
        }
      }
    },
    canComplete(order) {
      return order.order_status == 'SHIPPING' ? true : false
    },
    canCancelOrder(order) {
      return order.order_status == 'UNPAID'
    },
    handleCompletionOrder(order) {
    
      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Ini akan merubah status pesanan menjadi "selesai" dan status pembayaran jadi "Dibayar" apabila menggunakan pembayaran COD',
        cancel: true,
      }).onOk(() => {
        this.form.status = 'COMPLETE'
        this.form.order_id = order.id
        this.handleUpdateStatusOrder()
      })
      
    },
    handleSearchOrder() {
      if(this.search) {

        this.$store.commit('SET_LOADING', true)
        this.searchOrder(this.search)
      }
    },
    resetOrder() {
      this.orderFiltered = []
      this.isFilter = false
      this.setFilter('ALL')
    },
    canConfirm(order) {
      if(order.shipping_courier_name != 'COD') {
        if(order.order_status == 'PENDING') return true

        return false
      } else {
        return false

      }
    },
    canInputResi(order) {
      if(order.shipping_courier_name == 'COD') {
        return false
      } else {
        if(order.order_status == 'TOSHIP') { 
          return true
        } else {
          return false
        }
      }

    },
    messageButtonLabel(status) {
      if(status == 'PENDING') return 'Follow Up'
      return 'Kirim Pesan'
    },
    handleDeleteOrder(id) {
      this.$q.dialog({
        title: 'Yakin akan menghapus data?',
        message: 'data yang dihapus tidak dapat dikembalikan.',
        ok: {label: 'Hapus', flat: true, 'no-caps': true, 'color' :'red-7'},
        cancel: {label: 'Batal', flat: true, 'no-caps': true },
        }).onOk(() => {
          this.destroyOrder(id).then(response => {
              if(response.status == 200) {
                this.filterOrder(this.filter)
              }
            })
        }).onCancel(() => {
        }).onDismiss(() => {
      })
    },
    handleConfirmationOrder(id) {
      this.$q.dialog({
        title: 'Konfirmasi pembayaran',
        message: 'Pastikan pembayaran telah anda terima dengan baik',
        cancel: true,
        }).onOk(() => {
          this.acceptPayment(id).then(() => {
            this.setFilter('TOSHIP')
            this.filterOrder(this.filter)
          })
        }).onCancel(() => {
        }).onDismiss(() => {
      })
    },
    handleFollowUp(data) {
      this.currentOrder = data
      this.followUpModal = true
    },
    handleInputResi(order) {
      this.form.resi = ''
      this.orderSelected = order
      this.form.order_id = order.id
      this.inputResiModal = true
    },
    closeModal() {
      this.inputResiModal = false
      this.orderSelected = ''
      this.form.order_id = ''
    },
    submitResi() {
      this.inputResi(this.form).then((res) => {
        if(res.status == 200) {
          let status = res.data.results.order_status
          this.setFilter(status)
        }
        this.filterOrder(this.filter)
      })
      this.closeModal()
    }
  },
  meta() {
    return {
      title: 'Order List',
    }
  }
  
}
</script>
