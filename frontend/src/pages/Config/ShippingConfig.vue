<template>
   <div>
      <form @submit.prevent="updateData">
         <q-card flat class="q-pa-sm">
            <q-card-section>
               <div class="flex items-center justify-between">
                  <div class="text-md text-weight-bold">Ekspedisi</div>
                  <q-toggle v-model="formdata.is_shipping_active"
                     :label="formdata.is_shipping_active ? 'Active' : 'Disabled'" left-label color="teal"
                     class="text-grey-8"></q-toggle>
               </div>
               <div class="text-caption text-grey-7">
                  <div>Pengaturan ongkir otomatis by Rajaongkir</div>
                  <div>Silahkan daftar di rajaongkir.com untuk mendapatkan apikey</div>
               </div>
               <div class="q-gutter-y-sm q-py-md">
                  <q-select label="Raja Ongkir Paket" filled :options="rajaongkirtypes"
                     v-model="formdata.rajaongkir_type" @input="selectCourierType"></q-select>
                  <q-input filled v-model="formdata.rajaongkir_apikey" label="Raja Ongkir API KEY">
                  </q-input>
               </div>
               <div v-if="config" class="q-mt-lg">
                  <div class="text-grey-8 text-weight-medium q-py-sm">Pengaturan Gudang Pengiriman</div>
                  <div @click="changeWarehouse" class="cursor-pointer q-pa-md full-width border q-filled">{{
                     warehouseTitle() }}
                  </div>
                  <div class="q-mt-md" v-if="theCouriers.length">
                     <div class="text-grey-8 text-weight-medium q-pt-md text-md">Pengaturan Kurir</div>
                     <div class="text-grey-7 text-caption q-pb-md">Aktifkan kurir yang yang akan digunakan</div>
                     <div class="q-gutter-sm">
                        <q-btn no-caps unelevated padding="3px 8px" :outline="!isCourierActive(name)" size="13px"
                           v-for="(name, index) in theCouriers" :key="index"
                           :color="isCourierActive(name) ? 'green' : 'grey-7'" @click="handleSelectCourier(name)">
                           <q-icon :name="isCourierActive(name) ? 'eva-checkmark-square' : 'eva-square'"></q-icon>
                           <span class="q-ml-xs">{{ name.label }}</span>
                        </q-btn>
                     </div>
                  </div>
               </div>
            </q-card-section>
            <q-card-actions class="q-py-lg">
               <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
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
                  <q-input ref="warehouse" :loading="searchLoading && searchType == 'warehouse'"
                     placeholder="Ketik nama kecamatan" v-model="search" debounce="1000"
                     @input="searchWarehouseData"></q-input>
                  <transition appear enter-active-class="animated fadeIn" leave-active-class="animated fadeOut">
                     <div style="min-height:100px;max-height:300px;overflow-y:auto;" class="relative bg-grey-2"
                        v-if="searchType == 'warehouse' && subdistrictOptions.length">
                        <q-list>
                           <q-item v-for="item in subdistrictOptions" :key="item.id" clickable
                              @click="selectSubdistrict(item)">
                              <q-item-section>
                                 <q-item-label>{{ item.subdistrict_name }} - {{ item.type }} {{ item.city
                                    }}</q-item-label>
                              </q-item-section>
                           </q-item>
                        </q-list>
                        <q-inner-loading :showing="searchLoading">
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
   data() {
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
            rajaongkir_couriers: [],
         },
      }
   },
   watch: {
      'formdata.is_shipping_active'(val) {
         if (val == true) {
            if (
               !this.formdata.rajaongkir_type ||
               !this.formdata.rajaongkir_apikey ||
               !this.formdata.warehouse_id ||
               !this.formdata.warehouse_address ||
               !this.formdata.rajaongkir_couriers.length
            ) {

               this.formdata.is_shipping_active = false

               this.$q.notify({
                  type: 'warning',
                  message: 'Untuk mengaktifkan servis, semua input tidak boleh kosong'
               })
               return
            }
         }
      }
   },
   computed: {
      theCouriers() {
         if (this.formdata.rajaongkir_type == 'pro') {
            return this.config.courier_available.pro
         } else if (this.formdata.rajaongkir_type == 'basic') {
            return this.config.courier_available.basic
         } else {
            return this.config.courier_available.starter
         }
      },
      config: function () {
         return this.$store.state.config
      },
      isCouriersValueActive() {
         return this.formdata.rajaongkir_couriers.map(t => t.value)
      },
   },
   mounted() {
      if (!this.config) {
         this.getAdminConfig()
      } else {
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
         if (this.formdata.warehouse_address) {
            return `${this.formdata.warehouse_address.subdistrict_name} - ${this.formdata.warehouse_address.type} ${this.formdata.warehouse_address.city}, ${this.formdata.warehouse_address.province}`
         }
         return 'Pilih Gudang Pengiriman'
      },
      setConfig(item) {
         this.formdata.is_shipping_active = item.is_shipping_active
         this.formdata.rajaongkir_type = item.rajaongkir_type
         this.formdata.rajaongkir_apikey = item.rajaongkir_apikey
         this.formdata.rajaongkir_couriers = item.rajaongkir_couriers && item.rajaongkir_couriers.length ? item.rajaongkir_couriers : []
         this.formdata.warehouse_address = item.warehouse_address
         this.formdata.warehouse_id = item.warehouse_id
      },
      selectCourierType() {
         this.formdata.rajaongkir_couriers = []
      },
      isCourierActive(name) {
         if (this.formdata.rajaongkir_couriers.length) {

            if (this.isCouriersValueActive.includes(name.value)) {
               return true

            } else {
               return false
            }
         }
         return false
      },
      handleSelectCourier(evt) {

         let already = this.formdata.rajaongkir_couriers.find(i => i.value == evt.value)

         if (already != undefined) {
            this.formdata.rajaongkir_couriers = this.formdata.rajaongkir_couriers.filter(el => el.value != already.value)
         } else {
            this.formdata.rajaongkir_couriers.push(evt)
         }

      },
      updateData() {
         Api().post('config', this.formdata).then(() => {
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
         return this.formdata.warehouse_address.id == item.id ? true : false
      },
      searchWarehouseData() {
         this.searchType = 'warehouse'
         this.findAddress()
      },
      getAdminConfig() {
         Api().get('admin-config').then((response) => {
            if (response.status == 200) {
               this.setConfig(response.data.results)
            }
         })
      },
      findAddress() {
         this.subdistrictOptions = []
         if (this.search.length < 3) return
         this.searchLoading = true
         Api().get('searchAddress/' + this.search)
            .then(response => {
               if (response.status == 200) {
                  if (response.data.success) {

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
