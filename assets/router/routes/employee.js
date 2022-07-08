export default [
    {
        component: () => import('../pages/employee/AppEmployeePage.vue'),
        meta: {requiresAuth: true},
        name: 'employee-list',
        path: '/employee-list'
    }
]
