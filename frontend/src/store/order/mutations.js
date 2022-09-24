
export function SET_ORDERS (state, payload) {

  state.orders.data = payload.results
  state.orders.ready = true
  state.orders.count = payload.count
  state.orders.limit = payload.limit
  state.orders.available = payload.count > 0 ? true : false
  
}
export function SET_PAGINATE_ORDERS (state, payload) {

  state.orders.data = [...state.orders.data, ...payload.results]

}

export function SET_CUSTOMER_ORDERS (state, payload) {
  state.customer_order.data = payload.results
  state.customer_order.ready = true
  state.customer_order.count = payload.count
  state.customer_order.limit = payload.limit
  state.customer_order.available = payload.count > 0 ? true : false
  
}
export function SET_PAGINATE_CUSTOMER_ORDERS (state, payload) {

  state.customer_order.data = [...state.customer_order.data, ...payload.results]
  
}
export function SET_INVOICE (state, payload) {

  state.invoice = payload
  
}
export function SET_TRANSACTION (state, payload) {

  state.transaction = payload
  
}
export function SET_LOAD_MORE (state, status) {

  state.orders.isLoadMore = status
  
}
export function SET_LOAD_MORE_CUSTOMER (state, status) {

  state.customer_order.isLoadMore = status
  
}
export function COLLECT_ORDER (state, payload) {

  state.formOrder.items = payload.items
  state.formOrder.quantity = payload.qty
  state.formOrder.weight = payload.weight
  state.formOrder.subtotal = payload.subtotal
  state.formOrder.coupon_discount = payload.discount_amount
  
}

export function SET_FORM_ORDER (state, payload) {
  state.formOrder[payload.key] = payload.value
}

export function SET_NOTIFY_ORDER_ITEMS (state, payload) {

  state.orderItems = payload

}



