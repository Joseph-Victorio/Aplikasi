<template>
   <q-page class="q-pb-xl">
      <q-header :class="getHeaderColorBrand">
         <q-toolbar>
            <q-btn :to="{ name: 'Settings' }" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title>
               List Pesanan
            </q-toolbar-title>
         </q-toolbar>
         <div class="box-shadow bg-white text-dark">
            <q-tabs v-model="filter" active-color="primary" outside-arrows>
               <q-tab name="ALL" label="Semua" no-caps></q-tab>
               <q-tab v-for="option in statusOptions" :key="option.value" :name="option.value" :label="option.label"
                  no-caps></q-tab>
            </q-tabs>
         </div>
      </q-header>
      <div class="q-py-md auto-padding-side">
         <div class="q-gutter-x-sm q-mt-sm">
            <q-input v-model="search" placeholder="No invoice, nama, email atau ponsel customer" dense borderless
               clearable class="bg-grey-3 q-px-sm" @keypress.enter="handleSearchOrder" @clear="clearSearch">
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
                     {{ index + 1 }}
                  </q-item-section>
                  <q-item-section top>
                     <div>
                        <table class="dense">
                           <tr>
                              <td>Invoice</td>
                              <td>{{ order.order_ref }}</td>
                           </tr>
                           <tr>
                              <td>Pelanggan</td>
                              <td>{{ order.customer_name }}</td>
                           </tr>
                           <tr>
                              <td>Telp</td>
                              <td>{{ order.customer_phone }}</td>
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
                                 <q-badge :color="getOrderStatusColor(order.order_status)">{{ order.status_label
                                    }}</q-badge>
                              </td>
                           </tr>

                           <tr v-if="order.shipping_courier_name">
                              <td>Pengiriman</td>
                              <td>{{ order.shipping_courier_name == 'COD' ? 'Diantar Kurir Toko' :
                                 order.shipping_courier_name }}
                              </td>
                           </tr>

                           <tr v-if="order.shipping_courier_code">
                              <td>No Resi</td>
                              <td>{{ order.shipping_courier_code ? order.shipping_courier_code : '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </q-item-section>
                  <q-item-section side top>
                     <div class="q-gutter-xs column order-btn-group">
                        <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px" label="Detail"
                           color="purple"
                           :to="{ name: 'AdminOrderShow', params: { order_ref: order.order_ref } }"></q-btn>

                        <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px"
                           @click="handleFollowUp(order)" label="Kirim Pesan" color="teal"></q-btn>


                        <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px"
                           label="Update Status" color="blue" @click="handleUpdateStatus(order)"></q-btn>

                        <q-btn class="btn-order-item" unelevated no-caps padding="6px 12px" size="12px"
                           label="Menu Lain" outline color="grey-8">
                           <q-menu auto-close>
                              <q-list separator>
                                 <q-item clickable @click="handleCopyLink(order.order_ref)">
                                    <q-item-section>
                                       <span class="nowrap">Salin Invoice Link</span>
                                    </q-item-section>
                                 </q-item>
                                 <q-item clickable @click="handleInputResi(order)">
                                    <q-item-section>Input Resi</q-item-section>
                                 </q-item>
                                 <q-item clickable @click="handleDeleteOrder(order.id)">
                                    <q-item-section>Hapus Order</q-item-section>
                                 </q-item>
                              </q-list>
                           </q-menu>
                        </q-btn>

                     </div>
                  </q-item-section>
               </q-item>
            </q-list>
         </div>
         <div class="flex justify-center q-py-xl">
            <q-btn outline :loading="orders.isLoadMore" v-if="orders.count > orders.data.length" label="loadmore..."
               @click="loadMore" unelevated color="primary"></q-btn>
         </div>
      </template>
      <div v-if="orders.ready && orders.count <= 0" class="text-center q-pt-xl">Tidak ada data</div>
      <q-dialog v-model="followUpModal" persistent>
         <follow-up @close="followUpModal = false" :order="currentOrder" />
      </q-dialog>

      <q-dialog v-model="inputResiModal">
         <q-card square style="width:100%;max-width:420px;">
            <div class="q-px-md q-py-sm bg-dark text-white text-weight-bold">Input Resi <span v-if="orderSelected"># {{
               orderSelected.order_ref }}</span></div>
            <form @submit.prevent="submitResi">
               <q-card-section>
                  <div class="text-grey-8">No Resi</div>
                  <q-input outlined v-model="form.resi" :rules="[val => val && val.length > 0 || 'Wajib diisi']" />
                  <q-checkbox label="Update Status ( Dikirim )" v-model="form.update_to_ship"></q-checkbox>

                  <div class="flex justify-end q-mt-sm q-gutter-x-sm">
                     <q-btn outline label="Batal" @click.prevent="closeModal" color="primary"></q-btn>
                     <q-btn unelevated type="submit" label="Simpan" color="primary"></q-btn>
                  </div>
               </q-card-section>
            </form>
         </q-card>
      </q-dialog>

      <q-dialog v-model="updateStatusModal" persistent no-shake>
         <q-card square style="width:100%;max-width:420px;">
            <div class="q-px-md q-py-sm bg-dark text-white text-weight-bold">Form Update Status</div>
            <form @submit.prevent="submitUpdateStatus">
               <q-card-section v-if="orderSelected">
                  <div class="text-grey-8">Status</div>
                  <q-select outlined v-model="formUpdateStatus.status"
                     :rules="[val => val && val.length > 0 || 'Wajib diisi']" :options="statusOptions" emit-value
                     map-options />
                  <div class="q-pb-sm">
                     <q-checkbox
                        v-if="orderSelected.order_status == 'CANCELED' || formUpdateStatus.status == 'CANCELED'"
                        v-model="formUpdateStatus.update_stock" :val="true">
                        <div>Update Stok?</div>
                        <div class="text-xs text-grey-7">
                           Jika diaktifkan, opsi ini akan mempengaruhi stok
                        </div>
                     </q-checkbox>


                  </div>
                  <div class="flex justify-end q-mt-sm q-gutter-x-sm">
                     <q-btn outline label="Batal" @click.prevent="closeModal" color="primary"></q-btn>
                     <q-btn :disable="orderSelected.order_status == formUpdateStatus.status" unelevated type="submit"
                        label="Update Status" color="primary"></q-btn>
                  </div>
               </q-card-section>
            </form>
         </q-card>
      </q-dialog>
      <q-inner-loading :showing="stateLoading">

      </q-inner-loading>
   </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import { copyToClipboard } from 'quasar'
