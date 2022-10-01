<template>
  <div v-if="config && config.is_demo_mode" >
    <div class="fixed-right-middle z-50" v-if="!OpenThemeSetting">
      <div class="theme-setting bg-white" @click="OpenThemeSetting = !OpenThemeSetting">
        <q-spinner-gears
          color="primary"
          size="2em"
        />
      </div>
    </div>
    <q-dialog v-model="OpenThemeSetting" position="right" square v-if="config">
      <q-card class="text-sm">
        <q-linear-progress :value="1" color="primary" />
        <q-card-section >
          <div class="q-pb-sm row items-center justify-between">
            <div class="text-weight-bold text-md text-primary">Setting</div>
            <q-btn flat padding="xs" v-close-popup no-caps icon="eva-close" round color="primary"></q-btn>
          </div>
          <div class="q-gutter-y-md">
            <div>
              <div class="text-md text-weight-medium q-mb-xs">Product Card</div>
              <q-list dense>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Beranda
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side v-if="config">
                    <q-btn-group unelevated rounded>
                      <q-btn @click="changeHomeViewMode('grid')"
                      size="sm" 
                      unelevated 
                      color="primary" 
                      :outline="config.home_view_mode == 'list'" 
                      icon="eva-grid">
                        <q-tooltip>
                          Grid Mode
                        </q-tooltip>
                      </q-btn>
                      <q-btn @click="changeHomeViewMode('list')" 
                      size="sm" 
                      unelevated 
                      color="primary" 
                      :outline="config.home_view_mode == 'grid'" 
                      icon="eva-list">
                        <q-tooltip>
                          List Mode
                        </q-tooltip>
                      </q-btn>
                    </q-btn-group>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Katalog
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn-group unelevated rounded>
                      <q-btn 
                      @click="changeProductViewMode('grid')" 
                      size="sm" 
                      unelevated 
                      color="primary" 
                      :outline="config.product_view_mode == 'list'" 
                      icon="eva-grid">
                        <q-tooltip>
                          Grid Mode
                        </q-tooltip>
                      </q-btn>
                      <q-btn @click="changeProductViewMode('list')" 
                      size="sm" 
                      unelevated 
                      color="primary" 
                      :outline="config.product_view_mode == 'grid'" 
                      icon="eva-list">
                        <q-tooltip>
                          List Mode
                        </q-tooltip>
                      </q-btn>
                     </q-btn-group>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <div>
              <div class="text-md text-weight-medium">Themes</div>
              <q-list dense>
                <q-item>
                  <q-item-section>
                    <q-item-label>Tema</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-select outlined dense v-model="theme" :options="themes"></q-select>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label>Warna</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <input ref="color" type="color" v-model="theme_color" style="width:110px;height:20px;"/>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <div>
              <div class="text-md text-weight-medium">Checkout Mode</div>
              <q-list dense>
                  <q-item dense>
                    <q-item-section>
                      <q-item-label>Whatsapp Checkout</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-toggle v-model="is_whatsapp_checkout" color="green-6"></q-toggle>
                    </q-item-section>
                  </q-item>
                  <q-item dense>
                    <q-item-section>
                      <q-item-label>Guest Checkout</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-toggle v-model="is_guest_checkout" color="green-6"></q-toggle>
                    </q-item-section>
                  </q-item>
                </q-list>
            </div>
          </div>
        </q-card-section>
        <div class="column q-px-md q-pb-md text-left q-gutter-y-sm">
          <q-btn unelevated @click="buyNow" no-caps label="Minat? Chat disini" color="primary" class="full-width"></q-btn>
          <!-- <q-btn outline v-close-popup no-caps label="close" color="primary" class="full-width"></q-btn> -->
        </div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  data() {
    return {
      OpenThemeSetting: false,
    }
  },
  mounted() {
    this.$store.commit('LOAD_DEMO_CONFIG')
  },
  computed: {
    ...mapState({
      config: state => state.config,
      shop: state => state.shop
    }),
    themes() {
      return this.$store.state.themes
    },
    theme_color: {
      get() {
        return localStorage.getItem('__clr') ? localStorage.getItem('__clr') : this.config.theme_color
      },
      set(val) {
          this.$store.commit('SET_THEME_COLOR', val)
      }
    },
    theme: {
      get() {
        return this.config.theme
      },
      set(val) {
        localStorage.setItem('__theme', val)
        this.$store.commit('SET_THEME', val)
      }
    },
    is_whatsapp_checkout: {
      get() {
        return this.config.is_whatsapp_checkout
      },
      set(val) {
        localStorage.setItem('__wck', val)
        this.$store.commit('SET_WHATSAPP_CHECKOUT', val)
      }
    },
    is_guest_checkout: {
      get() {
        return this.config.is_guest_checkout
      },
      set(val) {
        localStorage.setItem('__guest', val)
         this.$store.commit('SET_GUEST_CHECKOUT', val)
      }
    }
  },
  methods: {
    changeHomeViewMode(str) {
      this.$store.commit('SET_HOME_VIEW_MODE', str)
    },
    changeProductViewMode(str) {
      this.$store.commit('SET_PRODUCT_VIEW_MODE', str)
    },

    buyNow() {
      let url = `https://api.whatsapp.com/send?phone=${this.config.demo_phone}&text=${location.href}`

      window.open(url, '_blank')
    }
  }
}
</script>
