<template>
    <div>
        <div class="flex justify-evenly q-py-md overflow-hidden no-wrap">
            <div class="column justify-center items-center" v-for="a in skeletonLength" :key="a">
                <q-skeleton type="circle" :width="theCircle" :height="theCircle" />
                <q-skeleton type="text" width="70px" class="q-mt-sm"/>
            </div>
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

            return 3
        },
        theCircle() {
            if(this.page_width >= 768) return '90px'
            if(this.page_width >= 600) return '65px'

            return '50px'
        }
    }
}
</script>