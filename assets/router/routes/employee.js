export default [
    {
        component: () => import('../pages/employee/AppEmployeeFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'employeeFormShow',
        path: '/employee/formshow'
    }
]
