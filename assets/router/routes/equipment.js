export default [
    {
        component: async () => import('../pages/equipment/AppWorkstationShow.vue'),
        meta: {requiresAuth: true},
        name: 'workstationShow',
        path: '/workstation/show'
    },
    {
        component: async () => import('../pages/equipment/AppToolShow.vue'),
        meta: {requiresAuth: true},
        name: 'toolShow',
        path: '/tool/show'
    },
    {
        component: async () => import('../pages/equipment/AppTestCounterPartShow.vue'),
        meta: {requiresAuth: true},
        name: 'testCounterPartShow',
        path: '/testcounterpart/show'
    }
]
