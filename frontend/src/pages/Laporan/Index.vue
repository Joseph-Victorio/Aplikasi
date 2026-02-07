<template>
    <q-page padding>
        <q-card class="q-mb-md">
            <q-card-section class=" items-center justify-between flex">
                <q-toolbar>
                    <q-btn :to="{ name: 'Settings' }" flat round dense icon="eva-arrow-back" />
                    <q-toolbar-title>
                        Dashboard Laporan Order
                    </q-toolbar-title>
                </q-toolbar>

                <q-select v-model="filter" :options="filters" emit-value map-options dense outlined
                    style="width:150px" />
            </q-card-section>

            <q-separator />

            <div class="row q-col-gutter-md q-pa-md text-center">
                <div class="col-12 col-md-4">
                    <q-card class="bg-primary text-white">
                        <q-card-section>
                            <div>Total Order</div>
                            <div class="text-h5">{{ summary.total_orders || 0 }}</div>
                        </q-card-section>
                    </q-card>
                </div>

                <div class="col-12 col-md-4">
                    <q-card class="bg-green text-white">
                        <q-card-section>
                            <div>Total Pendapatan</div>
                            <div class="text-h5">Rp {{ format(summary.total_income) }}</div>
                        </q-card-section>
                    </q-card>
                </div>

                <div class="col-12 col-md-4">
                    <q-card class="bg-warning text-white">
                        <q-card-section>
                            <div>Total Item</div>
                            <div class="text-h5">{{ summary.total_items || 0 }}</div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </q-card>
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
import axios from 'axios';

import {
    Chart,
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Legend,
    Tooltip
} from 'chart.js'

Chart.register(
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Legend,
    Tooltip
)

export default {
    name: 'LaporanIndex',

    data() {
        return {
            filter: 'monthly',
            orders: [],
            summary: {},
            chart: null,

            filters: [
                { label: 'Mingguan', value: 'weekly' },
                { label: 'Bulanan', value: 'monthly' },
                { label: 'Tahunan', value: 'yearly' }
            ],
            chartIncome: null,
            chartOrder: null,
        }
    },
    watch: {
        filter() {
            this.getReports()
        }
    },

    mounted() {
        this.getReports()
    },

    methods: {
        async getReports() {
            const res = await axios.get(`http://localhost:8000/api/reports/orders?type=${this.filter}`)

            this.orders = res.data.results || []
            this.summary = res.data.summary || {}

            this.renderChart()
        },

        renderChart() {
            const labels = this.orders.map(o => o.label)
            const income = this.orders.map(o => o.total_income)
            const orders = this.orders.map(o => o.total_orders)

            if (this.chartIncome) this.chartIncome.destroy()
            if (this.chartOrder) this.chartOrder.destroy()

            this.chartIncome = new Chart(this.$refs.incomeChart, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: income,
                        borderRadius: 8,
                        backgroundColor: [
                            '#6366F1', '#8B5CF6', '#EC4899', '#F43F5E',
                            '#F59E0B', '#10B981', '#06B6D4', '#3B82F6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            })

            this.chartOrder = new Chart(this.$refs.orderChart, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Jumlah Order',
                        data: orders,
                        borderColor: '#22C55E',
                        backgroundColor: 'rgba(34,197,94,0.15)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#22C55E',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            })
        },

        format(val) {
            return Number(val || 0).toLocaleString('id-ID')
        }
    }
}
</script>