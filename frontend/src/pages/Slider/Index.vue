<template>
  <q-page class="q-pb-xl">
    <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
          Slider Banner
        </q-toolbar-title>
        <q-btn color="white" text-color="grey-8" size="13px" class="gt-xs" icon="add_circle" label="Tambah Slider" no-caps @click="handleBtnUpload"/>
      </q-toolbar>
    </q-header>
   <div class="q-pa-sm text-xs text-grey-8 bg-yellow-2">
     <div>Note</div>
     <div>Untuk hasil terbaik, Gunakan gambar dengan ukuran yang sama.</div>
   </div>
     <input type="file" class="hidden" ref="image" @change="handleFileUpload">
     <div>
       <q-list separator>
         <q-item class="item-header">
           <q-item-section style="min-width:110px;">Image</q-item-section>
           <q-item-section>Sort</q-item-section>
           <q-item-section side>Action</q-item-section>
         </q-item>
         <q-item v-for="slider in sliders.data" :key="slider.id">
           <q-item-section>
             <q-img :src="slider.src" class="bg-white rounded-corners img-thumbnail img-avatar" ratio="1"/>
           </q-item-section>
           <q-item-section>
             <div class="row q-gutter-sm items-center">
               <q-btn icon="remove" size="sm" unelevated round class="bg-blue-grey-1" @click="decre(slider)" />
               <span class="text-md">{{ slider.weight }}</span>
               <q-btn icon="add" size="sm" round unelevated class="bg-blue-grey-1" @click="incre(slider)" />
             </div>
           </q-item-section>
           <q-item-section center side>
             <q-btn @click="remove(slider.id)" size="sm" round icon="delete" color="red"/>
           </q-item-section>
         </q-item>
       </q-list>
     </div>
     <template v-if="!sliders.available">
       <div class="absolute-center">Tidak ada data</div>
     </template>
   <q-inner-loading :showing="!sliders.ready">
      
   </q-inner-loading>
   <q-page-sticky class="lt-sm" position="bottom-right" :offset="[18, 18]">
     <q-btn fab icon="add" color="primary" glossy @click="handleBtnUpload"/>
   </q-page-sticky>
 </q-page>
</template>

<script>
import { Api } from 'boot/axios'
import { mapState, mapActions } from 'vuex'
export default {
 data () {
   return {
     timeout: null,
     uploadPercentage: 0
   }
 },
 computed: {
   ...mapState({
     sliders: state => state.slider.sliders,
     loading: state => state.loading
   })
 },
 mounted() {
   if(!this.sliders.data.length) this.getSliders()
 },
 methods: {
   ...mapActions('slider',['removeSlider','getSliders', 'updateSliderWeight']),

   changeWeight(weight, id) {
     
     this.updateSliderWeight({ value: weight, id: id })
       
   },

   incre(slider) {
     this.changeWeight(slider.weight+1, slider.id)
     // if (this.timeout) clearTimeout(this.timeout); 
     // this.timeout = setTimeout(() => {
     //   }, 1000); // delay
   },
   decre(slider) {
     if(slider.weight <=1) return
       this.changeWeight(slider.weight-1, slider.id)

     // if (this.timeout) clearTimeout(this.timeout); 
     // this.timeout = setTimeout(() => {
     //   }, 100); // delay
   },

   remove(id) {
     this.$q.dialog({
       title: 'Konfirmasi Penghapusan Item',
       message: 'Yakin akan menghapus data ini?',
       ok: {label: 'Hapus', flat: true, 'no-caps': true},
       cancel: {label: 'Batal', flat: true, 'no-caps': true},
     }).onOk(() => {
       this.removeSlider(id)
     })
   },
   handleBtnUpload() {
     this.$refs.image.click()
   },
   handleFileUpload(){
     let self = this;
     self.$store.commit('slider/SET_DATA_STATUS', false)
     let formData = new FormData();
     formData.append('image', this.$refs.image.files[0]);

     Api().post('sliders',
       formData,
       {
         headers: {'Content-Type': 'multipart/form-data' },
         onUploadProgress: (progressEvent) => {
               this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ))
             }
       }).then(function(res){
         self.getSliders()
       })
       .catch(function(err){
          self.$store.commit('slider/SET_DATA_STATUS', true)
       });
     },
   }
}
</script>
