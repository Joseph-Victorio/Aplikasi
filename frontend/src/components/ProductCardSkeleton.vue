<template>
  <div class="q-pa-xs q-mb-md" :class="pageWidth >= 800 ? 'col-4' : 'col-6'">
    <div class="column full-height relative bg-white">
        <q-skeleton :height="imgHeight" square />
      <div class="q-mt-sm relative col column justify-between q-pa-sm">
        <q-skeleton type="text" width="40%"/>
        <q-skeleton type="text" width="90%"/>
        <q-skeleton type="text" width="70%"/>
        <div class="flex justify-between items-center">
          <q-skeleton type="text" width="50%"/>
          <q-skeleton type="circle" size="20px" />
        </div>
        <q-skeleton height="30px" width="100%"/>
      </div>
    </div>
  </div>
</template>
<script>

export default{
  name: 'Skeleton',
  data() {
    return {
      pageWidth: 800
    }
  },
  computed: {
    imgHeight() {
      if(this.$q.screen.width < 480) {
        return this.$q.screen.width/2.8 + 'px'
      }
      return '200px'
    }
  },
  methods: {
    pageResize() {
      this.pageWidth = window.innerWidth
    }
  },
  created() {
    this.pageWidth = window.innerWidth
    window.addEventListener('resize', this.pageResize)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.pageResize)
  }
}
</script>
