export default [
    {
        component: async () => import('../pages/direction/AppSocietyShowInList.vue'),
        meta: {requiresAuth: true},
        name: 'directionsShow',
        path: '/society/show/:id_society'
    }
]
