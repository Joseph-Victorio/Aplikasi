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
                           <q-item-label class="text-grey-7">{{ destinationAddressFormat(item.address) }}</q-item-label>
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
                  <q-btn label="Tambah Alamat" outline class="full-width" color="blue" @click="handleAddAddress"></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
      <q-dialog v-model="formAddressModal" persistent>
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-title flex justify-between q-pb-md">
                  <div>{{ formAddress._method == 'PUT' ? 'Edit' : 'Tambah' }} Alamat</div>
                  <q-btn icon="close" flat dense v-close-popup></q-btn>
               </div>
               <form @submit.prevent="submitAddress" class="q-gutter-y-md">
                  <q-input filled required label="Label" v-model="formAddress.label" placeholder="eg: Kantor"></q-input>
                  <div class="q-mt-sm">
                     <q-select required filled v-model="formAddress.subdistrict_id" use-input hide-selected fill-input
                        input-debounce="500" map-options emit-value label="Pilih Kecamatan" :options="subdistrict_options"
                        @filter="filterFn">
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
                  <q-checkbox v-if="user_address.length" label="Gunakan sebagai alamat utama"
                     v-model="formAddress.is_primary"></q-checkbox>
                  <div class="card-action">
                     <q-btn label="Simpan Alamat" class="full-width" color="primary" type="submit"></q-btn>
                  </div>
               </form>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
   props: {
      autoSelectModal: {
         type: Boolean,
         default: false
      }
   },
   data() {
      return {
         addressModal: false,
         formAddressModal: false,
         formAddress: {
            _method: '',
            id: '',
            label: '',
            is_primary: false,
            address_street: '',
            subdistrict_id: ''
         },
         subdistrict_options: [],
         current_selected_address: null
      }
   },
   mounted() {
      if (this.user_address.length) {
         this.selectPrimaryAddress()
      }
   },
   computed: {
      user_address() {
         return this.$store.state.user.address
      },
   },
   methods: {
      selectPrimaryAddress() {
         let prim = this.user_address.find(e => e.is_primary == true && e.is_complete == true)

         if (prim != undefined) {
            this.selectAddress(prim)
         }
      },
      selectAddress(item) {

         this.$store.commit('CLEAR_ERRORS')

         this.$emit('onSelectAddress', item)
         this.addressModal = false

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
         const addr = this.subdistrict_options.find(el => el.id == this.formAddress.subdistrict_id)

         const data = { ...this.formAddress }
         data.address = addr

         this.$store.commit('user/PUSH_ADDRESS', data)

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
         this.formAddress.subdistrict_id = ''

         this.addressModal = false
         this.formAddressModal = true
      },
      handleEditAddress(item) {
         const data = { ...item }
         this.formAddress._method = 'PUT'
         this.formAddress.id = data.id
         this.formAddress.label = data.label
         this.formAddress.is_primary = data.is_primary
         this.formAddress.address_street = data.address_street
         this.formAddress.subdistrict_id = data.subdistrict_id ?? ''
         if (data.address) {
            this.subdistrict_options = [data.address]
         }
         this.formAddressModal = true
      },
      destinationAddressFormat(obj) {
         if (!obj) {
            return ''
         }
         return `${obj.subdistrict_name} - ${obj.type} ${obj.city}, ${obj.province}`
      },
      async filterFn(val, update, abort) {
         if (val == '' || val.length <= 3) {
            update()

            return
         }

         let response = await Api().get('searchAddress/' + val)

         if (response.status == 200) {
            update(() => {
               this.subdistrict_options = response.data.results
            })
         }
      },
      handleDeleteAddress(id) {
         this.$q.dialog({
            title: 'Konfirmasi',
            message: 'Yakin Akan menghapus data?',
            cancel: true,
         }).onOk(() => {
            this.$store.commit('user/DELETE_ADDRESS', id)
            this.$emit('onSelectAddress', null)
         })
      }
   }
}
</script>