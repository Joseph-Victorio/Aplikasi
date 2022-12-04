<template>
  <q-page :class="{'flex flex-center' : !reviews.length}">
   <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title>
         Moderasi Ulasan
        </q-toolbar-title>
      </q-toolbar>
      <q-tabs 
      v-model="tab"
      active-color="accent"
      class="bg-grey-1 text-grey-8"
      >
        <q-tab name="Unapproved" label="Belum Di Approve"></q-tab>
        <q-tab name="Approved" label="SUdah Di Approve"></q-tab>
      </q-tabs>
    </q-header>
    <div class="q-py-md">
      <q-list separator>
        <q-item v-for="(item, index) in reviews" :key="index">
          <q-item-section side top class="q-mt-md">{{ index+1}}</q-item-section>
          <q-item-section>
            <q-list dense>
              <q-item>
                <q-item-section avatar>Rating</q-item-section>
                <q-item-section>
                  <q-rating 
                    readonly
                    :value="item.rating"
                    color="accent"
                    icon="star_border"
                    icon-selected="star"
                    icon-half="star_half"
                    size="18px"
                  />
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>User</q-item-section>
                <q-item-section>{{ item.name }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>Produk</q-item-section>
                <q-item-section>{{ item.product.title }}</q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar top>Ulasan</q-item-section>
                <q-item-section top><q-item-label caption>{{ item.comment }}</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-item-section>
          <q-item-section avatar class="q-gutter-sm">
            <q-btn unelevated size="sm" round icon="delete" color="red-7" @click="handleDeleteReview(item.id)">
              <q-tooltip>Hapus</q-tooltip>
            </q-btn>
            <q-btn v-if="!item.is_approved" unelevated size="sm" round icon="check" color="blue-6" @click="handleApproveReview(item.id)">
              <q-tooltip>Approved</q-tooltip>
            </q-btn>
          </q-item-section>
        </q-item>
      </q-list>
      <div v-if="!reviews.length">
        Tidak ada ulasan
      </div>
    </div>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  name: 'ReviewIndex',
  data() {
    return {
      reviews: [],
      loading: false,
      tab: 'Unapproved',
      take: 8,
    }
  },
  mounted() {
    this.getReviews()
  },
  watch: {
    tab: function() {
      this.reviews = []
      this.getReviews()
    }
  },
  methods: {
    getReviews() {
      this.$q.loading.show()
      Api().get(`reviews/${this.tab}?take=${this.take}&skip=${this.reviews.length}`).then(response => {
        if(response.status == 200) {
          this.reviews = this.reviews.length ?  [...this.reviews, ...response.data.results] : response.data.results
        }
      })
      .finally(() => {
        this.$q.loading.hide()
      })
    },
    handleDeleteReview(id) {
      this.$q.dialog({
        title: 'Konfirmasi Penghapusan',
        message: 'Yakin akan menghapus ulasan ini?',
        cancel: true,
      }).onOk(() => {
        this.deleteReview(id)
      })
    },
    handleApproveReview(id) {
      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Ingin menyetujui ulasan ini?',
        cancel: true,
      }).onOk(() => {
        this.approveReview(id)
      })
    },
    deleteReview(id) {
      this.$q.loading.show()
      Api().delete('reviews/'+ id).finally(() => {
        this.reviews = []
        this.getReviews()
      })
    },
    approveReview(id) {
      this.$q.loading.show()
      Api().put('reviews/'+ id).finally(() => {
        this.reviews = []
        this.getReviews()
      })
    }
  }
  
}
</script>
