export default [
    {
        component: async () => import('../pages/employee/AppEmployeeShow.vue'),
        meta: {requiresAuth: true},
        name: 'employeeShow',
        path: '/employee/show'
    }
]
