<template>
   <q-card flat class="q-pa-sm">
      <form @submit.prevent="updateData">
         <q-card flat>
            <q-card-section>
               <div class="flex items-center justify-between">
                  <div class="text-md text-weight-bold">Kurir Toko</div>

                  <q-toggle v-model="formdata.is_local_shipping_active"
                     :label="formdata.is_local_shipping_active ? 'Active' : 'Disabled'" left-label color="teal"
                     class="text-grey-8"></q-toggle>
               </div>
               <div class="text-caption text-grey-7">
                  <div class="q-mb-sm text-caption text-grey-7">Pengaturan kurir toko berdasarkan kecamatan</div>
               </div>

               <div>
                  <div>
                     <q-input filled :loading="searchLoading" placeholder="Tambah kecamatan tujuan" v-model="search"
                        debounce="600" @input="searchCodData">
                        <template slot="append" v-if="search">
                           <q-btn icon="close" flat @click="closeSubdistrictBox" color="red" round></q-btn>
                        </template>
                     </q-input>
                     <div class="relative" v-if="subdistrictOptions.length">
                        <transition appear enter-active-class="animated fadeIn" leave-active-class="animated fadeOut">
                           <div class="absolute full-width z-30">
                              <q-card class="bg-white full-width full-height">
                                 <div class="scroll" style="height:100%;max-height:300px;">
                                    <q-list class="bg-grey-1">
                                       <q-item clickable v-for="(itemData, index) in subdistrictOptions" :key="index"
                                          @click="selectCodItemData(itemData)">
                                          <q-item-section side>
                                             <template v-if="hasCodData(itemData)">
                                                <q-icon name="remove_circle" size="17px" color="red"></q-icon>
                                             </template>
                                             <template v-else>
                                                <q-icon name="add_circle" size="17px" color="primary"></q-icon>
                                             </template>
                                          </q-item-section>
                                          <q-item-section>
                                             <q-item-label>{{ itemData.subdistrict_name }} - {{ itemData.type }} {{
                                                itemData.city
                                                }}</q-item-label>
                                          </q-item-section>
                                       </q-item>
                                    </q-list>
                                 </div>
                                 <q-inner-loading :showing="searchLoading">
                                 </q-inner-loading>
                              </q-card>
                           </div>
                        </transition>
                     </div>
                  </div>
                  <div class="q-py-md">
                     <q-list separator>
                        <q-separator></q-separator>
                        <q-item>
                           <q-item-section>
                              Kecamatan Tujuan
                           </q-item-section>
                           <q-item-section side>
                              Ongkos Kirim
                           </q-item-section>
                        </q-item>
                        <q-separator></q-separator>
                        <q-item v-for="(codItem, index) in formdata.cod_list" :key="index" class="q-px-xs">
                           <q-item-section side>
                              <q-btn @click="removeCodList(index)" icon="close" color="red" dense flat round></q-btn>
                           </q-item-section>
                           <q-item-section>
                              <q-item-label>{{ codItem.subdistrict_name }} {{ codItem.type }} {{ codItem.city
                                 }}</q-item-label>
                           </q-item-section>
                           <q-item-section side>
                              <div>
                                 <q-input filled square dense min="0" prefix="Rp" style="width:120px;" mask="########"
                                    v-model="formdata.cod_list[index].price" required></q-input>
                              </div>
                           </q-item-section>
                        </q-item>
                     </q-list>
                     <div v-if="!formdata.cod_list.length" class="text-center q-py-lg">Tidak ada data</div>
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
export default {
   name: 'LocalShipping',
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
            is_local_shipping_active: false
         },
      }
   },
   watch: {
      'formdata.is_local_shipping_active'(val) {
         if (val == true) {
            if (this.formdata.is_local_shipping_active && !this.formdata.cod_list.length) {
               this.formdata.is_local_shipping_active = false
               this.$q.notify({
                  type: 'warning',
                  message: 'Untuk mengaktifkan servis, kecamatan tidak boleh kosong'
               })
               return
            }
         }
      }
   },
   computed: {
      config: function () {
         return this.$store.state.config
      }
   },
   mounted() {
      if (!this.config) {
         this.getAdminConfig()
      } else {
         this.setConfig(this.config)
      }
   },
   methods: {
      setConfig(item) {
         this.formdata.is_cod_payment = item.is_cod_payment
         this.formdata.is_local_shipping_active = item.is_local_shipping_active
         if (item.cod_list) {
            item.cod_list.forEach(element => {
               this.formdata.cod_list.push(element)
            });
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
      setLoading(status) {
         this.$store.commit('SET_LOADING', status)
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
