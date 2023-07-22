export default [
    {
        component: () => import('../../components/pages/project/product/AppProductFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'productFormShow',
        path: '/product/formshow'
    }
]
