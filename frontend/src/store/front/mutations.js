export function SET_BLOCKS (state, payload) {
  if(payload.length) {
    state.blocks.banner = payload.filter(el => el.position == 'Banner');
    state.blocks.partner = payload.filter(el => el.position == 'Partner');
    state.blocks.featured = payload.filter(el => el.position == 'Featured');
  }
  state.blocks.ready = true;
  state.blocks.available = payload.length? true : false;
}

export function SET_PRODUCT_PROMO (state, payload) 
{
  state.product_promo = payload

}
export function SET_CATEGORIES ( state, payload ) {

  if(payload.length != state.categories.length) {
    state.categories.data = payload.map(cat => ({...cat, product_items: [], is_done: false, is_available: true }))
    state.categories.ready = true
    state.categories.available = state.categories.data.length > 0
  }

}
export function CLEAR_FRONT_CATEGORIES ( state, payload ) {

  state.categories.data = []
  state.categories.ready = false
  state.categories.available = true

}
export function SET_PRODUCT_CATEGORY ( state, payload ) {
  let idx = state.categories.data.findIndex(el => el.id == payload.category_id)

  if(idx >= 0) {
    state.categories.data[idx].product_items = payload.product_items
    state.categories.data[idx].is_done = true
    state.categories.data[idx].is_available = payload.product_items.length > 0
  }

}
export function SET_SLIDERS (state, payload) {
  state.sliders.data = payload
  state.sliders.ready = true
  state.sliders.available = payload.length > 0 ? true : false
}

export function SET_SLIDERS_COUNT (state, total) {
  state.sliders.count = total
}

export function SET_POSTS (state, payload) {
  state.posts.data = payload
  state.posts.is_available = payload.length > 0
  state.posts.is_done = true
}

export function SET_LOADED(state) {
  state.is_loaded = true
}
export function REMOVE_PROMO(state, id) {
  state.product_promo = state.product_promo.filter(el => el.id != id)
}