import FollowUp from './FollowUp.vue'
export default {
   name: 'OrderIndex',
   components: { FollowUp },
   data() {
      return {
         isFilter: true,
         updateStatusModal: false,
         statusOptions: [
            { value: 'PENDING', label: 'Pending' },
            { value: 'TOSHIP', label: 'Perlu Dikirim' },
            { value: 'AWAITING_PICKUP', label: 'Belum Diambil' },
            { value: 'SHIPPING', label: 'Dikirim' },
            { value: 'COMPLETE', label: 'Selesai' },
            { value: 'CANCELED', label: 'Batal' }
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
         formUpdateStatus: {
            order_id: '',
            status: '',
            update_stock: false
         }
      }
   },
   watch: {
      'filter': function (newVal, oldVal) {
         if (newVal != oldVal) {
            this.form.status = newVal
            localStorage.setItem('order_filter', newVal)
            this.filterOrder(newVal)
         }
      },
   },
   computed: {
      orders() {
         return this.$store.state.order.orders
      },
      isMobile() {
         return window.innerWidth <= 800
      }
   },
   created() {
      if (this.orders.data.length <= this.orders.limit) {

         if (this.$route.query.filter) {
            this.setFilter(this.$route.query.filter)
            this.filterOrder(this.filter)

         } else if (localStorage.getItem('order_filter')) {

            this.setFilter(localStorage.getItem('order_filter'))
            this.filterOrder(this.filter)

         } else {
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
         this.$router.push({ name: 'OrderIndex', query: { filter: evt } })
         this.search = ''
         this.filterOrder(evt)
      },
      submitUpdateStatus() {
         this.$store.commit('SET_LOADING', true)
         this.updateStatusOrder(this.formUpdateStatus).then(() => {
            this.updateStatusModal = false
            this.filterOrder(this.filter)

         }).finally(() => this.$store.commit('SET_LOADING', false))
      },
      handleSearchOrder() {
         if (this.search) {

            this.$store.commit('SET_LOADING', true)
            this.searchOrder(this.search)
         }
      },
      handleCopyLink(order_ref) {
         let str = this.getRoutePath(order_ref)

         copyToClipboard(str)
            .then(() => {
               this.$q.notify({
                  type: 'positive',
                  message: 'Berhasil menyalin'
               })
            })
            .catch(() => {
               this.$q.notify({
                  type: 'negative',
                  message: 'Browser anda tidak support copy to clipboard'
               })
            })
      },
      getRoutePath(order_ref) {
         let props = this.$router.resolve({
            name: 'UserInvoice',
            params: { order_ref: order_ref },
         });

         return location.origin + props.href;
      },
      resetOrder() {
         this.orderFiltered = []
         this.isFilter = false
         this.setFilter('ALL')
      },
      handleDeleteOrder(id) {
         this.$q.dialog({
            title: 'Yakin akan menghapus data?',
            message: 'data yang dihapus tidak dapat dikembalikan.',
            options: {
               type: 'checkbox',
               model: [],
               items: [
                  { label: 'Mengembalikan Stok?', value: true },
               ]
            },
            ok: { label: 'Hapus', flat: true, 'no-caps': true, 'color': 'red-7' },
            cancel: { label: 'Batal', flat: true, 'no-caps': true },
         }).onOk((data) => {
            this.destroyOrder({
               order_id: id,
               update_stock: data.length ? true : false
            }).then(response => {
               if (response.status == 200) {
                  this.filterOrder(this.filter)
               }
            })
         }).onCancel(() => {
         }).onDismiss(() => {
         })
      },
      handleFollowUp(data) {
         this.currentOrder = data
         this.followUpModal = true
      },
      handleUpdateStatus(order) {
         this.orderSelected = order
         this.formUpdateStatus.order_id = order.id
         this.formUpdateStatus.status = order.order_status
         this.formUpdateStatus.update_stock = false
         this.updateStatusModal = true
      },
      handleInputResi(order) {
         this.form.resi = ''
         this.orderSelected = order
         this.form.order_id = order.id
         this.inputResiModal = true
      },
      closeModal() {
         this.orderSelected = ''
         this.form.order_id = ''
         this.formUpdateStatus.order_id = ''
         this.formUpdateStatus.status = ''
         this.inputResiModal = false
         this.updateStatusModal = false
      },
      submitResi() {
         this.inputResi(this.form).then((res) => {
            if (res.status == 200) {
               let status = res.data.results.order_status
               this.setFilter(status)
               this.filterOrder(this.filter)
            }
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
