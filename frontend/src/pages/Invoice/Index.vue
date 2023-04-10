<template>
  <q-page class="q-pb-lg">
    <q-header class="no-print box-shadow" :class="getHeaderColorBrand">
      <q-toolbar>
        <q-btn @click="goBack"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title class="text-weight-bold brand">
          <span v-if="invoice">Pesanan {{ invoice.order_ref }}</span>
          </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div v-if="invoice" class="no-print">
      <div class="q-gutter-y-md">
        <div class="q-pb-lg bg-white q-py-md q-px-sm">
          <q-list dense >
            <q-item>
              <q-item-section top>
                <q-item-label>Nomor Invoice</q-item-label>
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  {{ invoice.order_ref }}
                  <q-icon name="eva-copy" size="18px" class="q-ml-xs cursor-pointer" @click="copy(invoice.order_ref)"></q-icon>
                  </q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>Tanggal Pembelian</q-item-section>
              <q-item-section>{{ invoice.created }}</q-item-section>
            </q-item>
            <q-item>
              <q-item-section>Status Pesanan</q-item-section>
              <q-item-section>
                <q-item-label>
                  <span class="q-px-md rounded-borders text-white q-py-xs"
                   :class="`bg-${getOrderStatusColor(invoice.order_status)}`"
                  >{{ invoice.customer_status_label }}</span>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>Status Pembayaran</q-item-section>
              <q-item-section>
                <q-item-label>
                  <span class="q-px-md rounded-borders text-white q-py-xs"
                   :class="`bg-${getOrderStatusColor(invoice.transaction.status)}`"
                  >{{ invoice.transaction.status_label }}</span>
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </div>
        <q-card class="no-print bg-white shadow" square>
          <div class="card-heading">Produk Detil</div>
          <q-card-section>
            <q-separator color="teal q-pt-xs" />
            <q-list separator>
              <q-item class="bg-green-1">
                <q-item-section>Produk</q-item-section>
                <q-item-section>Qty</q-item-section>
                <q-item-section side>Harga</q-item-section>
              </q-item>
              <q-item v-for="(item, index) in invoice.items" :key="index">
                <q-item-section>
                  <div>{{ item.name }}</div>
                  <div class="text-caption tet-grey-6">{{ item.note }}</div>
                </q-item-section>
                <q-item-section>{{ item.quantity }}</q-item-section>
                <q-item-section side>{{ moneyIDR(item.price) }}</q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
        <q-card class="no-print bg-white shadow" square>
          <div class="card-heading border-b">Rincian Pembayaran</div>
          <q-card-section class="q-px-sm">
            <q-list dense>
              <q-item>
                <q-item-section>Referensi</q-item-section>
                <q-item-section>{{  invoice.transaction.payment_ref  }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Metode Pembayaran</q-item-section>
                <q-item-section>{{ invoice.transaction.payment_method.split('_').join(' ') }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Total Belanja</q-item-section>
                <q-item-section>{{  moneyIDR(invoice.order_subtotal) }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Total Ongkos Kirim ({{ invoice.order_weight }} gram)</q-item-section>
                <q-item-section>{{  moneyIDR(invoice.shipping_cost) }}</q-item-section>
              </q-item>
              <q-item v-if="invoice.order_unique_code">
                <q-item-section>Kode Unik</q-item-section>
                <q-item-section>{{  invoice.order_unique_code }}</q-item-section>
              </q-item>
              <q-item v-if="invoice.discount">
                <q-item-section>Diskon</q-item-section>
                <q-item-section>{{  moneyIDR(invoice.discount) }}</q-item-section>
              </q-item>
              <q-item v-if="invoice.payment_fee">
                <q-item-section>Payment Fee [ {{ invoice.transaction.payment_name }} ]</q-item-section>
                <q-item-section>{{ moneyIDR(invoice.payment_fee) }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label class="text-weight-bold q-py-sm text-md">Total Tagihan</q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-weight-bold q-py-sm text-md">{{  moneyIDR(invoice.grand_total) }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
        <q-card class="no-print bg-white shadow" square>
          <div class="card-heading">
              <div>Info Pengiriman</div>
          </div>
          <q-card-section class="q-px-sm">
            <q-list dense>
              <q-item>
                <q-item-section>Penerima</q-item-section>
                <q-item-section>{{ invoice.customer_name }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Ponsel</q-item-section>
                <q-item-section>{{ invoice.customer_whatsapp }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section top>Alamat</q-item-section>
                <q-item-section>
                  <div v-html="invoice.shipping_address"></div>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Kurir</q-item-section>
                <q-item-section>{{ invoice.shipping_courier_name }} {{ invoice.shipping_courier_service ? '' + ' - ' + invoice.shipping_courier_service : '' }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>No Resi <q-icon v-if="invoice.shipping_courier_code" name="eva-copy" size="18px" class="q-ml-sm cursor-pointer" @click="copy(invoice.shipping_courier_code)"></q-icon> </q-item-label>
                </q-item-section>
                <q-item-section>{{ invoice.shipping_courier_code ? invoice.shipping_courier_code  : '-'  }}</q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card> 
      </div>
      <q-dialog 
      v-model="modalPayment"
      persistent
      maximized
      seamless
      transition-show="slide-up"
      transition-hide="slide-down"
      >
        <q-card flat square class="max-width bg-grey-1">
          <q-toolbar class="bg-white sticky-top border-b">
            <q-toolbar-title><span class="text-weight-bold card-heading">Pembayaran</span></q-toolbar-title>
            <q-btn flat round dense icon="eva-close" @click="closeModalPayment" />
          </q-toolbar>
          <keep-alive>
          <component v-bind:is="isPaymentType" v-bind:transaction="invoice.transaction" @kirimBukti="kirimBuktiTransfer"></component>
          </keep-alive>
        </q-card>
      </q-dialog>
      <q-footer class="bg-white q-pa-md" v-if="invoice.order_status == 'PENDING'">
        <div class="text-center text-grey-7 column q-gutter-y-sm">
            <q-btn v-if="invoice && invoice.transaction.payment_method != 'COD'" label="Instruksi Pembayaran" no-caps icon="payments" @click.prevent="handlePaymentModal" color="green-7"></q-btn>
            <q-btn ref="chatAdmin" label="Chat admin" name="eva-message-circle" no-caps  @click="chatToAdmin" color="blue-7"></q-btn>
        </div>
      </q-footer>
    </div>
    <q-inner-loading :showing="loading" class="no-print">
       
    </q-inner-loading>
  </q-page>
</template>

<script>
import { copyToClipboard } from 'quasar'
import { mapActions, mapState } from 'vuex'
import QRCode from 'qrcode'
import PaymentGateway from './PaymentGateway.vue'
import DirectPayment from './DirectPayment.vue'
export default {
  name: 'InvoiceIndex',
  components: { PaymentGateway, DirectPayment },
  data () {
    return {
      modalPayment: false,
      throtle: 1,
      interval: null,
      timeout: null,
      requestCount: 1,
      whatsappUrl: 'https://api.whatsapp.com',
      qrData: '',
      autoShowModal: false
    }
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      shop: state => state.shop,
      invoice: state => state.order.invoice,
      config: state => state.config
    }),
    isPaymentType: function() {
      let paymentType = this.invoice.transaction.payment_type 
      if(paymentType == 'DIRECT' || paymentType == 'BANK_TRANSFER') {
        return 'DirectPayment'
      }
      return 'PaymentGateway'
    },
    title() {
      if(this.invoice) {
        return `Tagihan ${this.invoice.order_ref}`
      }
      return 'Tagihan'
    }
  },
  created() {
    if(!this.invoice){
      this.getData()
    } else {
      if(this.invoice.order_ref != this.$route.params.order_ref) {
        this.getData()
      } else {
        this.ready = true
        this.checkOrderStatus()
      }
    }
  },
  mounted() {
    if(this.$q.platform.is.desktop) {
      this.whatsappUrl = 'https://web.whatsapp.com'
    }
    if(this.$route.query.pay) {
      this.autoShowModal = true
    }
  },
  methods: {
    ...mapActions('order', ['getInvoice']),
    generateQr() {
      let opts = {
          errorCorrectionLevel: 'H',
          type: 'image/jpeg',
          quality: 0.3,
          margin: 1,
        }
        QRCode.toDataURL(this.getRoutePath(), opts, (err, url) => {
        if (err) throw err

          this.qrData = url
        })
    },
    getRoutePath() {
      let props = this.$router.resolve({ 
        name: 'UserInvoice',
        params: { order_ref: this.invoice.order_ref },
      });

      return location.origin + props.href;
    },
    getData() {
      this.$store.commit('SET_LOADING', true)
      if(this.$route.params.order_ref) {
        this.getInvoice(this.$route.params.order_ref).then(response => {
          if(response.status == 200) {
            this.$store.commit('order/SET_INVOICE', response.data.results)
          }
          if(this.autoShowModal && this.invoice.order_status == 'PENDING' && this.invoice.transaction.payment_method != 'COD') {
            this.modalPayment = true
          }
          this.$store.commit('SET_LOADING', false)
          this.checkOrderStatus()

          setTimeout(() => {
            this.generateQr()
          }, 1000)
        }).catch(() => {
          this.$router.push({name: 'Cart'})
        })
      } else {
        this.$router.push({name: 'Cart'})
      }
    },
    goBack() {
      if(this.$route.query._rdr) {
        this.$router.push(this.$route.query._rdr)
      }else {

        if(this.$store.state.user.loggedUser) {
          this.$router.push({ name: 'CustomerAccount'})
        }else {
          this.$router.push('/')
        }
      }
    },
    chatToAdmin() {
      if(this.shop.phone) {
        let text = `Halo ${this.shop.name},\nMohon pesanan saya untuk segera di proses.\nTerima Kasih.\n\nReferensi Order:\n${location.href}`
        let url = `${this.whatsappUrl}/send?phone=${this.formatPhoneNumber(this.shop.phone)}&text=${encodeURI(text)}`

        window.open(url, '_blank')
      }
    },
    kirimBuktiTransfer() {
      this.modalPayment = false

      if(this.shop.phone) {

        let text = `Halo kak, saya akan mengkonfirmasi pembayaran atas pesanan\nInvoice ID: *${this.invoice.order_ref}*\nReferensi Order:\n${location.href}\n`
        let url = `${this.whatsappUrl}/send?phone=${this.formatPhoneNumber(this.shop.phone)}&text=${encodeURI(text)}`
  
        window.open(url, '_blank')
      }
    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    copy(str) {
      copyToClipboard(str)
      .then(() => {
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil menyalin data'
        })
      })
      .catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Browser anda tidak support copy to clipboard'
        })
      })
    },
    handlePaymentModal() {
      this.modalPayment = true
    },
    closeModalPayment() {
      this.modalPayment = false
      this.autoShowModal = false
    },
    getCheckOrder() {
       this.getInvoice(this.$route.params.order_ref).then(response => {
          if(response.status == 200) {
            this.$store.commit('order/SET_INVOICE', response.data.results)
            this.checkOrderStatus()
          }
        })
    },
    checkOrderStatus() {

      if(this.requestCount < 10) {
        if(this.invoice.order_status == 'PENDING') {
  
          this.requestCount++
          this.timeout = setTimeout(() => {
            this.getCheckOrder()
          }, 25000)

        } else {
          clearTimeout(this.timeout)
        }

      }else {

        clearTimeout(this.timeout)
      }

    }
  },
  beforeDestroy() {
    clearTimeout(this.timeout)
  },
  meta() {
    return {
      title: this.title,
      meta: {
        ogTitle:  { property: 'og:title', content: this.title },
      }
      
    }
  }

}
</script>

<style lang="scss">
.table-order-item {
  width: 100%;
  border-spacing: inherit;
  tr {
    th, td {
      padding:.5rem;
    }
    th {
      background-color: #666;  
      color: #ccc;
    }
    td {
      border-bottom: 1px solid #eee;
    }
  }
}
.print-packing,
.print-invoice {
  display:none;
}

@media print {
  .no-print {
    display: none;
  }
  .print-packing,
    .print-invoice {
      display:block;
    }
  .border {
     border: 1px solid #eee;
  }
  .table-order-item {
    width: 100%;
    border-spacing: inherit;
    tr {
      th, td {
        padding: none;
        border: 1px solid #eee;
      }
    }
  }
}
</style>