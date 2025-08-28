<template>
   <div>
      <q-dialog v-model="addressModal">
         <q-card>
            <q-card-section v-if="user_address.length">
               <div class="card-title flex justify-between q-mb-md items-center">
                  <div>Pilih Alamat</div>
                  <q-btn flat icon="close" round v-close-popup dense></q-btn>
               </div>
               <div class="scroll q-pa-sm bg-grey-2" style="height:50vh">
                  <q-list separator>
                     <q-item v-for="(item, idx) in user_address" :key="idx" class="bg-white">
                        <q-item-section>
                           <q-item-label class="row items-center q-gutter-x-sm">
                              <div class="text-md">{{ item.label }} </div>
                              <q-badge color="green" v-if="item.is_primary" label="Utama"></q-badge>
                           </q-item-label>
                           <q-item-label class="q-pt-xs text-grey-7">{{ item.address_street }}</q-item-label>
                           <q-item-label class="text-grey-7">{{ item.address.name }}</q-item-label>
                           <q-item-label v-if="!item.is_complete"> <q-badge color="red-4"
                                 outline>incomplete</q-badge></q-item-label>
                        </q-item-section>
                        <q-item-section side class="q-gutter-xs">
                           <q-btn v-if="item.is_complete" style="width:70px" unelevated label="Pilih" color="teal"
                              size="sm" @click="selectAddress(item)"></q-btn>
                           <q-btn style="width:70px" unelevated label="Edit" color="blue" size="sm"
                              @click="handleEditAddress(item)"></q-btn>
                           <q-btn style="width:70px" unelevated label="Delete" color="red" size="sm"
                              @click="handleDeleteAddress(item.id)"></q-btn>
                        </q-item-section>
                     </q-item>
                  </q-list>
               </div>
               <div class="q-pa-sm">
                  <q-btn label="Tambah Alamat" outline class="full-width" color="blue"
                     @click="handleAddAddress"></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
      <q-dialog v-model="formAddressModal" persistent no-shake position="bottom">
         <q-card class="max-width">
            <q-card-section class="q-pa-none" style="min-height:320px">
               <div class="card-title flex justify-between q-py-sm q-px-md sticky-top bg-white">
                  <div>{{ formAddress._method == 'PUT' ? 'Edit' : 'Tambah' }} Alamat</div>
                  <q-btn icon="close" flat dense v-close-popup></q-btn>
               </div>
               <div class="q-pa-md">

                  <form @submit.prevent="submitAddress" class="q-gutter-y-md">
                     <q-input filled required label="Label" v-model="formAddress.label"
                        placeholder="eg: Kantor"></q-input>
                     <div class="q-mt-sm">
                        <q-select required filled v-model="formAddress.address" use-input hide-selected fill-input
                           :input-debounce="1000" label="Ketik Kecamatan" :options="subdistrict_options"
                           @input="selectCurrentAddress" @filter="filterFn">
                           <template v-slot:no-option>
                              <q-item>
                                 <q-item-section class="text-grey">
                                    No results
                                 </q-item-section>
                              </q-item>
                           </template>
                        </q-select>
                     </div>
                     <q-input filled required type="textarea" v-model="formAddress.address_street"
                        label="Alamat Lengkap"></q-input>
                     <div v-if="config" class="q-mt-md q-pa-sm">
                        <div class="card-subtitle">
                           <div>Pilih Koordinat</div>
                           <q-item-label caption>
                              Klik pada map atau geser posisi ikon kurir untuk mendapatkan koordinat yang tepat
                           </q-item-label>
                        </div>
                        <div>
                           <ClientMap ref="clientMap" :config="config" :coordinate="formAddress.coordinate"
                              :is_client="true" @onSelected="onEmitMap" @onSelectAddress="selectMapAddress"
                              @onError="(m) => error_map = m" />
                           <div class="text-amber-10 q-pa-xs text-sm" v-if="error_map">{{ error_map }}</div>

                        </div>
                     </div>

                     <q-checkbox v-if="user_address.length" label="Gunakan sebagai alamat utama"
                        v-model="formAddress.is_primary"></q-checkbox>
                     <div class="card-action q-py-md sticky-bottom bg-white" style="z-index:1000">
                        <q-btn label="Simpan Alamat" class="full-width" color="primary" type="submit"></q-btn>
                     </div>
                  </form>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { Api } from 'boot/axios'
