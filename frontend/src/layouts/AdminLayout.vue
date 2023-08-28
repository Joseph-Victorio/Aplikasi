<template>
  <q-layout view="hHh lpR fFf" class="bg-white">
    <q-page-container>
      <router-view />
    </q-page-container>
    <!-- <q-footer class="text-center text-xs text-grey-7 bg-white q-pa-md">&copy; Copyright 2021 {{ shop? shop.name : '' }} Allrights Reserved</q-footer> -->
  </q-layout>
</template>
<script>
import { mapState } from 'vuex'
export default {
  name: 'AdminLayout',
  computed: {
    ...mapState({
      shop: state => state.shop,
    })
  },
  created() {
    if (!this.shop) {

      this.$store.dispatch('getShop')
    }
    if (!this.currentUser) {

      this.$store.dispatch('user/getUser')
    }
  },
  mounted() {
    setTimeout(() => {
      this.$store.dispatch('getAdminConfig')
    }, 500)
  }
}
</script>
