<template>
  <div class="carousel-item" 
    :style="`width: ${width}px`"
    >
    {{ width }}
      <!-- <slot :width="width"> {{ width }} </slot> -->
    </div>
</template>
<script>
export default{
  props: ['width']
}
</script>

