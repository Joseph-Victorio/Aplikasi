export default function () {
  return {
    orders: {
      data: [],
      ready: false,
      available: true,
      isLoadMore: false,
      count: 0,
      limit: 0
    },
    customer_order: {
      data: [],
      ready: false,
      count: false,
      limit: 0,
      available: true,
      isLoadMore: false
    },
    invoice: null,
    transaction: null,
    formOrder: {
      items: [],
      quantity: 0,
      weight: 0,
      subtotal: 0,
      shipping_method: 'EKSPEDISI',
      shipping_destination: '',
      shipping_courier_name: '',
      shipping_courier_service: '',
      shipping_cost: 0,
      customer_name: '',
      customer_email: '',
      customer_phone: '',
      customer_address: '',
      customer_address_selected: null
    },
    orderItems: [],
  }
}
