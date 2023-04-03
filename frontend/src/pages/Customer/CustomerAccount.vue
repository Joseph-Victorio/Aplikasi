<template>
  <q-page class="q-pb-xl">
    <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-toolbar-title>
           Akun
        </q-toolbar-title> 
        <q-btn to="/"
        flat dense padding="xs"
        label="Beranda" no-caps
        icon-right="eva-arrow-forward" />
      </q-toolbar>
    </q-header>
    <div class="header_banner">
      <div class="header_banner--inner large">
        <q-card class="bg-brand" flat dark square>
          <q-list>
            <q-item>
              <q-item-section avatar>
                <q-avatar color="white" text-color="brand" size="70px">{{ initialName }}</q-avatar>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-h5 text-weight-bold">{{ user.name }}</q-item-label>
                <q-item-label class="text-grey-1">{{ user.phone }}</q-item-label>
                <q-item-label class="text-grey-4">{{ user.email }}</q-item-label>
              </q-item-section>
              <q-item-section side top >
                <div class="column q-gutter-y-sm">
                  <q-btn icon="eva-edit-2" round size="11px" color="grey-4" text-color="dark" :to="{ name: 'CustomerAccountEdit' }">
                    <q-tooltip content-class="bg-indigo" :offset="[10, 10]">Edit Akun</q-tooltip>
                  </q-btn>
                  <q-btn icon="eva-log-out" round size="11px" color="grey-4" text-color="dark" @click="logout">
                    <q-tooltip content-class="bg-amber text-black" :offset="[10, 10]">Keluar</q-tooltip>
                  </q-btn>
                </div>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card>
      </div>
      </div>
      <div class="q-mt-lg q-pa-sm">
        <div class="flex justify-between q-pa-sm items-center">
          <div class="text-weight-bold text-md">Transaksi Terbaru</div>
          <q-btn color="primary" label="Selengkapnya" flat dense no-caps icon-right="eva-chevron-right" :to="{ name: 'CustomerOrder' }"></q-btn>
        </div>

        <q-list separator>
          <q-item>
            <q-item-section side>#</q-item-section>
            <q-item-section>Invoice</q-item-section>
            <q-item-section class="mobile-hide">Total</q-item-section>
            <q-item-section>Status</q-item-section>
            <q-item-section side>Detail</q-item-section>
          </q-item>
          <q-item v-for="(order, index) in currentOrder" :key="order.id">
            <q-item-section side top>
              {{ index+1 }}
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ order.order_ref }}</q-item-label>
              <q-item-label class="desktop-hide">{{ moneyIDR(order.grand_total) }}</q-item-label>
              <q-item-label caption>{{ order.created }}</q-item-label>
            </q-item-section>
            <q-item-section class="mobile-hide">{{ moneyIDR(order.grand_total) }}</q-item-section>
            <q-item-section>
              <div class="row">
                 <q-badge class="text-center justify-center" style="min-width:90px;padding:4px;" :color="getOrderStatusColor(order.order_status)">{{ order.customer_status_label }}</q-badge>  
              </div>
            </q-item-section>
            <q-item-section side>
              <q-btn icon="eva-external-link-outline" round flat :to="{name: 'UserInvoice', params: {order_ref: order.order_ref}, query: { _rdr: $route.path }}"></q-btn>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  </q-page>
</template>

<script>
import { mapState, mapActions} from 'vuex'
import { Api } from 'boot/axios'
export default {
  data () {
    return {
      isPwd: true,
      isPwd1: true,
      changePassword: false,
      form: {
        name:'',
        email:'',
        phone: '',
        password: '',
        password_confirmation: '',
      }
    }
  },
  computed: {
    ...mapState({
      user: state => state.user.user,
      loading: state => state.loading,
      orders: state => state.order.customer_order,
    }),
    currentOrder() {
      if(this.orders.data.length) {
        return this.orders.data.slice(0, 5)
      }
      return []
    },
    initialName() {
      if(this.user) {
        let named = this.user.name.split(' ').map(el => el.slice(0,1)).join('')
        return named.slice(0,2).toUpperCase()
      }
      return 'SW'
    }
  },
  created() {
    if(!this.orders.data.length) {
      this.$store.dispatch('order/getCustomerOrders')
    }
    this.getCurrentUSer()
  },
  methods: {
    ...mapActions('user', ['getUser', 'updateUser']),
    getCurrentUSer() {
      Api().get('user').then(response => {
        if(response.status == 200) {
          this.form.name = response.data.results.name
          this.form.email = response.data.results.email
          this.form.phone = response.data.results.phone
          this.$store.commit('user/SET_USER', response.data.results)
        }
      })
    },
    submit() {
      this.$store.commit('SET_LOADING', true)
      this.updateUser(this.form)
    },
    btnChangePassword() {
      this.changePassword = !this.changePassword
      this.form.password_confirmation = ''
      this.form.password = ''
    },
    logout() {
      this.$store.dispatch('user/logout')
    },
  },

}
</script>