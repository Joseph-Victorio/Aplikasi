<template>
  <q-card bordered class="card-lg">
    <div class="card-heading text-md">Tambah Produk Varian</div>
    <form @submit.prevent="addVarianProduk">
      <q-card-section> 
        <div>
          <q-input required label="Varian Label" v-model="form.tempVarian.label" placeholder="contoh: Ukuran"></q-input>
          <q-select
            class="q-mt-sm"
            v-model="form.tempVarian.value"
            use-input
            use-chips
            multiple
            hide-dropdown-icon
            new-value-mode="add-unique"
            label="Varian Item (multiple)"
            :error="varianError"
            hint="Ketik item kemudian enter untuk menambahkan"
          >
          <template v-slot:error>Item tidak boleh kosong</template>
          </q-select>
          
        </div>

        <q-toggle class="q-mt-lg" v-if="canToggleSubvarian" v-model="subvarian_toggle" label="Dengan Subvarian?"></q-toggle>
        
        <div class="q-mt-lg" v-if="mustHaveSubvarian"> 
          <!-- <div class="text-md">Subvarian</div> -->
          <q-input required label="Subvarian Label" v-model="form.tempSubvarian.label" placeholder="contoh: Warna"></q-input>
          <q-select
            class="q-mt-sm"
            v-model="form.tempSubvarian.value"
            use-input
            use-chips
            multiple
              hide-dropdown-icon
            new-value-mode="add-unique"
            label="Subvarian Item (multiple)"
            :error="subvarianError"
            hint="Ketik item kemudian enter untuk menambahkan"
          >
          <template v-slot:error>Item tidak boleh kosong</template>
          </q-select>
          
        </div>
        <div class="flex q-mt-md q-gutter-sm">
          <q-btn unelevated label="Tambah" type="submit" color="primary"></q-btn>
          <q-btn label="Tutup" v-close-popup outline color="primary"></q-btn>
        </div>
      </q-card-section>
    </form>
  </q-card>
</template>

<script>
export default {
  props: ['has_subvarian', 'mustHaveSubvarian', 'canToggleSubvarian'],
  data() {
    return {
      varianError: false,
      subvarianError: false,
      form: {
        tempVarian: {
          label: '',
          value: [],
        },
        tempSubvarian: {
          label: '',
          value: [],
        },
      }
    }
  },
   watch: {
    'tempVarian.value': function(val) {
      if(val.length) {
        this.varianError = false
      }
    },
    'tempSubvarian.value': function(val) {
      if(val.length) {
        this.subvarianError = false
      }
    }
  },
  computed: {
    subvarian_toggle: {
      get() {
        return this.has_subvarian
      },
      set(val) {
        this.$emit('changeSubvarian', val)
      }
    }
  },
  mounted() {
    this.form.tempVarian.value = []
    this.form.tempSubvarian.value = []
    this.varianError = false
    this.subvarianError = false
  },
  methods: {
    addVarianProduk() {

       if(!this.form.tempVarian.value.length) {
        this.varianError = true
        return
      }
      if(this.subvarian_toggle && !this.form.tempSubvarian.value.length) {
        this.subvarianError = true
        return
      }

      this.$emit('addVarian', this.form)
    }
  }
}
</script>
