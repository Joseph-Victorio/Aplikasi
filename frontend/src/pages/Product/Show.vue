<template>
  <q-page class="relative" :class="{'flex flex-center' : !ready }">
    <template v-if="ready && product">
    <div class="q-pa-md header-top">
      <div class="flex justify-between">
        <q-btn 
        @click="backButton"
        flat icon="eva-arrow-back" icon-size="27px" style="cursor:pointer;opacity:.9;">
        </q-btn>
        <shopping-cart />
      </div>
    </div>
    <div class="col relative overflow-x-hidden">
      <q-carousel
        ref="carousel"
        :style="cStyle"
        class="img-carousel"
        animated
        swipeable
        v-model="slide"
        navigation
        infinite
        :height="height"
        style="max-height:574px;"
      >
        <q-carousel-slide v-for="(img, index) in product.assets" :key="index" :name="index+1" :img-src="img.src" ratio="1" />

        <template v-slot:control>
          <q-carousel-control
            position="bottom-right"
            :offset="[18, 40]"
          >
            <q-btn
               dense color="white" text-color="dark" icon="eva-maximize-outline"
               unelevated size="18px" round
              @click="fullscreen = !fullscreen"
            />
          </q-carousel-control>
        </template>
      </q-carousel>
      <q-card class="product-detail relative box-shadow">
        <q-card-section class="q-pt-xs q-pb-lg">
          
          <h1 class="text-h6 text-weight-semibold q-mb-md" v-if="product">{{ product.title }}</h1>
          <div class="row items-center justify-between">
            <div class="flex items-center">
              <div class="flex items-center text-secondary">
                  <span class="text-md">Rp</span>
                <span class="text-lg text-weight-medium">
                {{ $money(parseInt(getCurrentPrice * this.quantity)) }} 
                </span>
              </div>
              <div class="flex items-center text-strike text-xs q-ml-xs" v-if="renderDiscount">
                <span class="text-sm">Rp</span>
                <span class="text-md">{{ $money(getDefaultPrice * this.quantity) }} </span>
              </div>
              <template v-if="renderMaxPrice">
              <div class="q-px-sm text-md text-weight-bold">-</div>
              <div class="flex items-center text-secondary">
                <span class="text-md">Rp</span>
                <span class="text-lg text-weight-medium">{{ $money(getMaxPrice) }} </span>
              </div>
              </template>
              <div>
            </div>
            </div>
            <div class="row q-gutter-md text-h6 items-center">
              <q-btn flat round icon="eva-minus-circle-outline" size="24" @click="decrementQty" style="cursor:pointer;"></q-btn>
              <div>{{ quantity }}</div>
              <q-btn flat round icon="eva-plus-circle-outline" size="24" @click="incrementQty" style="cursor:pointer;"></q-btn>
            </div>
          </div>

          <div class="row items-center q-gutter-x-sm">
            <q-rating 
              v-model="productRating"
              readonly
              color="accent"
              icon="ion-star-outline"
              icon-selected="ion-star"
              icon-half="ion-star-half"
              size="1.3rem" 
            />
            <div class="text-weight-medium text-sm"> {{ product.reviews_count > 0 ? product.reviews_count +' ulasan' : ''}}</div>
          </div>
        </q-card-section>
      </q-card>

     <div class="box-shadow bg-white q-px-md q-mt-md q-py-lg" v-if="product.varians.length">
        <div class="q-pb-sm">
          <div class="text-md">Pilih Varian <span class="text-sm text-weight-normal text-grey-7"></span></div>
          <div class="q-mt-md">
          <div class="q-mb-xs">{{ product.varians[0].label}}</div>
          <div class="q-gutter-sm">
            <q-btn class="product-varian--btn" outline v-for="item in product.varians" :key="item.id" :label="item.value" :color="varianSelected && varianSelected.id == item.id? 'accent' : 'grey-9'" @click="selectProductVarian(item)">
            <badge-tick v-if="varianSelected && varianSelected.id == item.id " />
            </q-btn>
          </div>
          </div>
            <div class="q-mt-md" v-if="varianSelected && varianSelected.has_subvarian">
              <div class="q-mb-xs">{{ varianSelected.subvarian[0].label }}</div>
              <div class="q-gutter-sm">
                <q-btn outline v-for="item in varianSelected.subvarian" 
                :key="item.id" 
                :label="item.value" 
                :disable="item.stock < 1 && !item.is_preorder"
                @click="selectProductSubvarian(item)" 
                :color="subvarianSelected && subvarianSelected.id == item.id ? 'accent' : 'grey-9'" 
                class="relative product-variation product-varian--btn"
                >
                  <badge-tick v-if="subvarianSelected && subvarianSelected.id == item.id " />
                </q-btn>
              </div>
          </div>
        </div>
      </div>
     
      <q-card class="box-shadow q-mt-md bg-white q-pb-xl">
        <q-tabs 
        v-model="tab"
        active-color="accent"
        align="left"
        >
          <q-tab name="Description" label="Deskripsi Produk"></q-tab>
          <q-tab name="Review" label="Ulasan Produk"></q-tab>
        </q-tabs>
         <q-separator />
        <q-tab-panels v-model="tab">
          <q-tab-panel name="Description">
            <div id="description" class="q-mt-md" style="min-height:180px;">
              <div>
                <div class="" v-html="product.description"></div>
              </div>
              <div id="product-images" class="q-mt-lg">
                <div class="product-image--container">
                  <div v-for="img in product.images" :key="img.id" class="product-image">
                    <div class="product-image--content">
                      <img class="product-image--img" :src="img.src" :alt="img.caption" />
                      <div class="product-image--caption">{{ img.caption }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </q-tab-panel>
          <q-tab-panel name="Review">
            <div id="ulasan" class="q-mt-lg">
              <div class="flex column justify-center items-center">
                <div class="text-center" v-if="productRating > 0">
                  <div class="text-3xl text-weight-bold">{{ product.rating }}</div>
                  <q-rating 
                    readonly
                    v-model="productRating"
                    color="accent"
                    icon="ion-star-outline"
                    icon-selected="ion-star"
                    icon-half="ion-star-half"
                    size="30px"
                  />
                </div>
                <div class="text-weight-medium text-md q-my-sm">
                {{ product.reviews_count > 0 ? 'Total ' + product.reviews_count +' ulasan' : 'Belum ada ulasan'}}
                </div>
                <q-btn outline color="accent" @click="handleReviewModal" label="Berikan ulasan" class="q-my-xs"></q-btn>
              </div>
              <div class="q-pt-xl">
              <q-list separator>
                <q-item class="q-py-md" v-for="(review, index) in AllProductReviews" :key="index">
                  <q-item-section avatar top>
                    <q-avatar icon="eva-person-outline" color="grey-2" text-color="grey-5"></q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-md q-mb-xs">{{ review.name }} <span v-if="!review.is_approved" class="text-xs text-orange">( Menunggu Moderasi )</span></q-item-label>
                      <q-rating 
                      readonly
                      v-model="review.rating"
                      color="accent"
                      icon="ion-star-outline"
                      icon-selected="ion-star"
                      icon-half="ion-star-half"
                      size="1.1rem"
                    />
                    <div class="text-xs text-grey-6 q-my-xs">{{ review.created }}</div>
                    <div class="q-pa-xs text-sm text-grey-8"> {{ review.comment }} </div>
                  </q-item-section>
                </q-item>
              </q-list>
              </div>
            <div class="q-my-md row justify-center">
              <q-btn outline no-caps color="primary" :loading="loadMoreLoading" v-if="productReviews.length < product.reviews_count" label="loadmore..." @click="loadReview">
                <template v-slot:loading>
                  <q-spinner-facebook />
                </template>
              </q-btn>
            </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </div>
    <q-footer class="q-gutter-x-sm flex q-pa-md bg-white">
      <q-btn unelevated rounded outline @click="chatModal = true" icon="eva-message-circle-outline" label="Chat" color="primary" class="col"></q-btn>
      <q-btn unelevated rounded @click="addNewItem" icon="eva-shopping-cart-outline" label="Beli" color="primary" class="col"></q-btn>
    </q-footer>
    </template>
    <q-inner-loading :showing="!ready">
      <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <q-dialog v-model="reviewModal" persistent>
      <div class="q-card" style="width:100%;max-width:360px;">
        <q-card-section>
        <form @submit.prevent="submitReview">
          <div>
          <div class="text-subtitle2 q-mb-sm">Berikan Ulasan Anda</div>
            <q-rating 
              v-model="form.rating"
              color="amber"
              icon="ion-star-outline"
              icon-selected="ion-star"
              icon-half="ion-star-half"
              size="sm" 
            />
          <div class="q-my-md q-gutter-y-xs">
            <q-input 
            dense 
            label="Nama Anda" 
            v-model="form.name"
            :rules="[val => val && val != '' || 'Wajib disisi']"
            ></q-input>
            <q-input 
            dense 
            label="Ulasan Anda" 
            type="textarea" 
            v-model="form.comment" 
            rows="3"
            :rules="[val => val && val != '' || 'Wajib disisi']"
            ></q-input>
          </div>
          <div class="q-gutter-y-sm q-my-md items-center text-grey">
            <div class="text-grey text-xs">Jawab tantangan berikut, hanya untuk memastikan anda bukan robot</div>
            <div class="row q-gutter-x-sm items-center">
              <div class="text-weight-bold bg-dark text-white q-px-sm q-py-xs rounded">{{ number2 }} + {{ number1 }} </div> 
              <div class="text-weight-bold"> = </div> 
              <input class="rounded text-grey-9" type="text" ref="jawaban" v-model="jawaban" style="width:60px;padding:3px 6px;border:1px solid grey">
            </div>
          </div>
          <div class="row justify-end q-gutter-x-sm">
            <q-btn unelevated type="button" @click.prevent="closeReviewModal" label="Batal" color="primary" outline></q-btn>
            <q-btn unelevated :disabled="chalengeTesting" type="submit" :loading="loading" label="Kirim Ulasan" color="primary"></q-btn>
          </div>
        </div>
        </form>
        </q-card-section>
      </div>
    </q-dialog>
    <q-dialog v-model="chatModal">
      <q-card style="max-width:450px;width:100%;" class="text-grey-9">
        <div class="text-weight-bold q-py-sm q-px-md bg-primary text-white">Kirim pesan / tanya ke penjual</div>
        <q-card-section class="transition-height">
          <div class="q-mb-sm text-subtitle2" v-if="this.product"># {{ product.title }}</div>
          <q-input outlined autogrow autofocus v-model="chatText" placeholder="contoh: Halo Admin, Apakah ini masih ada?"></q-input>
          <div class="q-pt-sm">
            <div class="q-pa-xs cursor-pointer" v-for="chat in defaultChat" :key="chat" @click="changeChatText(chat)">
              <span>{{ chat }}</span>
            </div>
          </div>
        </q-card-section>
        <q-card-actions class="justify-end q-pa-md">
          <q-btn @click="closeChatModal" outline color="primary" label="Batal"></q-btn>
          <q-btn @click="submitChat" :disabled="!chatText" color="primary" label="Kirim"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog 
    v-model="cartModal"
    transition-show="slide-up"
    transition-hide="slide-up"
    >
      <q-card flat class="card-lg bg-white" v-if="product">
        <q-linear-progress size="10px" :value="100" />
          <q-card-section>
          <q-list>
            <q-item class="q-px-xs">
              <q-item-section avatar top>
                <q-img :src="product.assets[0].src" width="80px" class="rounded-borders"></q-img>
              </q-item-section>
              <q-item-section top>
                <div class="text-md text-weight-meduim q-mb-sm"><span class="text-weight-medium">{{ product.title }}</span> berhasil ditambahkan di keranjang belanja.</div>
                <q-item-label caption>Anda bisa lanjut kehalaman checkout atau melanjutkan berbelanja</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
          <div class="flex justify-end q-gutter-x-sm q-pt-sm">
            <q-btn outline no-caps @click="cartModal = false" label="Berbelanja Lagi" color="primary"></q-btn>
            <q-btn unelevated no-caps :to="{ name: 'Cart' }" label="Lanjut Checkout" color="primary"></q-btn>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="alreadyItemModal">
      <q-card class="card-lg">
        <q-linear-progress size="10px" :value="100" />
        <q-card-section v-if="product">
          <div class="q-mb-sm text-weight-medium text-lg">Konfirmasi</div>
          <div><span class="text-weight-medium text-md text-capitalize">{{ product.title }} </span> {{ getVarianTextNote() }}<br>Sudah ada dikeranjang, Apakah ingin tetap menambahkan?, Jika YA, keranjang akan diperbarui kuantitasnya</div>
        </q-card-section>
        <q-card-actions class="justify-end q-gutter-x-sm q-pa-md">
          <q-btn outline no-caps @click="alreadyItemModal = false" label="Batal" color="primary"></q-btn>
          <q-btn unelevated no-caps @click="updateNewItem" label="Tambahkan" color="primary"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog 
      v-model="formVariantModal"
      position="bottom"
      transition-show="slide-up"
      transition-hide="slide-down"
      >
      <q-card class="max-width" flat v-if="product && product.varians.length">
        <q-card-section>
          <div class="text-weight-medium text-lg q-mb-sm text-secondary">{{ moneyIDR(parseInt(getCurrentPrice)) }}</div>
            <div>
              <div class="text-md">Pilih Varian <span class="text-sm text-weight-normal text-grey-7"></span></div>
              <div class="q-mt-sm">
              <div class="q-mb-xs">{{ product.varians[0].label}}</div>
              <div class="q-gutter-sm">
                <q-btn class="product-varian--btn" outline v-for="item in product.varians" :key="item.id" :label="item.value" :color="varianSelected && varianSelected.id == item.id? 'accent' : 'grey-9'" @click="selectProductVarian(item)">
                <badge-tick v-if="varianSelected && varianSelected.id == item.id " />
                </q-btn>
              </div>
              </div>
                <div class="q-mt-md" v-if="varianSelected && varianSelected.has_subvarian">
                  <div class="q-mb-xs">{{ varianSelected.subvarian[0].label }}</div>
                  <div class="q-gutter-sm">
                    <q-btn outline v-for="item in varianSelected.subvarian" 
                    :key="item.id" 
                    :label="item.value" 
                    :disable="item.stock < 1 && !item.is_preorder"
                    @click="subvarianSelected = item" 
                    :color="subvarianSelected && subvarianSelected.id == item.id ? 'accent' : 'grey-9'" 
                    class="relative product-variation product-varian--btn"
                    >
                      <badge-tick v-if="subvarianSelected && subvarianSelected.id == item.id " />
                    </q-btn>
                  </div>
              </div>
            </div>
        </q-card-section>
        <q-card-section>
        <q-btn unelevated @click="addNewItem" name="eva-shopping-cart-outline" label="Beli Sekarang" color="primary" class="full-width"></q-btn>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="fullscreen" persistent maximized>
      <div class="max-width relative bg-grey-10" v-if="product">
        <div class="text-center q-py-md absolute" style="top:5px;width:100%;z-index:99;">
          <div class="flex justify-center">
            <div class="q-px-md" style="background:rgb(240 240 240 / 90%);">Scroll mouse atau cubit layar untuk zoom</div>
          </div>
        </div>
        <Transition name="slide" tag="div" class="preview-image relative bg-grey-5 text-center">
          <PinchScrollZoom
          ref="zoomer"
          :width="zoomerWidth"
          :height="zoomerHeight"
          :scale="scale"
          >
          <img ref="zoomerimage" :src="currentImage" :style="zoomImageStyle"/>
          </PinchScrollZoom>
        </Transition>
        <div class="absolute row items-center" style="bottom: 4%; right:4%;">
          <div class="q-mr-lg" v-if="product.assets.length > 1">
            <q-btn
              :disable="slide == 1"
              dense
              size="18px"
              color="teal" round unelevated
              icon="eva-arrow-ios-back"
              @click="slide--"
              class="q-mr-sm"
            />
            <q-btn
              :disable="product.assets.length == slide"
              dense
              size="18px"
              color="teal" round unelevated
              icon="eva-arrow-ios-forward"
              @click="slide++"
            />
          </div>
            <q-btn
              dense
              size="18px"
              color="teal" round unelevated
              icon="ion-refresh"
              @click="resetZoom"
              class="q-mr-sm"
            />
            <q-btn
              dense
              size="18px"
              color="teal" round unelevated
              icon="eva-close"
             
              @click="fullscreen = !fullscreen"
            />
          </div>
      </div>
    </q-dialog>
  </q-page>
</template>

<script>
import { mapMutations, mapActions } from 'vuex'
import ShoppingCart from 'components/ShoppingCart.vue'
import BadgeTick from 'components/BadgeTick.vue'
import PinchScrollZoom, { PinchScrollZoomEmitData } from "@coddicat/vue-pinch-scroll-zoom";
export default {
  name: 'ProductShow',
  components: { ShoppingCart, BadgeTick, PinchScrollZoom },
  data () {
    return {
      scale: 1,
      tab: 'Description',
      defaultChat: ['Apakah ini masih ada?', 'Apakah bisa grosir?'],
      chatText: '',
      chatModal: false,
      reviewModal: false,
      timeInterval: null,
      number1: 0,
      number2: 0,
      jawaban: '',
      loading: false,
      slide: 1,
      quantity: 1,
      discount: 0,
      fullscreen: false,
      shop: this.$store.state.shop,
      ready: false, 
      loadMoreLoading: false,
      form: {
        product_id: null,
        name: '',
        comment: '',
        rating: 0
      },
      cartModal: false,
      alreadyItemModal: false,
      varianSelected: null,
      subvarianSelected: null,
      formVariantModal: false,
      product: null,
      productReviews: [],
      imagePreviewIndex: 0,
      unapproved_review: JSON.parse(localStorage.getItem('unapproved_review')) || null,
    }
  },
  watch: {
    tab: function(val, oldval) {
      if(val != oldval 
      && val == 'Review' 
      && !this.productReviews.length 
      && this.product.reviews_count > 0) {
         this.getReview()
      } 
    }
  },
  computed: {
    zoomImageStyle() {
      return `width:100%;height:100%;object-fit:contain;padding:4%;max-width:${this.zoomerWidth};max-height:${this.zoomerHeight}`
    },
    currentImage(){
      return this.product.assets[this.slide -1].src
    },
    zoomerHeight() {
      return window.innerHeight
    },
    zoomerWidth() {
      if(window.innerWidth > 768) {
        return 768;
      }else {
        return window.innerWidth
      }
    },
    renderMaxPrice(){
      if(this.isHasVarian && !this.isVarianHasSelected) {
        if(parseInt(this.getCurrentPrice) < parseInt(this.getMaxPrice)) {
          return true
        }
      }
      return false
    },
    renderDiscount() {
      if(this.product.pricing.is_discount) {
        if(this.isHasVarian && this.isVarianHasSelected) {
          return true
        }
      }
      return false
    },
    AllProductReviews() {
      if(this.unapproved_review) {
        return [this.unapproved_review, ...this.productReviews]
      }
      return this.productReviews
    },
    session_id() {
      return this.$store.state.session_id
    },
    currentSlide() {
      return this.slide-1
    },
    chalengeTesting() {
      return this.number1+this.number2 != this.jawaban
    },
    productRating() {
      return parseFloat(this.product.rating)
    },
    carts() {
      return this.$store.state.cart.carts
    },
    cStyle() {
      if(!this.fullscreen && this.$q.screen.width < 560 && this.$q.screen.width > 200) {
        return 'height:'+ this.$q.screen.width +'px'
      }
        return ''
    },
    height() {
      return this.$q.screen.width+'px'
    },
    productStock() {
      if(this.product.varians.length) {
        if(this.subvarianSelected) {
          return this.subvarianSelected.stock
        }
        return 0
      } 
      return this.product.stock
    },
    currentStock() {
      let hasCart = this.carts.find(el => el.sku == this.currentProductSku)

      if(this.isHasVarian) {

        if(this.varianSelected) {  
        
          if(!this.varianSelected.has_subvarian) {
            return hasCart != undefined ? this.varianSelected.stock-hasCart.quantity : this.varianSelected.stock
          }

          if(this.subvarianSelected) {
  
            return hasCart != undefined ? this.subvarianSelected.stock-hasCart.quantity : this.subvarianSelected.stock
          }

          return hasCart != undefined ? this.productStock-hasCart.quantity : this.productStock

        } else {

           if(this.subvarianSelected) {

            return hasCart != undefined ? this.subvarianSelected.stock-hasCart.quantity :  this.productStock
          }

          return hasCart != undefined ?  this.productStock-hasCart.quantity :  this.productStock
        }

      }else {

        return hasCart != undefined ?  this.productStock-hasCart.quantity :  this.productStock

      }

    },
    isHasVarian() {
      return this.product.varians.length > 0
    },
    isVarianHasSelected() {
      if(this.varianSelected) {
         if(!this.varianSelected.has_subvarian) {
          return  true
         }else if(this.subvarianSelected) {
          return true
         }
      }
      return false
    },
    currentProductSku() {
      if(this.varianSelected) {
        if(!this.varianSelected.has_subvarian) {
          return this.varianSelected.sku
        }
        if(this.subvarianSelected) {
          return this.subvarianSelected.sku
        }
      }
      return this.product.sku ? this.product.sku : this.product.id
    }, 
    getDiscountAmount() {
      if(this.product.pricing.is_discount) {
        if(this.product.pricing.discount_type == 'PERCENT') {
          return (parseInt(this.getDefaultPrice)*parseInt(this.product.pricing.discount_amount))/100
        }else {
          return parseInt(this.product.pricing.discount_amount)
        }
      }
      return 0
    },
    getMaxPrice() {
      if(this.isHasVarian) {

        let maxPrice = parseInt(this.product.pricing.max_price);

        if(this.varianSelected && this.varianSelected.has_subvarian) {

          maxPrice = parseInt(this.varianSelected.subvarian[this.varianSelected.subvarian.length -1].price)

        }

        let discount = 0;

        if(this.product.pricing.is_discount) {
          if(this.product.pricing.discount_type == 'PERCENT') {
            discount = (parseInt(maxPrice)*parseInt(this.product.pricing.discount_amount))/100
          }else {
            discount = parseInt(this.product.pricing.discount_amount)
          }
        }
        return maxPrice - discount
      }
      return 0
    },
    getDefaultPrice() {
      if(this.varianSelected) {

        if(!this.varianSelected.has_subvarian) {

          return  parseInt(this.varianSelected.price)

        } else {

          if(this.subvarianSelected) {
    
            return parseInt(this.subvarianSelected.price)
          } 
  
            return parseInt(this.varianSelected.subvarian[0].price)
        }

      }

      return parseInt(this.product.pricing.default_price)
    },
    getCurrentPrice() {
      if(this.varianSelected) {

        if(!this.varianSelected.has_subvarian) {

          return  parseInt(this.varianSelected.price) - this.getDiscountAmount

        } else {

          if(this.subvarianSelected) {
    
            return parseInt(this.subvarianSelected.price) - this.getDiscountAmount
          } 
  
            return parseInt(this.varianSelected.subvarian[0].price) - this.getDiscountAmount
        }

      }

      return parseInt(this.product.pricing.default_price) - this.getDiscountAmount
    },
    getCurrentWeight() {
      if(this.varianSelected) {

        if(!this.varianSelected.has_subvarian) {

          return  parseInt(this.varianSelected.weight)

        } else {

          if(this.subvarianSelected) {
    
            return parseInt(this.subvarianSelected.weight)
          } 
  
            return parseInt(this.varianSelected.subvarian[0].weight)
        }

      }

      return parseInt(this.product.weight)
    },

  },
  methods: {
    ...mapActions('product', ['getProductBySlug', 'loadProductReview', 'addProductReview']),
    resetZoom() {
      this.$refs.zoomer.setData({
        scale: 1,
        originX: 0,
        originY: 0,
        translateX: 0,
        translateY: 0        
      });
    },
    selectProductVarian(item) {
      this.varianSelected = item
      this.subvarianSelected = null
      this.quantity = 1
    },
    selectProductSubvarian(item) {
      this.subvarianSelected = item
      this.quantity = 1
    },
    backButton() {
      if(window.history.length > 2) {
        window.history.back()
      }else {
        this.$router.push({name: 'ProductIndex'})
      }
    },
    addToCart() {
      this.formVariantModal = false

      let cartItem = {
        session_id: this.session_id, 
        product_id: this.product.id, 
        product_stock: this.currentStock, 
        sku: this.currentProductSku, 
        name: this.product.title, 
        price: this.getCurrentPrice, 
        quantity: this.quantity, 
        note: this.getVarianTextNote(),
        product_url: this.getRoutePath(), 
        image_url: this.product.assets[0].src, 
        weight: this.getCurrentWeight
      }

      this.$store.dispatch('cart/addToCart' , cartItem)

        this.quantity = 1
    },
    showNotifyHasSelectVarian() {
      if(this.formVariantModal) {
        this.$q.notify({
          type: 'info',
          message: 'Silahkan pilih produk varian terlebih dahulu',
        })
      } else {
        this.formVariantModal = true
      }
    },
    addNewItem() {
      if(this.isHasVarian) {
        if(this.varianSelected) {

          if(this.varianSelected.has_subvarian) {

            if(!this.subvarianSelected) {

              this.showNotifyHasSelectVarian()

              return 
            }
          }

        } else {
          this.showNotifyHasSelectVarian()
          return

        }
      }

      if(this.currentStock <= 0) {
        let item = this.isHasVarian ? 'varian' : 'produk'
        this.$q.dialog({
          title: 'Stok habis',
          message: `Maaf, stok untuk ${item} ini habis, silahkan pilih ${item} lainnya.`
        })

        return
      }
      
      this.checkCart().then(res => {
        this.addToCart()
        this.cartModal = true
      }).catch(err => {
        this.alreadyItemModal = true
      })
    },
    updateNewItem() {
      this.alreadyItemModal = false
      this.addToCart()
      this.cartModal = true
    },
    checkCart() {
      return new Promise((resolve, reject) => {
        let cartAlready;

          cartAlready = this.carts.find(el => el.sku == this.currentProductSku)

          if(cartAlready != undefined) {

            reject()

          } else {

            resolve()
          }

      })
    },
    getVarianTextNote() {
       let str = ''
      if(this.varianSelected) {
        if(this.varianSelected.has_subvarian && this.subvarianSelected) {
          str += `${this.varianSelected.label} ${this.varianSelected.value}, ${this.subvarianSelected.label} ${this.subvarianSelected.value}`
        } else {
          str += `${this.varianSelected.label} ${this.varianSelected.value}`
        }
      }
      return str
    },
    getRoutePath() {
      let props = this.$router.resolve({ 
        name: 'ProductShow',
        params: { slug: this.product.slug },
      });

      return location.origin + props.href;
    },
    checkVarianIsReady() {
      if(this.isHasVarian) {
        if(!this.varianSelected) return false

        if(this.varianSelected.has_subvarian) {
          if(!this.subvarianSelected) return false
        }
      }
      return true
    },
    incrementQty() {
      if(!this.checkVarianIsReady()) {
        this.$q.dialog({
          title: 'Pilih Varian!',
          message: 'Silahkan pilih varian untuk melanjutkan.'
        })
        return
      } 

      if(this.quantity < this.currentStock) {
        this.quantity += 1
      } else {
        this.$q.dialog({
          title: 'Warning!',
          message: 'Stok tidak cukup, stok tersisa ' + this.currentStock + ' item.'
        })
      }

    },
    decrementQty() {
       if(!this.checkVarianIsReady) {
        this.$q.dialog({
          title: 'Pilih Varian!',
          message: 'Silahkan pilih varian untuk melanjutkan.'
        })
        return
      } 

      if(this.quantity > 1) {
        this.quantity -= 1
      }
    },
    getTeaser(html) {
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 80)
      } else {
        return ''
      }
    },
    closeReviewModal() {
      clearInterval(this.timeInterval)
       this.reviewModal = false
    },
    handleReviewModal() {
      this.getRandomNumber()

      this.timeInterval = setInterval(() => {
        if(document.activeElement !== this.$refs.jawaban) {
          this.getRandomNumber()
        }
      }, 10000)
      this.reviewModal = true
    },
    submitReview() {
      if(this.number1+this.number2 != this.jawaban) {
        this.$q.notify({
          type: 'negative',
          message: 'Jawaban Salah, silahkan jawab dengan benar.'
        })
        this.getRandomNumber()
        return
      }
      this.jawaban = ''
      this.getRandomNumber()
      this.form.product_id = this.product.id
      if(this.form.name && this.form.comment && this.form.rating) {
        this.loading = true
        this.reviewModal = false
        this.addProductReview(this.form).then((res) => {
          let dataReview = res.data.results
          if(res.status == 200) {
            this.$q.notify({
              type: 'positive',
              message: res.data.message
            })
          }
          if(!dataReview.is_approved) {
            localStorage.setItem('unapproved_review', JSON.stringify(dataReview))
            this.unapproved_review = dataReview

            setTimeout(() => {
              localStorage.removeItem('unapproved_review');
            }, 30000)
          }
          this.getReview()
          this.getProduct()
        })
        this.resetForm()
        this.loading = false
      } else {
        this.$q.notify({
          type: 'warning',
          message: 'Semua field harus di isi'
        })
      }
    },
    resetForm() {
      this.form.name = ''
      this.form.comment = ''
    },
    getReview() {
      this.loadMoreLoading = true
      this.loadProductReview({ product_id: this.product.id }).then(response => {
        if(response.status == 200) {
          this.loadMoreLoading = false
          this.productReviews = response.data.results
        }
      }).catch(err => {
         this.loadMoreLoading = false
      })
    },
    loadReview() {
      this.loadMoreLoading = true
      this.loadProductReview({ product_id: this.product.id, skip: this.productReviews.length }).then(response => {
        if(response.status == 200) {
          this.loadMoreLoading = false
          this.productReviews = [... this.productReviews, ...response.data.results]
        }
      }).catch(err => {
         this.loadMoreLoading = false
      })
    },
    getProduct() {
      this.getProductBySlug(this.$route.params.slug).then(response => {
        if(response.status == 200) {
          this.product = response.data.data
          this.ready = true
          
          if(this.isHasVarian) {
            if(this.product.varians[0].has_subvarian) {
              this.varianSelected = this.product.varians[0];
            }
          }
        } else {
          // this.$router.push({name: 'ProductIndex'})
        }
      }).catch(() => {
        // this.$router.push({name: 'ProductIndex'})
      })
    },
    getRandomNumber() {
      let number = [1,2,3,4,5,6,7,8,9,10,11,12]
      this.number1 = Math.floor((Math.random() * number.length));
      this.number2= Math.ceil((Math.random() * number.length));
    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    closeChatModal() {
      this.chatText = ''
      this.chatModal = false
    },
    changeChatText(chat) {
      this.chatText = chat
    },
    submitChat() {
      let shopPhone = this.shop.phone
      if(!shopPhone) {
        this.$q.dialog({
            title: 'Maaf, Sedang masalah!',
            message: 'Silahkan coba kembali beberapa saat lagi, jika masih berlanjut silahkan hubungi kami.',
          })
          return
      }

      let link = 'https://api.whatsapp.com/send?phone=' + this.formatPhoneNumber(shopPhone) + '&text=' + encodeURI(this.chatText + '\nProduk: '+ this.product.title +'\n') + location.href;
      window.open(link, '_blank');

      setTimeout(() => {
        this.closeChatModal()
      }, 2000)

    },

  },
  mounted() {
    if(!this.session_id) {
      this.makeSessionId()
    }
  },
  created() {
    if(!this.product || this.product.slug != this.$route.params.slug) {
      this.getProduct()  

    } else {
      this.ready = true
    }
    this.getRandomNumber()   
  },
  meta() {
    return {
      title: this.product?.title
    }
  }
}
</script>

<style lang="scss">
.header-top {
  z-index:50;
  position:fixed;
  top:0;
  left:0;
  right:0;
  width:100%;
  background: transparent;
  background: linear-gradient(0deg, rgba(2, 0, 36, 0) 0%, rgba(0, 0, 5, 0.678) 100%); 
  color:#fff;
}
.q-body--fullscreen-mixin .img-carousel::after {
  height: 0;
}
.product-detail::before {
position: absolute;
  width: 100%;
  background-color:white;
  content: "";
  height: 20px;
  border-radius: 80px 80px 0 0;
  top: -20px;
  left: 0;
  .q-carousel__control.absolute.absolute-bottom-right {
    transform: translateY(-20px)
  }
  .q-carousel__navigation--bottom{
    transform: translateY(-20px)
  }
}
.slide-enter-active,
.slide-leave-active {
  transition: all 0.5s ease;
}

.slide-enter-to,
.slide-leave-from,
.slide-enter-from,
.slide-leave-to {
  opacity: 0;
}
.preview-image {
  height:100%;
  width:100%;
  position:relative;
  overflow: hidden;
}
// .preview-image img {
//   transition: all ease-in-out 300ms;
// }
</style>