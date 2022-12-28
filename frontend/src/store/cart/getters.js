export const cartCount = (state) => {
  return state.carts.length
}

export const getCarts = (state) => {
  let subtotal = getSumTotal(state.carts)
  let carts = {
    items: state.carts,
    subtotal: subtotal,
    weight: getSUmWeight(state.carts),
    qty: getSumQty(state.carts),
  }

  return carts
}

function getSumTotal(items) {

  if(items.length) {

    if(items.length > 1) {
      let j = [];
        items.forEach(element => {
        j.push(element.quantity*element.price)
      });
     return j.reduce((a,b) => a + b)
    } else {
  
     return items[0].quantity * items[0].price
    }
  }
  return 0

}
function getSUmWeight(items) {

  if(items.length) {

    if(items.length > 1) {
      let w = [];
      items.forEach(el => {
        w.push(parseInt(el.weight)*parseInt(el.quantity))
      });
      return  w.reduce((a,b) => a + b)
    } else {
  
      return parseInt(items[0].quantity) * parseInt(items[0].weight)
    }
  }
  return 0

}
function getSumQty(items) {
  if(items.length) {

    if(items.length > 1) {
      let q = [];
      items.forEach(el => {
        q.push(parseInt(el.quantity))
      });
      return q.reduce((a,b) => a + b)
    }
    return parseInt(items[0].quantity)
  }
  return 0
}
