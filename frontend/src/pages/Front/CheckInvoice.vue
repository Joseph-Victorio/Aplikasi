<template>
    <q-page class="q-pb-lg bg-grey-1">
        <q-header class="text-grey-9 bg-white box-shadow">
            <q-toolbar>
                <q-btn :to="{ name: 'Home' }" flat round dense icon="eva-arrow-back" />
                <q-toolbar-title class="text-weight-bold brand">Cek Invoice</q-toolbar-title>
            </q-toolbar>
        </q-header>

        <div class="q-px-md q-pt-md">
            <div class="col bg-white border">
                <q-input ref="input" outlined dense v-model="search" autofocus @keyup.enter="searchInvoice"
                    placeholder="Masukkan Invoice / nomor hp">
                    <template v-slot:append>
                        <q-icon name="eva-search" class="cursor-pointer" @click="searchInvoice" />
                    </template>
                </q-input>
            </div>

            <template v-if="ready">
                <div class="q-pt-sm">Hasil pencarian "<b>{{ searchTitle }}</b>"</div>
            </template>
        </div>

        <template v-if="orders.length">
            <q-list bordered class="q-mt-md bg-white q-mx-md">
                <q-item v-for="o in orders" :key="o.id" clickable
                    @click="$router.push({ name: 'UserInvoice', params: { order_ref: o.order_ref } })">
                    <q-item-section>
                        <q-item-label><b>{{ o.order_ref }}</b></q-item-label>
                        <q-item-label caption>
                            {{ o.customer_name }} • {{ o.customer_phone }} • {{ o.created }}
                        </q-item-label>
                    </q-item-section>

                    <q-item-section side>
                        <q-chip dense :color="statusColor(o.order_status)" text-color="white">
                            {{ o.order_status }}
                        </q-chip>
                    </q-item-section>
                </q-item>
            </q-list>
        </template>

        <template v-if="ready && !orders.length">
            <div class="text-center column q-pt-lg">
                <div class="text-h6">Invoice tidak ditemukan</div>
                <div>Tidak ada invoice dengan kata kunci <b>"{{ searchTitle }}"</b></div>
            </div>
        </template>

        <q-inner-loading :showing="loading" />
    </q-page>
</template>

<script>
export default {
    name: 'CheckInvoice',

    data() {
        return {
            loading: false,
            search: '',
            searchTitle: '',
            orders: [],
            ready: false,
            available: true
        }
    },

    methods: {
        searchInvoice() {
            if (this.search.length < 3) return

            this.loading = true
            this.$refs.input.blur()

            this.$axios.post(`${process.env.API}/check-invoice`, {
                key: this.search
            }).then(res => {
                this.orders = res.data.results || []
                this.available = this.orders.length > 0
            }).catch(() => {
                this.orders = []
                this.available = false
            }).finally(() => {
                this.ready = true
                this.loading = false
                this.searchTitle = this.search
                this.search = ''
            })
        },

        statusColor(status) {
            switch (status) {
                case 'PAID': return 'green'
                case 'PENDING': return 'orange'
                case 'CANCELED': return 'red'
                case 'SHIPPING': return 'blue'
                default: return 'grey'
            }
        }
    }
}
</script>