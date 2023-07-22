export default [
    {
        component: () => import('../../components/pages/hr/employee/AppEmployeeFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'employeeFormShow',
        path: '/employee/formshow'
    }
]
