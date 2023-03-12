<template>
  <q-page :class="{'flex flex-center' : !categories.available}">
   <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Kategori
        </q-toolbar-title>
      <q-btn color="white" text-color="grey-8" size="13px" class="gt-xs" label="Tambah Kategori" no-caps icon="eva-plus-circle" :to="{name: 'CategoryForm'}"/>
      </q-toolbar>
    </q-header>
    <template v-if="categories.available">
    <div class="">
      <q-list separator>
        <q-item class="item-header">
          <q-item-section style="width:60px" side>
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
        <q-expansion-item v-for="cat in categories.data" :key="cat.id" expand-separator group="menu-category">
        <template v-slot:header>
          <q-item-section avatar>
            <q-img :src="cat.src" ratio="1" class="img-thumbnail img-avatar"/>
          </q-item-section>
          <q-item-section>
            {{ cat.title }}
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
            <div class="row items-center q-gutter-x-sm">
              <q-btn dense size="md" @click="remove(cat.id)" flat icon="eva-trash-2" />
              <q-btn dense size="md" :to="{ name: 'CategoryFormEdit', params: { category_id: cat.id }}" flat icon="eva-edit-2" />
            </div>
          </q-item-section>
        </template>
          <q-list separator class="bg-grey-1">
            <q-item v-for="item in cat.childs" :key="item.id" class="q-px-lg">
              <q-item-section side>
                <q-icon name="radio_button_off" size="17px"/>
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ item.title }}</q-item-label>
              </q-item-section>
              <q-item-section side>
                <div class="text-grey-8 q-gutter-x-sm">
                  <q-btn dense size="md" flat @click="remove(item.id)" icon="eva-trash-2" color="red"/>
                  <q-btn dense size="md" flat :to="{ name: 'CategoryFormEdit', params: {category_id: item.id }}" icon="eva-edit-2" color="blue"/>
                </div>
              </q-item-section>
            </q-item>
            <div v-if="!cat.childs.length" class="q-py-lg text-center">Tidak ada subkategori</div>
          </q-list>
        </q-expansion-item>
      </q-list>
    </div>
    </template>
    <template v-else>
      <div>Tidak ada data</div>
    </template>
    <q-inner-loading :showing="!categories.ready">
    </q-inner-loading>
    <q-page-sticky class="lt-sm" position="bottom-right" :offset="[12, 12]">
      <q-btn glossy fab icon="add" color="primary" :to="{name: 'CategoryForm'}"/>
    </q-page-sticky>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
export default {
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
    ...mapActions('category',['getCategoriesWithChilds', 'categoryDelete']),
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
    if(!this.categories.data.length) this.getCategoriesWithChilds()
  }
}
</script>
