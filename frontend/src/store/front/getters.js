
export const getProductItemByCategoryId =  (state) => (id) => {

  let data = []
  let ind =  state.categories.findIndex(cat => cat.id == id)

  if(ind >= 0) {
    data = state.categories[ind].product_items
  }

  return data
}

