<template>
    <div class="flex justify-evenly overflow-hidden">
        <div class="text-center column items-center" v-for="a in skeletonLength" :key="a">
            <div class="full-width">
                <q-skeleton :height="theWidth" :width="theWidth" />
            </div>
            <q-skeleton type="text" width="70%" class="q-mt-sm"/>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        page_width() {
            return this.$store.state.page_width
        },
        skeletonLength() {

            if(this.page_width >= 768) return 5
            if(this.page_width >= 400) return 4
            if(this.page_width >= 250) return 3

            return 2
        },
        theWidth() {
            if(this.page_width >= 768) return `${768 / 5.8}px`
            if(this.page_width >= 400) return `${this.page_width / 5.2}px`
            if(this.page_width >= 250) return `${this.page_width / 3.7}px`
            return `${this.page_width / 4}px`
        }
    }
}
</script>