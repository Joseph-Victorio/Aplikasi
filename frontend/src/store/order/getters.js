
export function getFormOrder(state) {

  let order = { ...state.formOrder }

  order.total = sumTotal(order)

  return order

}

function sumTotal(order) {
  return order.subtotal + parseInt(order.shipping_cost)

}
function getUniqueCode() {
  let result = ''
  var randomNumb1 = '0123';
  var randomNumb2 = '123456789123456789';

  result += randomNumb1.charAt(Math.floor(Math.random() * randomNumb1.length));

  for (var i = 0; i < 2; i++) {
    result += randomNumb2.charAt(Math.floor(Math.random() * randomNumb2.length));
  }
  return parseInt(result);
}
