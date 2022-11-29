<template>
  <div>
    <q-input
    :value="pricing" 
    :filled="filled" 
    :outlined="outlined" 
    :square="square" 
    :dense="dense" 
    :label="label" 
    :prefix="prefix"
    :suffix="suffix"
    @input="updateData" 
    :stackLabel="stackLabel"
    ></q-input>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: 'Harga'
    },
    filled: Boolean,
    outlined: Boolean,
    stackLabel: Boolean,
    square: Boolean,
    dense: Boolean,
    value: [String, Number],
    prefix: String,
    suffix: String,
  },
  computed: {
    pricing() {
      return this.money(this.value)

    }
  },
  mounted() {
    if(!this.value) {
       this.$emit('input', 0)
    }
  },
  methods: {
    updateData(val) {
      if(val){
        this.$emit('input', this.money(this.cleaning(val)))
      }else {
        this.$emit('input', 0)
      }
    },
    money(numb) {
      return numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },
    cleaning(val) {
      return val.replace(/\D|^0+/g, '')
    }
  }
}
</script>