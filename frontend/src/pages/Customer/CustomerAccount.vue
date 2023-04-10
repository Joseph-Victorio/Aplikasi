<template>
  <q-page class="q-pb-xl">
    <q-header :class="getHeaderColorBrand" class="q-pa-sm">
      <q-toolbar>
        <q-toolbar-title>
         <q-input outlined placeholder="Cari produk" dense bg-color="white" v-model="search" @keyup.enter="searchNow" debouce="1000">
          <template v-slot:prepend>
              <q-icon
                name="eva-search"
                class="cursor-pointer"
                @click="searchNow"
              />
            </template>
        </q-input>
        </q-toolbar-title> 
      </q-toolbar>
    </q-header>
    <div class="header_banner">
      <div class="header_banner--inner large">
        <div class="bg-brand flex justify-center text-white">
          <div class="row q-gutter-sm q-mt-md q-mb-md" style="max-width:530px;">
             <div class="text-center">
               <q-btn size="19px" color="white" icon="eva-home-outline" outline padding="sm" to="/">
               </q-btn>
               <q-item-label class="color-grey-4 text-xs q-mt-xs">Beranda</q-item-label>
             </div>
             
             <div class="text-center">
               <q-btn size="19px" color="white" icon="receipt" outline padding="sm" :to="{ name: 'CustomerOrder' }">
               </q-btn>
               <q-item-label class="color-grey-4 text-xs q-mt-xs">Transaksi</q-item-label>
             </div>
             <div class="text-center">
               <q-btn size="19px" color="white" icon="eva-shopping-bag" outline padding="sm" :to="{ name: 'CustomerAddress'}">
               </q-btn>
               <q-item-label class="color-grey-4 text-xs q-mt-xs">Alamat</q-item-label>
             </div>
             <div class="text-center">
               <q-btn size="19px" color="white" icon="manage_accounts" outline padding="sm" :to="{ name: 'CustomerAccountEdit' }">
               </q-btn>
               <q-item-label class="color-grey-4 text-xs q-mt-xs">Akun</q-item-label>
             </div>
             
             <q-space />
             <div class="text-center">
               <q-btn size="19px" color="white" icon="logout" outline padding="sm" @click="logout">
               </q-btn>
               <q-item-label class="color-grey-4 text-xs q-mt-xs">Logout</q-item-label>
             </div>
           </div>
        </div>
      </div>
      <div class="relative flex justify-center">
        <div class="user-point">
          <q-card class="section">
            <q-card-section>
              
              <div class="flex justify-between items-center q-py-sm">
                <q-item>
                  <q-item-section avatar>
                    <q-avatar color="brand" text-color="white" size="70px">{{ initialName }}</q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h5 text-weight-bold">{{ user.name }}</q-item-label>
                    <q-item-label class="text-grey-7">{{ user.email }}</q-item-label>
                    <q-item-label class="text-grey-7">{{ user.phone }}</q-item-label>
                    <q-item-label class="text-grey-6 text-xs">Active from {{ dateParse(user.created_at, true) }}</q-item-label>
                  </q-item-section>
                </q-item>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
      </div>
      <div class="q-mt-sm q-pa-sm">
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
      search: '',
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
    searchNow() {
      if(!this.search || this.search == '') return
        this.$router.push({name: 'ProductSearch', query: {q: this.search }})
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