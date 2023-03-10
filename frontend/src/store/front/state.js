export default function () {
  return {
    posts: {
      is_done: false,
      is_available: true,
      data: []
    },
    blocks: {
      banner: [],
      partner: [],
      featured: [],
      ready: false,
      available: true
    },
    product_promo: [],
    categories: {
      ready: false,
      available: true,
      data: []
    },
    sliders: {
      count: 0,
      data: [],
      ready: false,
      available: true
    },
    is_loaded: false
  }
}