import ClientMap from 'components/ClientMap.vue'
export default {
   components: { ClientMap },
   props: {
      autoSelectModal: {
         type: Boolean,
         default: false
      }
   },
   data() {
      return {
         error_map: '',
         addressModal: false,
         formAddressModal: false,
         formAddress: {
            _method: '',
            id: '',
            label: '',
            is_primary: false,
            address_street: '',
            coordinate: [],
            address: ''
         },
         subdistrict_options: [],
      }
   },
   mounted() {
      setTimeout(() => {
         if (this.user_address.length) {
            this.selectAddress(this.user_address[0])
         }
      }, 1000)
   },
   computed: {
      user_address() {
         return this.$store.state.user.address
      },
      config() {
         return this.$store.state.config
      }
   },
   methods: {
      selectAddress(item) {
         this.$store.commit('CLEAR_ERRORS')

         this.$emit('onSelectAddress', item)
         this.addressModal = false

      },
      selectCurrentAddress(item) {
         this.error_map = ''
         let search = `${item.subdistrict} ${item.city.replace('Kabupaten ', '')}`
         this.$refs.clientMap.autoSearch(search)
      },
      selectMapAddress(list) {
         let latlng = [list.y, list.x]
         this.formAddress.coordinate = latlng

      },
      onEmitMap(evt) {
         this.formAddress.coordinate = evt.user_coordinate;
      },
      submitAddress() {
         this.handleSavelocalAddress()

         setTimeout(() => {
            if (this.user_address.length == 1) {
               this.selectAddress(this.user_address[0])
            }
         }, 500)
      },
      handleSavelocalAddress() {
         if (!this.formAddress.id) {

            this.formAddress.id = this.getRandomString(16)
         }
         this.formAddress.is_complete = true

         this.$store.commit('user/PUSH_ADDRESS', this.formAddress)

         this.formAddressModal = false
      },
      handleOpenAddressModal() {
         this.addressModal = true
      },
      handleAddAddress() {
         this.formAddress._method = 'POST'
         this.formAddress.id = ''
         this.formAddress.label = ''
         this.formAddress.is_primary = false
         this.formAddress.address_street = ''
         this.formAddress.address = ''

         if (!this.user_address.length) {
            this.formAddress.label = 'Rumah'
         }

         this.addressModal = false
         this.formAddressModal = true
      },
      handleEditAddress(item) {
         const data = { ...item }
         this.formAddress = { ...this.formAddress, ...data }
         if (data.address) {
            this.subdistrict_options = [data.address]
         }
         this.formAddressModal = true

      },
      async filterFn(val, update, abort) {
         if (val == '' || val.length <= 3) {
            update()

            return
         }

         Api().get('searchAddress/' + val).then(res => {
            if (res.data.success) {
               update(() => {
                  this.subdistrict_options = res.data.results
               })
            } else {
               this.$q.notify({
                  type: 'negative',
                  message: res.data.message
               })
            }
         }).catch((err) => {
            this.$q.notify({
               type: 'negative',
               message: err.response.data.message
            })
            update()

         })

      },
      handleDeleteAddress(id) {
         this.$q.dialog({
            title: 'Konfirmasi',
            message: 'Yakin Akan menghapus data?',
            cancel: true,
         }).onOk(() => {
            this.$store.commit('user/DELETE_ADDRESS', id)
            this.$emit('onSelectAddress', null)

            if (!this.user_address.length) {
               this.addressModal = false
            }
         })
      }
   }
}
</script>