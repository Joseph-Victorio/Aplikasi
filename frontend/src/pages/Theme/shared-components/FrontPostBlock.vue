<template>
  <BlockObserver @onObserve="handleObserve">
  <div id="post" class="block-container" v-if="available">
    <div class="block-heading">
      <div class="block-title auto-padding-side"><h2>Post Terbaru</h2></div>
    </div>
    <div class="block-content">
      <post-list v-for="(post, index) in posts" :key="index" v-bind="post" />
    </div>
    <div class="block-footer flex justify-center auto-padding" v-if="!is_loading">
      <q-btn label="Selengkapnya" no-caps icon-right="eva-arrow-forward-outline" color="primary" :to="{name: 'FrontPostIndex'}"></q-btn>
    </div>
    <template v-if="is_loading">
      <PostListSkeleton  v-for="o in 3" :key="o"/>
      <div class="flex justify-center q-py-xl">
        <q-skeleton height="40px" width="150px"></q-skeleton>
      </div>
    </template>
  </div>
  </BlockObserver>
</template>

<script>
import BlockObserver from 'components/BlockObserver';
import PostList from 'components/PostList.vue'
import PostListSkeleton from 'components/PostListSkeleton.vue'
export default {
  components: { PostList, BlockObserver, PostListSkeleton },
  data() {
    return {
      is_loading: false,
      available: true
    }
  },
  computed: {
    posts() {
      return this.$store.state.front.posts
    }
  },
  methods: {
    handleObserve() {
      if(!this.posts.length) {
        this.available = true
        this.is_loading = true
        this.$store.dispatch('front/getPromotePost').then(res => {
          if(res.status == 200) {
            this.$store.commit('front/SET_POSTS', res.data.results)
            if(!res.data.results.length) {
              this.available = false
            }
          }
        }).finally(() => {
          this.is_loading = false
        })
      }
    }
  }
}
</script>
