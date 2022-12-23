<template>
  <q-page class="flex flex-center relative">
    <q-header class="text-primary bg-white box-shadow">
      <q-toolbar dense>
        <q-btn :to="{ name: 'Login'}"
          flat round dense
          icon="eva-arrow-back" />
       <q-toolbar-title class="text-weight-medium">Lupa Password</q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div style="max-width: 420px;width:100%;" class="q-pa-sm">
      <div class="flex justify-center">
        <img v-if="shop && shop.logo_path" :src="shop.logo" style="width:auto;height:75px;object-fit:contain;max-width:150px;" />
        <img v-else src="/icon/icon-192x192.png" style="width:auto;height:75px;object-fit:contain;max-width:150px;" />
      </div>
    <div class="text-red q-pb-sm text-center" v-if="errors.email">{{ errors.email[0] }}</div>
      <q-card flat class="q-pt-sm" style="background:rgb(255 255 255 / 71%);">
        <q-card-section>
          <div class="text-grey-7 q-pa-sm" v-if="!isHasRequest">
           Lupa kata sandi? Silahkan masukan email yang terdaftar di situs ini, sistem akan mengirimkan kode token ke alamat email anda. 
            </div>
            <div class="text-grey-7 q-pa-sm" v-if="isHasRequest">
              Anda sudah membuat permintaan reset password, silahkan buka email anda.
              <router-link no-caps flat class="text-primary" :to="{name: 'ResetPassword'}">Klik disini untuk memasukan kode token</router-link>
            </div>
          <form @submit.prevent="submit" class="q-gutter-y-md q-pa-sm">
             
          <q-input 
            v-model="form.email" 
            label="Email / Ponsel"
            color="grey-6"
            dense
            :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
            >
            <template v-slot:prepend>
              <q-icon name="eva-email-outline" />
            </template>
          </q-input>

          <div class="column">
            <q-btn :loading="isLoading" 
            type="submit" color="primary" padding="sm lg"
            >Kirim</q-btn>
          </div>
          </form>
          <div class="text-center q-mt-sm"> 
            <q-btn :disable="isLoading" no-caps flat color="primary" :to="{name: 'Login'}">Kembali ke halaman login</q-btn>
          </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import { Notify } from 'quasar'
export default {
  name: 'pageForgotPassword',
  data () {
    return {
      isPwd: true,
      teks: '',
      newTeks: '',
      form: {
        email: '',
        token: '',
        password: '',
        passwod_confirmation: ''
      }
    }
  },
  computed: {
    errors() {
      return this.$store.state.errors
    },
    isLoading() {
      return this.$store.state.loading
    },
    isHasRequest() {
      return localStorage.getItem('is_reqpwd') ? true : false
    },
    shop() {
      return this.$store.state.shop
    }
  },
  methods: {
    ...mapActions('user', ['requestPasswordToken']),
    submit() {
      this.$store.commit('SET_LOADING', true)
      this.requestPasswordToken(this.form).then(response => {
        if(response.status == 200) {
          if(response.data.success) {
            localStorage.setItem('is_reqpwd', true)
            this.form.email = ''
            this.$store.commit('SET_FORGOT_PASSWORD', { key: 'hide_email', value: response.data.email })
            Notify.create({
              type: 'positive',
              message: response.data.message
            })
            this.$router.push({name: 'ResetPassword'})
          } else {
            this.$q.notify({
              type: 'negative',
              message: response.data.message
            })
          }
        }
      })
      .finally(() => {
        this.$store.commit('SET_LOADING', false)
      })
    }
  }
}
</script>
