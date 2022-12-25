<template>
  <q-page :class="{'flex flex-center' : !categories.available}">
   <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Kategori
        </q-toolbar-title>
      <q-btn class="gt-xs" outline label="Tambah Kategori" no-caps icon="eva-plus-circle" :to="{name: 'CategoryForm'}"/>
      </q-toolbar>
    </q-header>
    <template v-if="categories.available">
    <div class="">
      <q-list separator>
        <q-item class="bg-grey-2">
          <q-item-section avatar>
           Ikon
          </q-item-section>
          <q-item-section>
            Label
          </q-item-section>
          <q-item-section>
            Urutan
          </q-item-section>
          <q-item-section>
            Listing
          </q-item-section>
          <q-item-section side>
            Aksi
          </q-item-section>
        </q-item>
        <q-item v-for="cat in categories.data" :key="cat.id">
          <q-item-section avatar>
            <q-img :src="cat.src" ratio="1"/>
          </q-item-section>
          <q-item-section>
            <!-- <q-chip icon="sell">{{ cat.title }}</q-chip> -->
            <q-item-label>{{ cat.title }}</q-item-label>
          </q-item-section>
          <q-item-section >
            <div class="q-pa-md">{{ cat.weight }}</div>
          </q-item-section>
          <q-item-section>
            <div>
             <q-chip size="sm" :color="cat.is_front? 'positive' : 'grey'" text-color="white" icon="eva-checkmark-circle-2">
              {{ cat.is_front? 'Yes' : 'No'}}
            </q-chip>
            </div>
          </q-item-section>
         
          <q-item-section side>
            <div class="text-grey-8 q-gutter-xs column">
              <q-btn @click="remove(cat.id)" size="sm" round icon="eva-trash-2" glossy color="red"/>
              <q-btn :to="{ name: 'CategoryForm', query: {edit: cat.id }}" size="sm" round icon="eva-edit-2" glossy color="info"/>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
    </template>
    <template v-else>
      <div>Tidak ada data</div>
    </template>
    <q-inner-loading :showing="!categories.ready">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <q-page-sticky class="lt-sm" position="bottom-right" :offset="[12, 12]">
      <q-btn glossy fab icon="add" color="primary" :to="{name: 'CategoryForm'}"/>
    </q-page-sticky>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
export default {
  name: 'Categories',
  data () {
    return {
      modal: false,
      removeId: null
    }
  },
  computed: {
    ...mapState({
      categories: state => state.category.categories
    })
  },
  methods: {
    ...mapActions('category',['getAdminCategories', 'categoryDelete']),
    remove (id) {
      this.removeId = id
      this.$q.dialog({
        title: 'Konfirmasi Penghapusan Item',
        message: 'Yakin akan menghapus data?',
        ok: {label: 'Hapus', flat: true, 'no-caps': true},
        cancel: {label: 'Batal', flat: true, 'no-caps': true},
      }).onOk(() => {
        this.categoryDelete(this.removeId)
      })
    }
  },
  created () {
    if(!this.categories.data.length) this.getAdminCategories()
  }
}
</script>
