
const routes = [
   {
      path: '/',
      component: () => import('layouts/FrontLayout.vue'),
      children: [
         { path: '', name: 'Home', component: () => import('pages/Front/PageIndex.vue') },
         { path: 'favorite', name: 'ProductFavorite', component: () => import('pages/Front/ProductFavorite.vue') },
         { path: 'products', name: 'ProductIndex', component: () => import('pages/Front/ProductIndex.vue') },
         { path: 'products/category/:id', name: 'ProductCategory', component: () => import('pages/Front/ProductsByCategory.vue') },
         { path: 'search-product', name: 'ProductSearch', component: () => import('src/pages/Front/ProductSearch.vue') },
         { path: 'search-order', name: 'OrderSearch', component: () => import('src/pages/Front/OrderSearch.vue') },
         { path: 'posts', name: 'FrontPostIndex', component: () => import('src/pages/Front/PostIndex.vue') },
         { path: 'post/:slug', name: 'FrontPostShow', component: () => import('src/pages/Front/PostDetaIl.vue') },
      ]
   },
   {
      path: '/product',
      component: () => import('src/layouts/BlankLayout.vue'),
      children: [
         {
            path: ':slug',
            name: 'ProductShow',
            component: () => import('pages/Front/ProductDetail.vue')
         }
      ]
   },
   {
      path: '/p',
      component: () => import('src/layouts/BlankLayout.vue'),
      children: [
         { path: 'cart', name: 'Cart', component: () => import('src/pages/Shop/Cart.vue') },
         { path: 'checkout', name: 'Checkout', component: () => import('src/pages/Checkout/Index.vue') },
         { path: 'invoice/:order_ref', name: 'UserInvoice', component: () => import('src/pages/Invoice/Index.vue') },
      ]
   },
   {
      path: '/auth',
      component: () => import('src/layouts/AuthLayout.vue'),
      children: [
         { path: 'login', name: 'Login', component: () => import('src/pages/Auth/Login.vue') },
         { path: 'register', name: 'Register', component: () => import('src/pages/Auth/Register.vue') },
         { path: 'forgot-password', name: 'ForgotPassword', component: () => import('src/pages/Auth/ForgotPassword.vue') },
         { path: 'reset-password', name: 'ResetPassword', component: () => import('src/pages/Auth/ResetPassword.vue') },
      ]
   },
   {
      path: '/form',
      component: () => import('layouts/FormLayout.vue'),
      meta: { requiresAdmin: true },
      children: [
         { path: 'product/create', name: 'ProductCreate', component: () => import('pages/Product/Create.vue') },
         { path: 'product/edit/:id', name: 'ProductEdit', component: () => import('pages/Product/Edit.vue') },
         { path: 'category/form', name: 'CategoryForm', component: () => import('src/pages/Category/CategoryForm.vue') },
         { path: 'category/form/:category_id', name: 'CategoryFormEdit', component: () => import('src/pages/Category/CategoryForm.vue') },
         { path: 'post/create', name: 'PostCreate', component: () => import('pages/Post/Create.vue') },
         { path: 'post/edit/:id', name: 'PostEdit', component: () => import('pages/Post/Edit.vue') },
         { path: 'config', name: 'Config', component: () => import('pages/Config/Index.vue') },
      ]
   },
   {
      path: '/me',
      component: () => import('layouts/CustomerLayout.vue'),
      meta: { requiresCustomer: true },
      children: [
         { path: '', name: 'CustomerAccount', component: () => import('src/pages/Customer/CustomerAccount.vue') },
         { path: 'edit', name: 'CustomerAccountEdit', component: () => import('src/pages/Customer/CustomerAccountEdit.vue') },
         { path: 'order', name: 'CustomerOrder', component: () => import('src/pages/Customer/CustomerOrder.vue') },
         { path: 'address', name: 'CustomerAddress', component: () => import('src/pages/Customer/CustomerAddress.vue') },
      ]
   },
   {
      path: '/admin',
      component: () => import('layouts/AdminLayout.vue'),
      meta: { requiresAdmin: true },
      children: [
         { path: 'account', name: 'Account', component: () => import('src/pages/Account/Index.vue') },
         { path: 'settings', name: 'Settings', component: () => import('src/pages/Dashboard/Settings.vue') },
         { path: 'slider', name: 'Slider', component: () => import('src/pages/Slider/Index.vue') },
         { path: 'store', name: 'Shop', component: () => import('src/pages/Shop/Index.vue') },
         { path: 'categories', name: 'CategoryIndex', component: () => import('src/pages/Category/CategoryIndex.vue') },
         { path: 'products', name: 'AdminProductIndex', component: () => import('pages/Product/Index.vue') },
         { path: 'posts', name: 'AdminPostIndex', component: () => import('src/pages/Post/Index.vue') },
         { path: 'blocks', name: 'AdminBlockIndex', component: () => import('pages/Block/Index.vue') },
         { path: 'orders', name: 'OrderIndex', component: () => import('pages/Order/Index.vue') },
         { path: 'order/:order_ref', name: 'AdminOrderShow', component: () => import('pages/Order/Detail.vue') },
         { path: 'banks', name: 'BankIndex', component: () => import('pages/Shop/BankAccount.vue') },
         { path: 'users', name: 'UserList', component: () => import('pages/Account/UserList.vue') },
         { path: 'promo', name: 'PromoIndex', component: () => import('pages/Promo/Index.vue') },
         { path: 'promo/:id', name: 'PromoDetail', component: () => import('pages/Promo/Detail.vue') },
         { path: 'reviews', name: 'ReviewsIndex', component: () => import('pages/Reviews/Index.vue') },
      ]
   },
   // Always leave this as last one,
   // but you can also remove it
   {
      path: '*',
      component: () => import('pages/Error404.vue')
   }
]

export default routes
