export default [
    {
        component: async () => import('../pages/employee/AppEmployeeShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'employeeShow',
        path: '/employee/show/:id_employee'
    }
]
