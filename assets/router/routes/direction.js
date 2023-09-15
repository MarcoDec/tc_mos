export default [
    {
        component: async () => import('../../components/pages/management/society/bottom/AppSocietyShowInList.vue'),
        meta: {requiresAuth: true},
        name: 'directionsShow',
        path: '/society/show/:id_society'
    }
]
