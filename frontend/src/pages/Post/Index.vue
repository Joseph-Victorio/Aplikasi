<template>
  <q-page>
     <q-header :class="getHeaderColorBrand">
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Post
        </q-toolbar-title>
        <q-btn color="white" text-color="grey-8" size="13px" class="gt-xs" no-caps icon="eva-plus-circle" :to="{name: 'PostCreate'}" label="Tambah Post"/>
      </q-toolbar>
    </q-header>
     <div class="q-pb-xl">
      <q-list separator>
        <q-item class="item-header">
          <q-item-section avatar>#</q-item-section>
          <q-item-section>Title</q-item-section>
          <q-item-section side>Action</q-item-section>
        </q-item>
       <q-item v-for="post in posts.data" :key="post.id">

         <q-item-section avatar>
           <q-img v-if="post.image" :src="post.image_url" class="bg-white img-thumbnail img-avatar" ratio="1"/>
        </q-item-section>

        <q-item-section top> 
          <q-item-label>{{ post.title }}</q-item-label>
          <div class="q-mt-xs">
            <q-chip dense v-if="post.is_listing" label="Listing" size="sm" color="accent" text-color="white" icon="eva-checkmark-circle-2"></q-chip>
            <q-chip dense v-if="post.is_promote" label="Promote" size="sm" color="teal" text-color="white" icon="eva-checkmark-circle-2"></q-chip>
          </div>
          <div class="q-mt-sm text-xs" v-if="post.category">
           <q-chip dense size="sm">
            <q-avatar icon="sell" color="primary" text-color="white"/>
           {{ post.category }}
          </q-chip>
          </div>
        </q-item-section>

        <q-item-section side top>
          <div class="text-grey-8 q-gutter-x-sm">
            <q-btn @click="remove(post.id)" size="sm" round icon="eva-trash-2" color="red"/>
            <q-btn :to="{ name: 'PostEdit', params: {id: post.id }}" size="sm" round color="blue" icon="eva-edit-2" />
            <q-btn :to="{ name: 'FrontPostShow', params: { slug: post.slug }, query: { _rdr: $route.path }}" size="sm" round color="teal" icon="eva-external-link-outline" />
          </div>
        </q-item-section>
      </q-item>
    </q-list>
    <div class="text-center q-py-md" v-if="!posts.available">Tidak ada data</div>
    </div>

    <q-inner-loading :showing="loading"></q-inner-loading>
    
    <q-page-sticky class="lt-sm" position="bottom-right" :offset="[12, 12]">
      <q-btn fab icon="add" color="primary" :to="{name: 'PostCreate'}" glossy/>
    </q-page-sticky>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
export default {
  name: 'PostAdminIndex',
  data () {
    return {
      isSelect: '',
    }
  },
  computed: {
    ...mapState({
      posts: state => state.post.posts,
      loading: state => state.loading
    }),
  },
  methods: {
    ...mapActions('post', ['getAllPost', 'deletePost']),
    handleSelectFilter(str) {
      this.isSelect = str
      this.$store.commit('post/FILTERBY', str)
    },
    remove(id) {
      this.$q.dialog({
        title: 'Konfirmasi Penghapusan Item',
        message: 'Yakin akan menghapus data?',
        ok: {label: 'Hapus', flat: true, 'no-caps': true},
        cancel: {label: 'Batal', flat: true, 'no-caps': true},
      }).onOk(() => {
        this.deletePost(id)
      })
    },
  },
  created() {
    if(!this.posts.data.length) this.getAllPost()
  }
}
</script>
