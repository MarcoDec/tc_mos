export default [
    {
        component: () => import('../pages/equipment/AppWorkstationFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'workstationFormShow',
        path: '/workstation/formshow'
    },
    {
        component: () => import('../pages/equipment/AppToolFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'toolFormShow',
        path: '/tool/formshow'
    },
    {
        component: () => import('../pages/equipment/AppTestCounterPartFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'testCounterPartFormShow',
        path: '/testcounterpart/formshow'
    }
]
