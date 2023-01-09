<template>
  <div>
    <q-card flat>
      <q-card-section>
        <div class="flex items-center justify-between">
          <div class="text-subtitle1 text-weight-bold">Telegram Notifikasi</div>
          <div class="q-px-sm rounded-borders text-white" :class="config && config.is_telegram_ready? 'bg-teal' : 'bg-grey-6'">{{ config && config.is_telegram_ready? 'Active' : 'Disabled' }}</div>
        </div>
        <div class="text-caption text-grey-7">Notifikasi order untuk admin via telegram</div>
        <div class="text-caption text-grey-7">Silahkan buat bot di telegram untuk mendapatkan token, serta dapatkan user id di bot @infouserid</div>
      <form @submit.prevent="updateData">
        <div class="q-gutter-y-sm q-mt-md">
          <q-input
          filled
          v-model="form.telegram_bot_token"
          label="Telegram Bot Token"
          />
          <q-input
          filled
          v-model="form.telegram_user_id"
          label="Telegram user Id"
          placeholder="eg: 1486912253"
          />
        </div>
        <div class="flex justify-end q-mt-md q-gutter-sm">
          <q-btn :disable="!config.is_telegram_ready" :loading="isLoading" outline size="12px" type="button" label="Test Telegram" color="primary" @click="sendTelegram"></q-btn>
          <q-btn unelevated size="12px" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
        </div>
      </form>
    </q-card-section>
    </q-card>
    <q-card flat class="q-mt-lg">
      <q-card-section>
        <div class="flex items-center justify-between">
          <div class="text-subtitle1 text-weight-bold">Konfigurasi SMTP Email</div>
          <q-toggle
            left-label
            color="teal"
            :label="formEmail.is_active? 'Active' : 'Disabled'"
            v-model="formEmail.is_active">
          </q-toggle>
        </div>
        <div class="text-caption text-grey-7">Konfigurasi pengiriman notifikasi via email</div>
      <form @submit.prevent="updateMailConfig">
         <div class="q-gutter-y-sm q-mt-md">
            <q-input filled
            v-model="formEmail.smtp_host"
            label="SMTP Host"
            placeholder="eg: smtp.google.com"
            />
            <q-input filled
            v-model="formEmail.smtp_port"
            label="SMTP Port"
            placeholder="eg: 587"
            />
            <q-input filled
            v-model="formEmail.smtp_encryption"
            label="SMTP Encryption"
            placeholder="eg: tls"
            />
            <q-input filled
            v-model="formEmail.smtp_username"
            label="SMTP Username"
            placeholder="eg: youremail@yourdomain.com"
            />
            <q-input filled
            v-model="formEmail.smtp_password"
            label="SMTP Password"
            />
            <q-input filled
            v-model="formEmail.from_address"
            label="SMTP Mail Sender"
            placeholder="eg: youremail@yourdomain.com"
            type="email"
            />
            <q-input filled
            v-model="formEmail.from_name"
            label="Sender Name"
            placeholder="My Application"
            />
            <q-input filled
            v-model="formEmail.mail_admin"
            label="Admin Email"
            placeholder="Email Admin"
            hint="Email admin, semua notifikasi admin akan dikirim ke alamat email ini"
            type="email"
            :error="isErrorEmail"
            >
            <template v-slot:error>
             email from address & mail admin tidak boleh sama
            </template>
            </q-input>
          </div>
        <div class="flex justify-end q-pt-lg q-gutter-sm">
          <q-btn :disable="mailConfig && !mailConfig.is_ready" :loading="isLoading" outline size="12px" type="button" label="Test Email" color="primary" @click="sendEmail"></q-btn>
          <q-btn unelevated size="12px" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
        </div>
      </form>
    </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data() {
    return {
      form: {
        telegram_bot_token: '',
        telegram_user_id: ''
      },
      isLoading: false,
      mailConfig: null,
      formEmail: {
        smtp_host: '',
        smtp_port: '',
        smtp_username: '',
        smtp_password: '',
        smtp_encryption: '',
        from_address: '',
        from_name: '',
        mail_admin: '',
        is_active: false
      }
    }
  },
  watch: {
    'formEmail.is_active': function(val, old) {
      if(val) {
        for(let key in this.formEmail) {
          if(this.formEmail[key] == '' || this.formEmail[key] == null) {
            this.formEmail.is_active = false
            this.$q.notify({
              type: 'negative',
              message: 'Lengkapi semua form untuk mengaktifkan email'
            })
            return
          }
        }
      }
    }
  },
  computed: {
    config: function() {
      return this.$store.state.config
    },
    isErrorEmail() {
      if(this.formEmail.from_address && this.formEmail.mail_admin) {
        if(this.formEmail.from_address == this.formEmail.mail_admin) {
          return true
        }
      }
      return false;
    }
  },
  mounted() {
    this.setData()
    this.getMailConfig()
  },
  methods: {
    sendTelegram() {
      this.isLoading = true
      Api().get('testingTelegram')
      .then(res => {
        if(res.status == 200) {
          this.$q.notify({
            type: res.data.results.type,
            message: res.data.results.message
          })
        }
      }).finally(() => this.isLoading = false)
    },
    sendEmail() {
      this.isLoading = true
      Api().get('testingEmail')
      .then(res => {
        if(res.status == 200) {
          this.$q.notify({
            type: res.data.results.type,
            message: res.data.results.message
          })
        }
      }).finally(() => this.isLoading = false)
    },
    updateData() {
      Api().post('config',  this.form).then(() => {
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })
        this.$store.dispatch('getAdminConfig')
      }).catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Gagal memperbarui data'
        })  
      })
    },
    setData() {
      if(this.config) {
        this.form.telegram_bot_token = this.config.telegram_bot_token
        this.form.telegram_user_id = this.config.telegram_user_id
      }
    },
    getMailConfig(){
      Api().get('mailConfig').then(res => {
        if(res.status == 200 && res.data.success) {
          this.setEmailConfig(res.data.results)
        }
      })
    },
    setEmailConfig(data) {
      this.mailConfig = data
      this.formEmail.smtp_host = data.smtp_host
      this.formEmail.smtp_port = data.smtp_port
      this.formEmail.smtp_username = data.smtp_username
      this.formEmail.smtp_password = data.smtp_password
      this.formEmail.smtp_encryption = data.smtp_encryption
      this.formEmail.from_address = data.from_address
      this.formEmail.from_name = data.from_name
      this.formEmail.mail_admin = data.mail_admin
      this.formEmail.is_active = data.is_active
    },
    updateMailConfig() {
      for(let x in this.formEmail) {
        if(this.formEmail[x] == null || this.formEmail[x] == '') {
          this.formEmail.is_active = false
        }
      }
      Api().post('mailConfig', this.formEmail).then(res => {
        if(res.status == 200) {
          this.setEmailConfig(res.data.results)
          this.$store.dispatch('getAdminConfig')
           this.$q.notify({
            type: 'positive',
            message: 'Berhasil memperbarui data'
          })
        }
      })
    }
  }
}
</script>
