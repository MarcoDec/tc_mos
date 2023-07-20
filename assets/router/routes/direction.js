export default [
    {
        component: async () => import('../../components/pages/Management/Society/bottom/AppSocietyShowInList.vue'),
        meta: {requiresAuth: true},
        name: 'directionsShow',
        path: '/society/show/:id_society'
    }
]
