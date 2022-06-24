export default [
    {
        component: () => import('../pages/company/agenda/agendaMonth/MonthCalendar.vue'),
        meta: {requiresAuth: true},
        name: 'company',
        path: '/company'
    }
]
