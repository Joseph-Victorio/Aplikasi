<template>
  <q-card style="width:100%;max-width:400px;">
    <form @submit.prevent="submitMessage">
      <div class="q-px-md q-pt-md">
        <div class="text-h6">{{ messageButtonLabel }}</div>
        <div class="text-xs text-grey-7">Anda bisa mengedit dengan format pesan anda sendiri.</div>
      </div>
      <q-card-section style="max-height: 80vh" class="scroll">
        <q-input outlined type="textarea" v-model="message" rows="18" class="textarea-input"/>
        <div class="q-mt-md flex justify-end q-gutter-x-sm">
            <q-btn outline color="primary" type="button" @click.prevent="$emit('close')" label="Batal" no-caps unelevated />
            <q-btn unelevated type="submit" label="Kirim Whatsapp" color="primary" no-caps/>
        </div>
      </q-card-section>
    </form>
  </q-card>
</template>

<script>
export default {
  props: ['order'],
  data() {
    return {
      message: ''
    }
  },
  computed: {
    shop() {
     return this.$store.state.shop
    },
    banks() {
      return this.$store.state.bank.banks
    },
    messageButtonLabel() {
      if(this.order.order_status == 'UNPAID' || this.order.order_status == 'OVERDUE') return 'Follow Up Order'
      return 'Send Message'
    }
  },
  methods: {
    submitMessage() {
     let url = `https://api.whatsapp.com/send?phone=${this.formatPhoneNumber(this.order.customer_whatsapp)}&text=${encodeURI(this.message)}`
     window.open(url, '_blank')
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
     formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    getRoutePath() {
      let props = this.$router.resolve({ 
        name: 'UserInvoice',
        params: { order_ref: this.order.order_ref },
      });

      return location.origin + props.href;
    },
    setPaymentMessage() {
      let str ='Silahkan lakukan pembayaran ke nomor rekening berikut:\n\n'
      if(this.banks.data.length) {
        this.banks.data.forEach(el => {
          str += `*${el.bank_name} - ${el.bank_office}*\nNorek: ${el.account_number}\nA.n ${el.account_name}\n\n`
        })
        return str
      } else {
        return ''
      }
    },
    setTextMessage() {
       if(this.order) {
         if(this.order.order_status == 'UNPAID') {
           let tmp = `Halo kak ${this.order.customer_name}\n`

           tmp += `kami dari *${this.shop.name? this.shop.name: '...'}*\n`
           tmp += `berikut adalah pesanan kakak pada toko kami:\n\n`
           tmp += `Invoice : *${this.order.order_ref}*\n`
           tmp += `Di pesan pada : ${this.order.created_at}\n`
           tmp += `Subtotal : ${this.moneyIDR(this.order.order_subtotal)}\n`
           tmp += `Ongkos Kirim : ${this.moneyIDR(this.order.shipping_cost)}\n`

           if(this.order.order_unique_code) {
             tmp += `Kode Unik : - ${this.order.order_unique_code}\n`
           }
           if(this.order.service_fee) {
             tmp += `Service Fee : ${this.order.service_fee}\n`
           }
           if(this.order.payment_fee) {
             tmp += `Payment Fee: ${this.moneyIDR(this.order.payment_fee)}\n`
           }

           tmp += `\n*Total : ${this.moneyIDR(this.order.grand_total)}*\n\n`
          //  tmp += `${this.setPaymentMessage()}\n`
           tmp += `Untuk detail tagihan dapat dilihat di ${this.getRoutePath()}\n\n`
           tmp += `Terima Kasih`

           this.message = tmp

         } else {
            let tmp = `Halo kak ${this.order.customer_name}\nkami dari ${this.shop.name? this.shop.name: '...'}\n\n......\n\nTerima Kasih
           `
           this.message = tmp
         }
      }
    }
  },
  mounted() {
   this.setTextMessage()
  }
}
</script>