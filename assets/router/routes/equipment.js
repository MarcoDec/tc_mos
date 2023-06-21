export default [
    {
        component: async () => import('../pages/equipment/AppWorkstationShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'workstationShow',
        path: '/workstation/show'
    },
    {
        component: async () => import('../pages/equipment/AppToolShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'toolShow',
        path: '/tool/show'
    },
    {
        component: async () => import('../pages/equipment/AppTestCounterPartShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'testCounterPartShow',
        path: '/testcounterpart/show'
    }
]
