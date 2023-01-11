
export const getProductItemByCategoryId =  (state) => (id) => {

  let data = []
  let ind =  state.categories.data.findIndex(cat => cat.id == id)

  if(ind >= 0) {
    data = state.categories.data[ind].product_items
  }

  return data
}

export function getBanner1 (state) {
  if(state.blocks.banner.length) {
    return 
  }
  return null
}

