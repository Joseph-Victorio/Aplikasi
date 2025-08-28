<template>
   <q-card flat class="q-pa-sm">
      <form @submit.prevent="updateData">
         <q-card flat>
            <q-card-section>
               <div class="flex items-center justify-between">
                  <div>
                     <div class="text-md text-weight-bold">Ambil di Toko</div>
                     <q-item-label caption>Memungkinkan pembeli untuk ambil pesanan ditoko</q-item-label>
                  </div>

                  <q-toggle @input="autoUpdate" v-model="formdata.is_pic_order" :label="formdata.is_pic_order ? 'Active' : 'Disabled'"
                     left-label color="teal" class="text-grey-8"></q-toggle>
               </div>
               <div class="flex items-center justify-between q-mt-md">
                  <div>
                     <div class="text-md text-weight-bold">Kurir Toko</div>
                     <q-item-label caption>Pengaturan kurir toko berdasarkan kecamatan</q-item-label>

                  </div>

                  <q-toggle @input="autoUpdate" v-model="formdata.is_local_shipping_active"
                     :label="formdata.is_local_shipping_active ? 'Active' : 'Disabled'" left-label color="teal"
                     class="text-grey-8"></q-toggle>
               </div>

               <div class="q-mt-md">
                  <div class="">
                     <div class="text-md text-weight-bold">
                        Pengiriman dan Koordinat Toko
                     </div>
                     <div class="text-grey-8 q-pt-sm text-13">
                        Gunakan tombol lokasi terkini atau klik didalam map untuk
                        mendapatkan lokasi toko atau geser - geser ikon toko untuk
                        medapatkan lokasi yang sesuai
                     </div>
                  </div>

                  <div class="q-mt-md">
                     <div class="warehouse-map" v-if="config">
                        <MainMap ref="theMap" :config="config" :coordinate="formdata.warehouse_coordinate" @onEmitMap="onEmitMap" />
                     </div>
                  </div>
                  <div class="q-mt-lg">
                     <div class="flex justify-between no-wrap items-center q-py-sm">
                        <div>
                           <div class="text-weight-medium text-md">Aturan Ongkos Kirim</div>
                           <p class="text-13 text-grey-7">
                              Perhatikan baik - baik saat menambahkan aturan ongkos kirim,
                              jarak yang diluar aturan akan diabaikan dan menjadi diluar
                              jangkauan kurir.
                           </p>
                        </div>
                        <q-btn label="Tambah Aturan" color="teal" unelevated class="btn-action" @click="addRule"
                           no-caps></q-btn>
                     </div>
                     <div class="table-responsive">
                        <table class="table aligned dense">
                           <thead>
                              <tr>
                                 <th>Flat Ongkir *</th>
                                 <th>Radius (KM)</th>
                                 <th>Biaya</th>
                                 <th>Delete</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr v-for="(item, idx) in formdata.local_shipping_costs" :key="idx" class="q-px-none">
                                 <td>
                                    <q-checkbox class="q-py-xs" outlined dense
                                       v-model="formdata.local_shipping_costs[idx].flat"></q-checkbox>
                                 </td>
                                 <td>
                                    <q-input class="q-py-xs" outlined dense
                                       v-model="formdata.local_shipping_costs[idx].radius" label="Radius (KM)" mask="####"
                                       suffix="KM"></q-input>
                                 </td>
                                 <td>
                                    <q-input class="q-py-xs" outlined dense
                                       v-model="formdata.local_shipping_costs[idx].cost"
                                       :label="formdata.local_shipping_costs[idx].flat ? 'Ongkos Kirim Flat' : 'Ongkos Kirim /km'"
                                       mask="##########"></q-input>
                                 </td>
                                 <td>
                                    <div>
                                       <q-btn icon="delete" round flat padding="xs" color="red"
                                          @click="removeCost(idx)" />
                                    </div>
                                 </td>
                              </tr>

                           </tbody>
                        </table>

                     </div>
                     <div class="text-center q-py-md" v-if="!formdata.local_shipping_costs.length">Tidak ada data</div>
                     <div class="text-caption q-py-md">
                        * Flat ongkir adalah tarif tetap dalam radius tertentu alias tidak dihitung per km
                     </div>
                  </div>
               </div>
            </q-card-section>
            <q-card-actions class="q-py-lg">
               <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
            </q-card-actions>
         </q-card>
      </form>
   </q-card>
</template>

<script>
import { Api } from 'boot/axios'
import MainMap from 'components/MainMap.vue'
export default {
   name: 'LocalShipping',
   components: { MainMap },
   data() {
      return {
         codListModal: false,
         subdistrictOptions: [],
         modal: false,
         search: '',
         searchLoading: false,
         formdata: {
            cod_list: [],
            is_cod_payment: false,
            is_local_shipping_active: false,
            is_pic_order: false,
            local_shipping_costs: [],
            warehouse_coordinate: []
         },
      }
   },
   watch: {
      'formdata.is_local_shipping_active'(val) {
         if (val == true) {
            if (this.formdata.is_local_shipping_active && !this.formdata.local_shipping_costs.length && !this.formdata.warehouse_coordinate.length) {
               this.formdata.is_local_shipping_active = false
               this.$q.notify({
                  type: 'negative',
                  message: 'Koordinate dan ongkos kirim belum di input'
               })
               return
            }
         }
      }
   },
   computed: {
      config: function () {
         return this.$store.state.config
      },
       map_radius() {
         if (this.formdata.local_shipping_costs.length) {
            let r = this.formdata.local_shipping_costs[this.formdata.local_shipping_costs.length - 1].radius
            return parseInt(r * 1000)
         }
         return 0
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
      autoUpdate() {
         setTimeout(() => {
            this.updateData()
         }, 500)
      },
      setConfig(item) {
         console.log(item);
         
         this.formdata.is_cod_payment = item.is_cod_payment
         this.formdata.is_local_shipping_active = item.is_local_shipping_active
         this.formdata.is_pic_order = item.is_pic_order
         this.formdata.warehouse_coordinate = item.warehouse_coordinate
            ? item.warehouse_coordinate
            : [];
         this.formdata.local_shipping_costs = item.local_shipping_costs
            ? item.local_shipping_costs
            : [];
      },
      onEmitMap(evt) {
         this.formdata.warehouse_coordinate[0] = evt[0];
         this.formdata.warehouse_coordinate[1] = evt[1];
      },

      removeCost(idx) {
         this.formdata.local_shipping_costs.splice(idx, 1);
      },

      addRule() {
         let start = 0;

         if (this.formdata.local_shipping_costs.length) {
            let endItem = this.formdata.local_shipping_costs[this.formdata.local_shipping_costs.length - 1];

            start = parseInt(endItem.end) + 1;
         }
         this.formdata.local_shipping_costs.push({ cost: 500, radius: start, flat: false });
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
      selectCodItemData(data) {
         let hasData = this.formdata.cod_list.filter(elj => elj.id == data.id)
         if (hasData.length) {
            this.formdata.cod_list = this.formdata.cod_list.filter(elm => elm.id != data.id)
         } else {
         }
         this.formdata.cod_list.push({ ...data, price: 0 })
         this.search = ''
         this.subdistrictOptions = []
      },
      hasCodData(item) {
         let has = this.formdata.cod_list.filter(en => en.id == item.id)
         if (has.length) {
            return true
         } else {
            return false
         }
      },
      removeCodList(index) {
         this.formdata.cod_list.splice(index, 1)
      },
      searchCodData() {
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
