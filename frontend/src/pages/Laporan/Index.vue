<template>
    <q-page padding>
        <q-card class="q-mb-md">
            <q-card-section class="items-center justify-between flex">
                <q-toolbar>
                    <q-btn :to="{ name: 'Settings' }" flat round dense icon="eva-arrow-back" />
                    <q-toolbar-title>Dashboard Laporan Order</q-toolbar-title>
                </q-toolbar>

                <q-select v-model="filter" :options="filters" emit-value map-options dense outlined
                    style="width:150px" />

                <q-select v-if="dynamicFilters.length" v-model="selectedFilter" :options="dynamicFilters" emit-value
                    map-options dense outlined style="width:180px" class="q-ml-sm" />
            </q-card-section>

            <q-separator />

            <div class="row q-col-gutter-md q-pa-md text-center">
                <div class="col-12 col-md-4">
                    <q-card class="bg-primary text-white cursor-pointer" @click="showOrderDetails = true">
                        <q-card-section>
                            <div>Total Order</div>
                            <div class="text-h5">{{ summary.total_orders || 0 }}</div>
                        </q-card-section>
                    </q-card>
                </div>

                <div class="col-12 col-md-4">
                    <q-card class="bg-green text-white">
                        <q-card-section>
                            <div>Total Penjualan</div>
                            <div class="text-h5">
                                Rp {{ format(summary.total_income) }}
                            </div>
                        </q-card-section>
                    </q-card>
                </div>

                <div class="col-12 col-md-4">
                    <q-card class="bg-warning text-white cursor-pointer" @click="showItemDetails = true">
                        <q-card-section>
                            <div>Total Item</div>
                            <div class="text-h5">{{ summary.total_items || 0 }}</div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </q-card>

        <q-dialog v-model="showOrderDetails">
            <q-card style="min-width:350px;width:700px;max-width:80vw;height:80vh;">
                <q-card-section class="row items-center q-pb-none">
                    <div class="text-h6">Detail Order ({{ filter }})</div>
                    <q-space />
                    <q-btn icon="close" flat round dense v-close-popup />
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div v-if="loading" class="text-center q-pa-md">Loading...</div>
                    <div v-else-if="!orderList.length" class="text-center q-pa-md">Tidak ada data</div>
                    <div v-else class="table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Nama Customer</th>
                                    <th>Invoice / Ref</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orderList" :key="order.order_ref">
                                    <td>{{ order.customer_name }}</td>
                                    <td>
                                        <q-chip size="sm" color="blue-1" text-color="blue-9" square>{{ order.order_ref
                                        }}</q-chip>
                                    </td>
                                    <td class="text-right">Rp {{ format(order.total_price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>

        <q-dialog v-model="showItemDetails">
            <q-card style="min-width:350px;width:700px;max-width:80vw;height:80vh;">
                <q-card-section class="row items-center q-pb-none">
                    <div class="text-h6">Detail Item Terjual ({{ filter }})</div>
                    <q-space />
                    <q-btn icon="close" flat round dense v-close-popup />
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div v-if="loading" class="text-center q-pa-md">Loading...</div>
                    <div v-else-if="!itemList.length" class="text-center q-pa-md">Tidak ada data</div>
                    <div v-else class="table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Invoice / Ref</th>
                                    <th>Item</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in itemList" :key="item.order_ref + '-' + item.item_name">
                                    <td>
                                        <q-chip size="sm" color="blue-1" text-color="blue-9" square>{{ item.order_ref
                                        }}</q-chip>
                                    </td>
                                    <td>{{ item.item_name }}</td>
                                    <td class="text-right">{{ item.qty }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>

        <q-card class="q-mb-md">
            <q-card-section>
                <div class="text-subtitle1 q-mb-md">Grafik Pendapatan</div>
                <canvas ref="incomeChart"></canvas>
            </q-card-section>
        </q-card>

        <q-card>
            <q-card-section>
                <div class="text-subtitle1 q-mb-md">Grafik Jumlah Order</div>
                <canvas ref="orderChart"></canvas>
            </q-card-section>
        </q-card>

    </q-page>
</template>

<script>
import axios from 'axios'
import {
    Chart, BarController, BarElement, LineController, LineElement,
    PointElement, CategoryScale, LinearScale, Legend, Tooltip
} from 'chart.js'

Chart.register(
    BarController, BarElement, LineController, LineElement,
    PointElement, CategoryScale, LinearScale, Legend, Tooltip
)

export default {
    name: 'LaporanIndex',
    data() {
        return {
            filter: 'monthly',
            filters: [
                { label: 'Mingguan', value: 'weekly' },
                { label: 'Bulanan', value: 'monthly' },
                { label: 'Tahunan', value: 'yearly' }
            ],
            dynamicFilters: [],
            selectedFilter: null,

            showOrderDetails: false,
            showItemDetails: false,
            loading: false,
            orders: [],
            summary: {},
            orderList: [],
            itemList: [],
            chartIncome: null,
            chartOrder: null
        }
    },

    watch: {
        filter() {
            this.setDynamicFilters()
            this.selectedFilter = null
            this.getReports()
        },
        selectedFilter() {
            this.getReports()
        }
    },

    mounted() {
        this.setDynamicFilters()
        this.getReports()
    },

    methods: {
        format(val) {
            return Number(val || 0).toLocaleString('id-ID')
        },

        setDynamicFilters() {
            if (this.filter === 'weekly') {
                this.dynamicFilters = [
                    { label: 'Semua Hari', value: null },
                    { label: 'Senin', value: 1 },
                    { label: 'Selasa', value: 2 },
                    { label: 'Rabu', value: 3 },
                    { label: 'Kamis', value: 4 },
                    { label: 'Jumat', value: 5 },
                    { label: 'Sabtu', value: 6 },
                    { label: 'Minggu', value: 7 }
                ]
            } else if (this.filter === 'monthly') {
                this.dynamicFilters = [
                    { label: 'Semua Bulan', value: null },
                    { label: 'Januari', value: 1 },
                    { label: 'Februari', value: 2 },
                    { label: 'Maret', value: 3 },
                    { label: 'April', value: 4 },
                    { label: 'Mei', value: 5 },
                    { label: 'Juni', value: 6 },
                    { label: 'Juli', value: 7 },
                    { label: 'Agustus', value: 8 },
                    { label: 'September', value: 9 },
                    { label: 'Oktober', value: 10 },
                    { label: 'November', value: 11 },
                    { label: 'Desember', value: 12 }
                ]
            } else if (this.filter === 'yearly') {
                const currentYear = new Date().getFullYear()
                this.dynamicFilters = [
                    { label: 'Semua Tahun', value: null },
                    { label: currentYear, value: currentYear },
                    { label: currentYear - 1, value: currentYear - 1 },
                    { label: currentYear - 2, value: currentYear - 2 },
                    { label: currentYear - 3, value: currentYear - 3 },
                    { label: currentYear - 4, value: currentYear - 4 },
                ]
            }
        },

        async getReports() {
            try {
                this.loading = true
                let url = `http://localhost:8000/api/reports/orders?type=${this.filter}`
                if (this.selectedFilter) url += `&filter=${this.selectedFilter}`

                const res = await axios.get(url)

                this.orders = res.data.results || []
                this.summary = res.data.summary || {}
                this.orderList = res.data.order_details || []

                this.itemList = []
                this.orderList.forEach(order => {
                    if (order.items && Array.isArray(order.items)) {
                        order.items.forEach(i => {
                            this.itemList.push({
                                order_ref: order.order_ref,
                                item_name: i.item_name,
                                qty: i.qty
                            })
                        })
                    }
                })

                this.$nextTick(() => this.renderChart())
            } catch (err) {
                console.error(err)
            } finally {
                this.loading = false
            }
        },

        renderChart() {
            const labels = this.orders.map(o => o.label)
            const income = this.orders.map(o => o.total_income)
            const orders = this.orders.map(o => o.total_orders)

            if (this.chartIncome) this.chartIncome.destroy()
            if (this.chartOrder) this.chartOrder.destroy()

            this.chartIncome = new Chart(this.$refs.incomeChart, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Pendapatan', data: income, borderRadius: 8, backgroundColor: ['#6366F1', '#8B5CF6', '#EC4899', '#F43F5E', '#F59E0B', '#10B981', '#06B6D4', '#3B82F6'] }] },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            })

            this.chartOrder = new Chart(this.$refs.orderChart, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Jumlah Order', data: orders, borderColor: '#22C55E', backgroundColor: 'rgba(34,197,94,0.15)', tension: 0.4, fill: true, pointBackgroundColor: '#22C55E', pointRadius: 5 }] },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            })
        }
    }
}
</script>
<style
    scoped>
    .table-wrapper {
        flex: 1;
        overflow-y: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th {
        background: #f5f5f5;
        text-align: left;
        padding: 10px;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
    }

    .custom-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .text-right {
        text-align: right;
    }
</style>
