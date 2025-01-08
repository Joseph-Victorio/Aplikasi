<template>
   <q-page padding>
      <q-header class="text-primary bg-white box-shadow">
         <q-toolbar>
            <q-btn @click="$router.back()" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">Form Pemesanan</q-toolbar-title>
         </q-toolbar>
      </q-header>

      <div v-if="can_ship">
         <WithShipping></WithShipping>
      </div>
      <div v-else>
         <WithoutShipping></WithoutShipping>
      </div>

   </q-page>
</template>

<script>
import WithShipping from './WithShipping.vue'
import WithoutShipping from './WithoutShipping.vue'
export default {
   components: { WithShipping, WithoutShipping },
   computed: {
      config() {
         return this.$store.state.config
      },
      can_ship() {
         if (this.config.can_cod || this.config.can_shipping) {
            return true
         }
         return false
      }

   },
   created() {
      this.$store.dispatch('getConfig')
   }

}
</script>
