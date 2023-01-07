<template>
  <div ref="block" class="block-observe hidden">
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: ['BlockObserver'],
  mounted() {
    this.$nextTick(() => {
      setTimeout(() => {
        this.intersecObserve()
      }, 500)
    })
  },
  methods: {
    intersecObserve() {
      let el = this.$refs.block

      el.classList.remove('hidden')

      let opts = {
        rootMargin: '0px',
        threshold: 0,
      }

      let clb = (entries) => {

      entries.forEach(entry => {

        if(!entry.isIntersecting) {

          return
          
        } else {

          this.$emit('onObserve', true)

          observer.unobserve(entry.target)

        }
      });
    };

    let observer = new IntersectionObserver(clb, opts);
      observer.observe(el)
    },

  }
}
</script>
