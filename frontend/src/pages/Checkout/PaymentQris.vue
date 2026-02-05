<template>
    <q-layout view="hHh lpR fFf">
        <q-page-container>
            <q-page class="q-pa-md bg-grey-1">
                <div class="column items-center">

                    <div class="text-center q-mb-md">
                        <div class="text-h6 text-weight-bold">Pembayaran QRIS</div>
                        <div class="text-caption text-grey-7">
                            Silakan selesaikan pembayaran melalui QRIS
                        </div>
                    </div>

                    <q-card flat bordered class="q-mb-md" style="max-width:360px;width:100%">
                        <q-card-section class="text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MAKASIH UDAH BAYAR"
                                alt="QR Code Pembayaran" style="width:200px;height:200px;border-radius:8px" />
                            <div class="q-mt-sm text-weight-medium">
                                Total: {{ moneyIDR(total) }}
                            </div>
                        </q-card-section>
                    </q-card>

                    <!-- Upload Bukti -->
                    <q-card flat bordered style="max-width:360px;width:100%">
                        <q-card-section>
                            <div class="text-weight-medium q-mb-xs">
                                Upload Bukti Transfer <span class="text-negative">*</span>
                            </div>

                            <q-file v-model="proofFile" filled accept=".jpg,.jpeg,.png" label="Pilih gambar"
                                max-file-size="2048000" @rejected="onRejected">
                                <template v-slot:prepend>
                                    <q-icon name="eva-image-outline" />
                                </template>
                            </q-file>
                        </q-card-section>
                    </q-card>

                    <q-btn color="green" size="lg" class="full-width q-mt-lg" style="max-width:360px" :loading="loading"
                        :disable="!proofFile" label="Saya Sudah Bayar" @click="submitPayment" />

                    <div class="text-caption text-grey q-mt-sm text-center">
                        Admin akan memverifikasi pembayaran Anda
                    </div>

                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<script>
export default {
    data() {
        return {
            proofFile: null,
            loading: false,
            total: 0
        }
    },
    mounted() {
        const orderRef = this.$route.params.order_ref

        if (!orderRef) {
            this.$router.replace('/')
            return
        }
        this.$axios.get(`${process.env.API}/order/${orderRef}`)
            .then(res => {
                this.total = res.data.results.total
            })
            .catch(() => {
                this.$q.notify({ type: 'negative', message: 'Gagal ambil total' })
            })
    },
    methods: {
        async submitPayment() {
            if (!this.proofFile) return
            this.loading = true

            const form = new FormData()
            form.append('proof', this.proofFile)
            form.append('order_ref', this.$route.params.order_ref)

            try {
                await this.$axios.post(`${process.env.API}/payment/upload-proof`, form)
                this.$q.notify({ type: 'positive', message: 'Bukti berhasil dikirim, menunggu verifikasi' })

                this.$router.replace({
                    name: 'UserInvoice',
                    params: { order_ref: this.$route.params.order_ref }
                })
            } catch (e) {
                this.$q.notify({ type: 'negative', message: 'Upload gagal' })
            } finally {
                this.loading = false
            }
        },
        onRejected() {
            this.$q.notify({ type: 'negative', message: 'File harus JPG / PNG max 2MB' })
        },
        moneyIDR(val) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val)
        }
    }
}
</script>
