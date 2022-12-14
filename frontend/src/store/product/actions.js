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

export function getProducts ({ commit }) {

  Api().get('products').then(response => {
     commit('SET_PRODUCTS', response.data)
   })
}

export function getProductById ({}, id) {
  return Api().get('products/' + id)
}
export function getProductDetail ({}, slug) {
  return Api().get('getProductDetail/' + slug)
}

export function productDelete ( { dispatch },  id) {
  Api().delete('product/' + id).then(() => {
    dispatch('getAdminProducts')
  })
}

export function searchProducts ({ commit }, q) {
  return Api().get('search/'+q)
 }

export function getProductsByCategory ({ commit }, id) {

  Api().get('getProductsByCategory/'+id).then(response => {
    if(response.status == 200) {
      commit('SET_PRODUCT_CATEGORY', response.data)
      if(response.data.data.length) {
        if(response.data.data[0].category){
          commit('SET_META_TITLE', response.data.data[0].category.title, { root: true })
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

function _formatData(paylod) {
  let formData = new FormData();

  formData.append('_method', payload._method)
  formData.append('simple_product', paylod.simple_product)
  formData.append('id', paylod.id)
  formData.append('title', paylod.title)
  formData.append('price', paylod.price)
  formData.append('weight', paylod.weight)
  formData.append('has_subvarian', paylod.has_subvarian)
  formData.append('stock', paylod.stock)
  formData.append('description', paylod.description)
  formData.append('featured_index', paylod.featured_index)
  if(paylod.featured_asset) {
    formData.append('featured_asset', paylod.featured_asset)
  }

  if(paylod.category_id) {
    formData.append('category_id', paylod.category_id)
  }
  if(paylod.varians.length) {
    formData.append('varians', JSON.stringify(paylod.varians))
  }

  if(paylod.remove_varian.length) {
    formData.append('remove_varian', JSON.stringify(paylod.remove_varian))
  }
  if(paylod.remove_subvarian.length) {
    formData.append('remove_subvarian', JSON.stringify(paylod.remove_subvarian))
  }

  if(paylod.images.length > 0) {

    for( var i = 0; i < paylod.images.length; i++ ){
      let file = paylod.images[i];

      formData.append('images[' + i + ']', file);
    }
  }
  if(paylod.del_images.length > 0) {

    for( var j = 0; j < paylod.del_images.length; j++ ){
      let file = paylod.del_images[j];

      formData.append('del_images[' + j + ']', file);
    }
  }

  return formData;
}
