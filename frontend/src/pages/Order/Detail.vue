<template>
  <q-page class="q-pb-lg bg-grey-1">
    <q-header class="no-print box-shadow bg-brand">
      <q-toolbar>
        <q-btn v-go-back.single flat round dense icon="eva-arrow-back" />
        <q-toolbar-title class="text-weight-bold brand">
          <span v-if="invoice">Invoice {{ invoice.order_ref }}</span>
        </q-toolbar-title>
        <q-btn icon="print" dense @click="printInvoice" flat></q-btn>
      </q-toolbar>
    </q-header>
    <div v-if="invoice" class="no-print">
      <div class="q-gutter-y-md">
        <div class="q-mt-md q-pb-lg bg-white q-py-md q-px-sm">
          <q-list dense>
            <q-item>
              <q-item-section>
                <q-item-label class="text-md">Invoice</q-item-label>
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  {{ invoice.order_ref }}
                  <q-icon name="eva-copy" size="18px" class="q-ml-xs cursor-pointer"
                    @click="copy(invoice.order_ref)"></q-icon>
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
                    :class="`bg-${getOrderStatusColor(invoice.order_status)}`">{{ invoice.status_label }}</span>
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
                <q-item-section>Harga</q-item-section>
              </q-item>
              <q-item v-for="(item, index) in invoice.items" :key="index">
                <q-item-section>
                  <div class="text-md">{{ item.name }}</div>
                  <div class="text-caption tet-grey-6">{{ item.note }}</div>
                </q-item-section>
                <q-item-section>{{ item.quantity }}</q-item-section>
                <q-item-section>{{ moneyIDR(item.price) }}</q-item-section>
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
                <q-item-section>{{ invoice.customer_phone }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Alamat</q-item-section>
                <q-item-section>
                  <q-item-label>
                    <div v-html="invoice.shipping_address"></div>
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Kurir</q-item-section>
                <q-item-section>{{ invoice.shipping_courier_name }} </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>No Resi <q-icon v-if="invoice.shipping_courier_code" name="eva-copy" size="18px"
                      class="q-ml-sm cursor-pointer" @click="copy(invoice.shipping_courier_code)"></q-icon>
                  </q-item-label>
                </q-item-section>
                <q-item-section>{{ invoice.shipping_courier_code ? invoice.shipping_courier_code : '-' }}</q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
        <q-card class="no-print bg-white shadow" square>
          <div class="card-heading border-b">Rincian Pembayaran</div>
          <q-card-section class="q-px-sm">
            <q-list dense>
              <q-item>
                <q-item-section>Total Belanja</q-item-section>
                <q-item-section>{{ moneyIDR(invoice.order_subtotal) }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>Total Ongkos Kirim ({{ invoice.order_weight }} gram)</q-item-section>
                <q-item-section>{{ moneyIDR(invoice.shipping_cost) }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label class="text-weight-bold q-py-sm text-md">Total Tagihan</q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-weight-bold q-py-sm text-md">{{ moneyIDR(invoice.grand_total)
                  }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>

      </div>

    </div>
    <q-inner-loading :showing="loading" class="no-print">

    </q-inner-loading>
    <div v-if="invoice" :class="{ 'no-print': isPrintPacking }">
      <div class="print-invoice">
        <div class="">
          <div class="bg-white">
            <div class="text-center">
              <div class="text-weight-bold q-mb-xs" style="font-size:1.8rem">{{ shop.name }}</div>
              <div style="font-size:1.1rem">{{ shop.phone }}</div>
              <div style="font-size:1.1rem">{{ shop.app_url }}</div>
            </div>
            <hr />
            <div class="q-mb-md flex justify-between item-start" style="font-size:1.1rem">
              <table>
                <tr>
                  <td align="left">Invocie No</td>
                  <td>:</td>
                  <td align="left">{{ invoice.order_ref }}</td>
                </tr>
                <tr>
                  <td align="left">Tanggal</td>
                  <td>:</td>
                  <td>{{ invoice.created }}</td>
                </tr>
              </table>
              <div v-if="qrData" class="">
                <img :src="qrData" width="80" height="80" />
              </div>
            </div>
            <hr />
            <div class="text-weight-bold q-my-xs">Pelanggan:</div>
            <hr />
            <div class="flex justify-between items-start" style="font-size:1.1rem">
              <div class="">
                <div class="text-weight-bold">{{ invoice.customer_name }}</div>
                <div>{{ invoice.customer_phone }}</div>
                <div v-html="invoice.shipping_address" style="font-size:1.2rem"></div>
              </div>
            </div>
            <hr />
            <div class="relative q-pt-xs" style="font-size:1.1rem">
              <hr />
              <div class="text-weight-bold q-my-xs">Detil Pesanan:</div>
              <hr />
              <table class="table-order-item" v-if="invoice.items">
                <tr>
                  <th align="left">Item</th>
                  <th align="left">qty</th>
                  <th align="right">Harga</th>
                </tr>
                <tr v-for="(item, index) in invoice.items" :key="index">
                  <td class="">
                    <div>{{ item.name }}</div>
                    <div class="text-caption tet-grey-6">{{ item.note }}</div>
                  </td>
                  <td>{{ item.quantity }}</td>
                  <td align="right">{{ $money(item.price) }}</td>
                </tr>
              </table>
              <div class="column justify-end items-end q-mt-sm">
                <table class="dense">
                  <tr>
                    <td align="right">Jumlah</td>
                    <td>:</td>
                    <td>Rp</td>
                    <td align="right">{{ $money(invoice.order_subtotal) }}</td>
                  </tr>
                  <tr>
                    <td align="right">Ongkos Kirim</td>
                    <td>:</td>
                    <td>Rp</td>
                    <td align="right">{{ $money(invoice.shipping_cost) }}</td>
                  </tr>

                  <tr>
                    <th align="right">Total Bayar</th>
                    <th align="right">:</th>
                    <th>Rp</th>
                    <th align="right">{{ $money(invoice.grand_total) }}</th>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="invoice" :class="{ 'no-print': isPrintInvoice }">
      <div class="print-packing">
        <div class="bg-white">
          <div class="text-center">
            <div class="text-weight-bold q-mb-xs" style="font-size:1rem">{{ shop.name }}</div>
            <div class="">{{ shop.phone }}</div>
            <div class="text-xs" v-html="shop.address"></div>
          </div>
          <div class="flex justify-between q-mb-sm">
            <div class="">
              <div class="text-weight-medium q-mb-xs text-weight-bold">Tujuan:</div>
              <div>{{ invoice.customer_name }}</div>
              <div>{{ invoice.customer_phone }}</div>
              <div v-html="invoice.shipping_address"></div>
            </div>
          </div>
          <div>
            <div class="q-mb-xs text-weight-bold">Detil Pesanan</div>
            <table class="table-order-item" v-if="invoice.items">
              <tr>
                <th align="left">Item</th>
                <th align="left">qty</th>
              </tr>
              <tr v-for="(item, index) in invoice.items" :key="index">
                <td class="">
                  <div>{{ item.name }}</div>
                  <div class="text-caption tet-grey-6">{{ item.note }}</div>
                </td>
                <td>{{ item.quantity }}</td>
              </tr>
            </table>

          </div>

        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { copyToClipboard } from 'quasar'
import { mapActions, mapState } from 'vuex'
import QRCode from 'qrcode'
export default {
  name: 'InvoiceIndex',
  data() {
    return {
      throtle: 1,
      isPrintPacking: false,
      isPrintInvoice: false,
      timeout: null,
      qrData: ''
    }
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      shop: state => state.shop,
      invoice: state => state.order.invoice,
      config: state => state.config,
    })
  },
  created() {
    this.getOrder()
  },

  methods: {
    ...mapActions('order', ['getOrderByRef']),
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
    getOrder() {
      this.$store.commit('SET_LOADING', true)
      if (this.$route.params.order_ref) {
        this.getOrderByRef(this.$route.params.order_ref).then(response => {
          if (response.status == 200) {
            this.$store.commit('order/SET_INVOICE', response.data.results)
          }
          this.$store.commit('SET_LOADING', false)
          this.checkOrderStatus()
        }).catch(() => {
          // this.$router.push({ name: 'Cart' })
        })
      } else {
        // this.$router.push({ name: 'Cart' })
      }
    },
    money(number) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number)
    },
    copy(str) {
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
    copyAddress() {
      let addr = `${this.invoice.customer_name}\n${this.invoice.customer_phone}\n${this.invoice.shipping_address}`
      this.copy(addr)
    },
    printInvoice() {
      if (!this.qrData) {
        this.generateQr()
      }
      const today = new Date().toDateString()
      document.title = `INVOICE #${this.invoice.order_ref} ${today}`
      this.isPrintPacking = false
      this.isPrintInvoice = true
      setTimeout(() => {
        window.print()
      }, 200)
    },
    printPacking() {
      const today = new Date().toDateString()
      document.title = `PACKING #${this.invoice.order_ref} ${today}`
      this.isPrintPacking = true
      this.isPrintInvoice = false
      setTimeout(() => {
        window.print()
      }, 200)
    },
  },
  beforeDestroy() {
    clearTimeout(this.timeout)
  }

}
</script>

<style lang="scss">
.table-order-item {
  width: 100%;
  border-spacing: inherit;

  tr {

    th,
    td {
      padding: .5rem;
    }

    th {
      background-color: rgb(151, 250, 196);
    }

    td {
      border-bottom: 1px solid #eee;
    }
  }
}

.print-packing,
.print-invoice {
  display: none;
  width: 100%;
  max-width: 600px;
  border: 1px solid;
  padding: 10px;
  margin-left: 0 !important;
  font-size: 1rem;
}

@media print {
  .no-print {
    display: none;
  }

  .print-packing,
  .print-invoice {
    display: block;
    max-width: 600px;
    font-size: 16pt;
  }

  .border {
    border: 1px solid #eee;
  }

  .table-order-item {
    width: 100%;
    border-spacing: inherit;

    tr {

      th,
      td {
        padding: none;
        border: 1px solid #eee;
      }
    }
  }
}
</style>