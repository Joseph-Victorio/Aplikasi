<template>
  <q-page class="q-pb-xl">
    <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-toolbar-title>
          <q-btn :to="{ name: 'CustomerAccount' }" flat round icon="eva-arrow-back" />
          Daftar Alamat
        </q-toolbar-title>
        <q-btn color="white" text-color="grey-8" size="13px" @click="handleAddData" icon="eva-plus-circle"
          label="Alamat" />
      </q-toolbar>
    </q-header>
    <q-list separator>
      <q-item class="bg-grey-2">
        <q-item-section side>#</q-item-section>
        <q-item-section>Label</q-item-section>
        <q-item-section>Address</q-item-section>
        <q-item-section side>Action</q-item-section>
      </q-item>
      <q-item v-for="(item, index) in user.address" :key="item.id">
        <q-item-section side>
          {{ index + 1 }}
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ item.label }} <q-badge v-if="item.is_primary" class="q-ml-xs">Utama</q-badge></q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ item.address }}</q-item-label>
        </q-item-section>
        <q-item-section side>
          <div class="row q-gutter-x-sm">
            <q-btn @click="handleDeleteData(item.id)" icon="delete" color="red" round size="11px" padding="6px"></q-btn>
            <q-btn @click="handleEditData(item)" icon="edit" color="blue" round size="11px" padding="6px"></q-btn>
          </div>
        </q-item-section>
      </q-item>
      <div class="text-center q-py-md" v-if="!user.address.length">Tidak ada data</div>
    </q-list>
    <q-inner-loading :showing="loading"></q-inner-loading>
    <q-dialog v-model="formAddressModal" persistent>
      <q-card class="card-lg">
        <q-card-section v-if="user">
          <div class="card-title flex justify-between">
            <div>{{ formAddress._method == 'PUT' ? 'Edit' : 'Tambah' }} Alamat</div>
            <div class="q-gutter-x-sm">
              <q-btn icon="close" flat dense v-close-popup></q-btn>
            </div>
          </div>
          <form @submit.prevent="submitNewAddress" class="q-gutter-y-md">
            <q-input required label="Label" v-model="formAddress.label" placeholder="eg: Kantor"></q-input>
            <q-input required type="textarea" v-model="formAddress.address" label="Alamat Lengkap"></q-input>
            <q-checkbox label="Gunakan sebagai alamat utama" v-model="formAddress.is_primary"></q-checkbox>
            <div class="card-action">
              <q-btn label="Simpan Alamat" class="full-width" color="primary" type="submit"></q-btn>
            </div>
          </form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data() {
    return {
      formAddressModal: false,
      formAddress: {
        id: '',
        _method: 'POST',
        label: '',
        is_primary: false,
        address: ''
      }
    }
  },
  computed: {
    user() {
      return this.$store.state.user.user
    },
    loading() {
      return this.$store.state.loading
    }
  },
  methods: {
    getUser() {
      this.$store.dispatch('user/getUser')
    },
    submitNewAddress() {

      if (this.formAddress._method == 'PUT') {
        Api().post('user-address/' + this.formAddress.id, this.formAddress).then(() => {
          this.formAddressModal = false
          this.getUser()
        })
      } else {
        Api().post('user-address', this.formAddress).then(() => {
          this.formAddressModal = false
          this.getUser()
        })
      }

    },
    clearForm() {
      this.formAddress.id = ''
      this.formAddress.label = ''
      this.formAddress.is_primary = false
      this.formAddress.address = ''
    },
    handleAddData() {

      this.clearForm()
      this.formAddress._method = 'POST'
      this.formAddressModal = true
    },
    handleDeleteData(id) {
      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Yakin Akan menghapus data?',
        cancel: true,
      }).onOk(() => {
        this.destroyData(id)
      })
    },
    handleEditData(item) {
      this.clearForm()
      this.formAddress._method = 'PUT'
      this.formAddress.id = item.id
      this.formAddress.label = item.label
      this.formAddress.is_primary = item.is_primary
      this.formAddress.address = item.address

      this.formAddressModal = true
    },
    destroyData(id) {
      Api().delete('user-address/' + id).then(() => this.getUser())
    }
  }
}
</script>
