<template>
  <q-page class="bg-white" :class="{'flex flex-center' : !ready}">
    <q-header class="text-brand bg-white box-shadow">
       <q-toolbar>
         <q-btn v-go-back.single
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">Post</q-toolbar-title>
       </q-toolbar>
    </q-header>
    <template v-if="ready">
      <template v-if="post">
        <q-img :src="post.image_url" class="bg-grey-2 box-shadow"></q-img>
      <div class="auto-padding">
        <div v-if="post.tags" class="text-weight-medium text-accent">{{ post.tags }}</div>
        <h1 class="text-lg text-weight-medium q-mb-sm">{{ post.title }}</h1>
        <div class="text-grey-6 text-caption">{{ post.created_locale }}</div>
        <p class="q-mb-lg" v-html="post.body"></p>
      </div>
      </template>
      <template v-else>
        Data tidak ditemukan
      </template>
    </template>
    <q-inner-loading :showing="!ready">
       
    </q-inner-loading>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data() {
    return {
      ready: false,
      post: null
    }
  },
  methods: {
    async getPost() {
      let { data } = await Api().get('public/post/' + this.$route.params.slug)
      this.post = data.results
      this.ready = true
    }
  },
  created() {
    if(!this.post || this.post.slug != this.$route.params.slug) {
      this.getPost()
    } else {
      this.ready = true
    }
  },
  meta() {
    return {
      title: this.post?.title ,
      meta: {
        ogTitle:  { property: 'og:title', content: this.post?.title},
        ogImage:  { property: 'og:image', content: this.post?.image_url },
      }
      
    }
  }
}
</script>
