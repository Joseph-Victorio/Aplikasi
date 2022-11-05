<template>
  <div>
    <form @submit.prevent="updateData">
      <q-card flat>
        <q-card-section>
          <div class="flex items-center justify-between">
            <div class="text-md text-weight-bold">Ekspedisi</div>
             <q-toggle v-model="formdata.is_shipping_active" :label="formdata.is_shipping_active? 'Active' : 'Inactive'" left-label color="green" class="text-grey-8"></q-toggle>
          </div>
          <div class="text-caption text-grey-7">
            <div>Pengaturan ongkir otomatis by Rajaongkir</div>
            <div>Silahkan daftar di rajaongkir.com untuk mendapatkan apikey</div>
          </div>
          <div class="q-gutter-y-sm q-py-md">
            <q-select label="Raja Ongkir Tipe" filled :options="rajaongkirtypes" v-model="formdata.rajaongkir_type" @input="selectCourierType"></q-select>
            <q-input
            filled
            v-model="formdata.rajaongkir_apikey"
            label="Raja Ongkir API KEY"
            >
            </q-input>
          </div>
          <div v-if="config && config.is_shippable" class="q-mt-lg">
            <div class="text-grey-8 text-weight-medium q-py-sm">Pengaturan Gudang Pengiriman</div>
              <div @click="changeWarehouse" class="cursor-pointer q-pa-md full-width border q-filled">{{ warehouseTitle() }}</div>
              <div class="q-mt-md" v-if="theCouriers.length">
                <div class="text-grey-8 text-weight-medium q-py-md">Pengaturan Kurir Aktif</div>
                  <div class="q-gutter-sm">
                    <q-btn unelevated rounded size="10px" v-for="(name, index) in theCouriers" :key="index" 
                    :color="isCourierActive(name)? 'primary' : 'grey-5'" @click="handleSelectCourier(name)" :label="name"></q-btn>
                  </div>
              </div>
          </div>
        </q-card-section>
        <q-card-actions class="q-pa-md justify-end">
          <q-btn type="submit" unelevated size="12px" label="Simpan Pengaturan" color="primary"></q-btn>
        </q-card-actions>
      </q-card>
    </form>
    <q-dialog v-model="modal">
      <q-card style="width:100%;max-width:500px;">
        <q-card-section>
          <div class="flex items-center justify-between">
            <div class="text-md text-weight-bold q-mb-sm">Pilih Gudang Pengiriman</div>
            <q-btn flat icon="close" v-close-popup></q-btn>
          </div>
          <div class="q-pa-sm q-gutter-y-sm">
            <q-input ref="warehouse" :loading="searchLoading && searchType == 'warehouse'" placeholder="Ketik nama kecamatan" v-model="search" debounce="1000" @input="searchWarehouseData"></q-input>
            <transition
              appear
              enter-active-class="animated fadeIn"
              leave-active-class="animated fadeOut"
            >
            <div style="min-height:100px;max-height:300px;overflow-y:auto;" class="relative bg-grey-2" v-if="searchType == 'warehouse' && subdistrictOptions.length">
              <q-list>
                <q-item v-for="item in subdistrictOptions" :key="item.id" clickable @click="selectSubdistrict(item)">
                  <q-item-section>
                    <q-item-label>{{ item.subdistrict_name }} - {{ item.type }} {{ item.city }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
                <q-inner-loading
                  :showing="searchLoading"
                >
                </q-inner-loading>
              </div>
            </transition>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  name: 'Ekspedisi',
  data () {
    return {
      codListModal: false,
      modal: false,
      rajaongkirtypes: ['starter', 'basic', 'pro'],
      subdistrictOptions: [],
      search: '',
      isWarehouseSearch: false,
      searchType: 'cod',
      searchLoading: false,
      formdata: {
        is_shipping_active: false,
        rajaongkir_type: '',
        rajaongkir_apikey: '',
        warehouse_id: '',
        warehouse_address: null,
        rajaongkir_couriers: '',
      },
    }
  },
  computed: {
    courierHasSelect() {
      if(this.formdata.rajaongkir_couriers){
        return this.formdata.rajaongkir_couriers.split(',')
      } else {
        return []
      }
    },
    theCouriers() {
      if(this.formdata.rajaongkir_type == 'pro') {
        return this.config.courier_available.pro
      } else if(this.formdata.rajaongkir_type == 'basic'){
        return this.config.courier_available.basic
      } else {
        return this.config.courier_available.starter
      }
    },
    config: function() {
      return this.$store.state.config
    }
  },
  mounted() {
    if(!this.config) {
      this.getAdminConfig()
    }else {
      this.setConfig(this.config)
    }
  },
  methods: {
    changeWarehouse() {
      this.modal = true
      setTimeout(() => {
        this.$refs.warehouse.focus()
      }, 300)
    },
    warehouseTitle() {
      if(this.formdata.warehouse_address) {
        return `${this.formdata.warehouse_address.subdistrict_name} - ${this.formdata.warehouse_address.type} ${this.formdata.warehouse_address.city}, ${this.formdata.warehouse_address.province}`
      }
      return 'Pilih Gudang Pengiriman'
    },
    setConfig(item) { 
      this.formdata.is_shipping_active = item.is_shipping_active
      this.formdata.rajaongkir_type = item.rajaongkir_type
      this.formdata.rajaongkir_apikey = item.rajaongkir_apikey
      this.formdata.rajaongkir_couriers = item.rajaongkir_couriers
      this.formdata.warehouse_address = item.warehouse_address
      this.formdata.warehouse_id = item.warehouse_id
    },
    selectCourierType() {
      this.formdata.rajaongkir_couriers = ''
    },
    isCourierActive(name) {
      if(this.courierHasSelect.length) {

        if(this.courierHasSelect.includes(name)) {
          return true

        } else {
          return false
        }
      }
      return false
    },
    handleSelectCourier(evt) {
      let courierTemp = this.courierHasSelect

      if(courierTemp.includes(evt)) {
        courierTemp = courierTemp.filter(el => el != evt)
      } else {
        courierTemp.push(evt)
      }
      if(courierTemp.length) {
        this.formdata.rajaongkir_couriers = courierTemp.join(',')
      }else {
        this.formdata.rajaongkir_couriers = ''
      }
    },
    updateData() {
      Api().post('config',  this.formdata).then(() => {
        this.$store.dispatch('getAdminConfig')
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })
      }).catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Gagal memperbarui data'
        }) 
      })
    },
    submitWarehouse() {
      this.updateData()
    },
    setLoading(status) {
      this.$store.commit('SET_LOADING', status)
    },
    selectSubdistrict(item) {
      this.formdata.warehouse_id = item.city_id
      this.formdata.warehouse_address = item
      this.modal = false
      this.subdistrictOptions = []
      this.search = ''
    },
    isInWarehouseItem(item) {
      return this.formdata.warehouse_address.subdistrict_id == item.subdistrict_id ? true : false
    },
    searchWarehouseData() {
      this.searchType = 'warehouse'
      this.findSubdistrict()
    },
    getAdminConfig() {
      Api().get('adminConfig').then((response) => {
        if(response.status == 200){
          this.setConfig(response.data.results)
        }
      })
    },
    findSubdistrict() {
      this.subdistrictOptions = []
      if(this.search.length < 3) return
      this.searchLoading = true
      Api().get('shipping/findSubdistrict/'+ this.search)
      .then(response => {
        if(response.status == 200) {
          if(response.data.success) {

            this.subdistrictOptions = response.data.results

          } else {
            this.$q.notify({
              type: 'negative',
              message: response.data.message
            })
          }
        }
      })
      .finally(() => {
         this.searchLoading = false
      })
    },
    closeSubdistrictBox() {
      this.subdistrictOptions = []
      this.search = ''
    }
  }
}
</script>
