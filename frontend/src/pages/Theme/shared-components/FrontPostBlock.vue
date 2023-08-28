<template>
  <BlockObserver @onObserve="handleObserve">
    <div id="post" class="block-container" v-if="posts.is_available">
      <div class="block-heading">
        <div class="block-title auto-padding-side">
          <h2>Post Terbaru</h2>
        </div>
      </div>
      <template v-if="posts.is_available">
        <div class="block-content">
          <post-list v-for="(post, index) in posts.data" :key="index" v-bind="post" />
        </div>
        <div class="block-footer flex justify-center auto-padding" v-if="posts.is_done">
          <q-btn label="Selengkapnya" no-caps icon-right="eva-arrow-forward-outline" color="primary"
            :to="{ name: 'FrontPostIndex' }"></q-btn>
        </div>
      </template>
      <template v-if="!posts.is_done">
        <PostListSkeleton v-for="o in 3" :key="o" />
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
  computed: {
    posts() {
      return this.$store.state.front.posts
    },
  },
  methods: {
    handleObserve() {
      if (!this.posts.length) {
        this.$store.dispatch('front/getPromotePost').then(res => {
          if (res.status == 200) {
            this.$store.commit('front/SET_POSTS', res.data.results)
          }
        })
      }
    }
  }
}
</script>
