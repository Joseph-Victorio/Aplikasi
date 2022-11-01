<template>
  <q-page padding style="max-width:800px;margin:auto;">
    <q-btn label="Kembali" class="q-mb-sm" unelevated padding="3px 12px" no-caps @click="goBack" color="dark" ></q-btn>
    <div class="full-width">
        
      <q-card square class="box-shadow">
        <q-card-section class="q-pa-xl">
          <div class="">
            <div class="row justify-between items-start no-wrap">
              <div class="col-6">
                <div class="text-weight-bold" style="font-size:36px;">Tagihan</div>
                <q-item-label style="font-size:20px;">{{ order.invoice_number }}
                  <q-icon name="eva-copy" class="cursor-pointer" @click="copy(order.invoice_number)"></q-icon>
                </q-item-label>
                <q-item-label style="font-size:20px;">{{ order.payment_name }}</q-item-label>
              </div>
              <div class="col-6 text-right" style="max-width:230px;padding-top:4px;">
                <div class="text-right q-mt-md text-md">
                  <q-item-label class="text-weight-bold q-pt-sm">Jumlah Tagihan</q-item-label>
                  <q-item-label class="">{{ moneyIdr(order.amount) }}
                    <q-icon name="eva-copy" class="cursor-pointer" @click="copy(order.amount)"></q-icon>
                  </q-item-label>
                </div>
              </div>
            </div>
          </div>
          <div class="text-md q-mt-xl q-mb-sm">Detil Pesanan</div>
          <q-separator color="secondary q-pt-xs" />
          <q-list>
            <q-item class="bg-green-1">
              <q-item-section>Produk</q-item-section>
              <q-item-section side class="text-dark">Harga</q-item-section>
            </q-item>
            <q-separator />
            <q-item>
              <q-item-section>{{ order.description }}</q-item-section>
              <q-item-section side top>{{ moneyIdr(order.amount) }}</q-item-section>
            </q-item>
            <q-separator />
          </q-list>  
        
        </q-card-section>
      </q-card>   
      <q-card flat square>
        <q-card-section class="text-center bg-secondary text-white">
          <div class="text-md text-center q-mb-md">Lakukan Pembayaran Sebelum</div>
          <div class="row items-center justify-center q-gutter-x-lg">
            <div class="text-center">
              <div v-if="countdown.day != '00'" class="text-xl text-weight-bold">{{ `${countdown.day}` }}</div>
              <div class="q-pb-xs" v-if="countdown.day != '00'">hari</div>
            </div>
            <div class="text-center">
              <div class="text-xl text-weight-bold">{{ `${countdown.hour}` }}</div>
              <div class="q-pb-xs">jam</div>
            </div>
            <div class="text-center">
              <div class="text-xl text-weight-bold">{{ `${countdown.minute}` }}</div>
              <div class="q-pb-xs">menit</div>
            </div>
            <div class="text-center">
              <div class="text-xl text-weight-bold">{{ `${countdown.second}` }}</div>
              <div class="q-pb-xs">detik</div>
            </div>
          </div>
        </q-card-section>
      </q-card>
      <q-card square class="box-shadow q-mt-md">
        <q-card-section class="q-pa-xl">
          <div class="text-center q-pt-md">
            <div class="text-lg text-weight-bold q-mb-md">{{ order.payment_name }}</div>
            <div class="q-pb-sm text-center" style="font-size:15px;">
              Segera lakukan pembayaran sebesar <b>{{ moneyIdr(order.amount) }}</b> sebelum <b>{{ order.created_date }}</b>, Pastikan nominal sesuai dengan Tagihan, 
              <template v-if="isBank" >
              sertakan code <span class="cursor-pointer q-px-xs text-weight-bold text-teal" @click="copy(order.invoice_number)">{{ order.invoice_number }}</span> di kolom berita transfer.<div class="q-mt-sm">
                <q-btn color="teal" outline padding="3px 12px" no-caps @click="chatModal = true">Konfirmasi pembayaran disini</q-btn>
                </div>
              </template>
              <span v-if="isPg">Sistem akan memproses pesanan anda secara otomatis.</span>
            </div>
            <div class="q-py-lg">
              <div class="text-lg text-weight-bold">{{ isBank ? 'No Rekening' : 'Kode Bayar' }}</div>
              <div class="row items-center q-gutter-x-sm justify-center">
                <div class="text-center relative">
                  <div class=" text-secondary text-xl text-weight-bold ">{{ order.payment_code }}</div>
                </div>
                <q-btn dense size="xl" color="secondary" flat icon="eva-copy" @click="copy(order.payment_code)"></q-btn>
              </div>  
              <div>{{ order.payment_detail }}</div>
            </div>
          </div>
          <div class="text-md text-weight-bold q-my-sm q-pt-lg">Panduan Pembayaran</div>
          <q-list class="text-grey-9">
            <q-expansion-item
            v-for="(item, index) in instructions"
              :key="index"
              group="somegroup"
              :label="item.title"
              :default-opened="index == 0"
              header-class="bg-grey-7 text-white"
              expand-icon-class="text-white"
            >
              <q-card class="q-py-md q-pb-xl">
                <q-list bordered>
                  <q-item v-for="(step, index) in item.steps" :key="index">
                    <q-item-section avatar>
                      <q-avatar color="grey-7" text-color="white" size="sm">
                        {{ index+1 }}
                      </q-avatar>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label v-html="step"></q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card>
            </q-expansion-item>
          </q-list>
        </q-card-section>
      </q-card>
    </div>
    <q-dialog v-model="chatModal" persistent>
      <q-card class="card-md">
        <div class="card-heading flex justify-between items-center">
          <div>Konfirmasi Pembayaran</div>
          <q-btn icon="eva-close" flat padding="xs" dense v-close-popup></q-btn>
        </div>
        <q-card-section>
          <form @submit.prevent="submitChat">
            <q-input readonly stack-label required label="No Invoice" v-model="formChat.invoice"></q-input>
            <money-formatter required label="Jumlah Transfer" v-model="formChat.amount"></money-formatter>
            <q-input stack-label required label="Nama Pengirim" v-model="formChat.sender"></q-input>
            <div class="q-mt-lg">
              <q-btn label="Kirim Konfirmasi" class="full-width" color="secondary" type="submit"></q-btn>
            </div>
          </form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  data () {
    return {
      chatModal: false,
      formChat: {
        invoice: '',
        amount: '',
        sender: ''
      }
    }
  },
  computed: {
    invoice() {
      return this.$store.state.order.invoice
    },
    isOVO() {
      return this.transaction.payment_method == 'OVO'
    },
    isBank() {
      return this.transaction.payment_type == 'BANK_TRANSFER'
    },
    isQRIS() {
      return this.transaction.payment_method == 'QRIS'
    },
    instructions: function() {
      
      if(this.isBank) {

        return [`Lakukan transfer ke nomor rekening <b>${ this.transaction.payment_code }.</b>`,
      `Transfer dengan total pembayaran  <b>${ this.priceFormated }</b><br> <small>( jangan dibulatkan )</small> `,
      "Simpan dan <b>kirim bukti pembayaran.</b>",
      `Kirim bukti transfer sebelum <b>${ this.expiredFormatDate } </b> agar pesananmu segera diproses.`]

      } else {

        let ins = JSON.parse(this.transaction.instructions)
        if(this.isOVO) {
          ins[0].steps = [
            'Periksa <b>aplikasi OVO</b> di Ponsel Anda', 
            'Akan muncul prompt transaksi. Pastikan data transaksi sudah sesuai', 
            'Klik tombol <b>Bayar</b>',
            'Transaksi selesai. Simpan bukti pembayaran Anda'
          ]
        } 
        return ins
      }

    }
  },
  created() {
    if(!this.invoice){
      this.getOrder()
    } else {
      if(this.invoice.order_ref != this.$route.params.order_ref) {
        this.getOrder()
      } else {
        this.ready = true
        this.checkOrderStatus()
      }
    }
  },
  methods: {
    submitChat() {

    },
    getOrder() {
      this.$store.commit('SET_LOADING', true)
      if(this.$route.params.order_ref) {
        this.$store.dispatch('order/getOrderById', this.$route.params.order_ref).then(response => {
          if(response.status == 200) {
            this.$store.commit('order/SET_INVOICE', response.data.results)
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
  }
}
</script>
