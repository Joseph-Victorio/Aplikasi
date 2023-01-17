import { Api } from 'boot/axios'

export function productStore ({ dispatch, commit }, payload) {
  commit('SET_LOADING', true, { root: true })
  let self = this;
  return Api().post('products', payload, {headers: {'content-Type': 'multipart/formData'}})
  .then(response => {
    dispatch('getAdminProducts')
    self.$router.push({name: 'AdminProductIndex'})

  })
  .finally(() => {
    commit('SET_LOADING', false, { root: true })
  })
  
}
export function productUpdate ({ commit }, payload) {
  commit('SET_LOADING', true, { root: true })
    Api().post('products/' + payload.id, _formatData(payload), 
    {headers: {'content-Type': 'multipart/formData'}})
    .then(response => {
    commit('UPDATE_PRODUCT', response.data.results)
    
    this.$router.push({name: 'AdminProductIndex'})
  })
  .finally(() => {
    commit('SET_LOADING', false, { root: true })
  })
}

export function getAdminProducts ({ commit }, query = null) {
  let url = 'products'

  if(query) {
    url = `${url}?${new URLSearchParams(query).toString()}`
  }
  
  Api(url).get(url).then(response => {
     commit('SET_ADMIN_PRODUCTS', response.data.results)
   })
}
export function searchAdminProducts ({ commit }, key) {
  return  Api().get('searchAdminProducts/' + key)
}

export function getProductById ({}, id) {
  return Api().get('products/' + id)
}

export function productDelete ( { dispatch },  id) {
  Api().delete('products/' + id).then(() => {
    dispatch('getAdminProducts')
  })
}

// FRONT
export function getProducts ({ commit }) {

  Api().get('getProducts').then(response => {
     commit('SET_PRODUCTS', response.data)
   })
}

export function getProductDetail ({}, slug) {
  return Api().get('getProductDetail/' + slug)
}

export function searchProducts ({ commit }, q) {
  return Api().get('searchProduct/'+q)
 }

export function getProductsByCategory ({ commit }, id) {

  Api().get('getProductsByCategory/'+id).then(response => {
    if(response.status == 200) {
      commit('SET_PRODUCT_CATEGORY', response.data)
      if(response.data.results.length) {
        if(response.data.results[0].category){
          commit('SET_META_TITLE', response.data.results[0].category.title, { root: true })
        }
      }
    }
  })
 }

export function getProductsFavorites ({ commit }, payload) {
  Api().post('getProductsFavorites', payload).then(response => {
    if(response.status == 200) {
      commit('SET_PRODUCT_FAVORITE', response.data)
    }
  })
 }
export function addProductReview ({ commit }, payload) {
  return Api().post('addProductReview', payload)
}
export function loadProductReview ({}, payload) {
  if(payload.skip) {
    return Api().get('loadProductReview/'+ payload.product_id +'?skip=' + payload.skip)
  } else {
    return Api().get('loadProductReview/'+ payload.product_id )
  }
}

function _formatData(payload) {
  let formData = new FormData();

  formData.append('_method', payload._method)
  formData.append('simple_product', payload.simple_product)
  formData.append('id', payload.id)
  formData.append('title', payload.title)
  formData.append('price', payload.price)
  formData.append('weight', payload.weight)
  formData.append('has_subvarian', payload.has_subvarian)
  formData.append('stock', payload.stock)
  formData.append('description', payload.description)
  formData.append('featured_index', payload.featured_index)
  if(payload.featured_asset) {
    formData.append('featured_asset', payload.featured_asset)
  }

  if(payload.category_id) {
    formData.append('category_id', payload.category_id)
  }
  if(payload.varians.length) {
    formData.append('varians', JSON.stringify(payload.varians))
  }

  if(payload.remove_varian.length) {
    formData.append('remove_varian', JSON.stringify(payload.remove_varian))
  }
  if(payload.remove_subvarian.length) {
    formData.append('remove_subvarian', JSON.stringify(payload.remove_subvarian))
  }

  if(payload.images.length > 0) {

    for( var i = 0; i < payload.images.length; i++ ){
      let file = payload.images[i];

      formData.append('images[' + i + ']', file);
    }
  }
  if(payload.del_images.length > 0) {

    for( var j = 0; j < payload.del_images.length; j++ ){
      let file = payload.del_images[j];

      formData.append('del_images[' + j + ']', file);
    }
  }

  return formData;
}
