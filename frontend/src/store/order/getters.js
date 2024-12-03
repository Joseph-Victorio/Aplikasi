
export function getFormOrder(state) {

   let order = { ...state.formOrder }

   order.total = sumTotal(order)

   return order

}

function sumTotal(order) {
   return order.subtotal + parseInt(order.shipping_cost)

}

