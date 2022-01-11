<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/events'
    import {computed, onMounted, reactive, ref} from 'vue'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import {ActionTypes} from '../../../../store/events'
    import {ActionTypes as ActionTypesCompany} from '../../../../store/production/companies'
    import {ActionTypes as ActionTypesComponent} from '../../../../store/purchase/components'
    import {ActionTypes as ActionTypesCustomer} from '../../../../store/selling/customers'
    import {ActionTypes as ActionTypesEmp} from '../../../../store/hr/employees'
    import {ActionTypes as ActionTypesEngine} from '../../../../store/production/engines'
    import {ActionTypes as ActionTypesSupplier} from '../../../../store/purchase/suppliers'
    import type {Actions as ActionsCompany} from '../../../../store/production/companies'
    import type {Actions as ActionsComponent} from '../../../../store/purchase/components'
    import type {Actions as ActionsCustomer} from '../../../../store/selling/customers'
    import type {Actions as ActionsEmp} from '../../../../store/hr/employees'
    import type {Actions as ActionsEngine} from '../../../../store/production/engines'
    import type {Actions as ActionsSupplier} from '../../../../store/purchase/suppliers'
    import '@fullcalendar/core/vdom'
    import FullCalendar from '@fullcalendar/vue3'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import interactionPlugin from '@fullcalendar/interaction'
    import timeGridPlugin from '@fullcalendar/timegrid';

    const month = ref(1)
    const year = ref(2022)
    const show = ref(false)
    const selected = ref(0)
    const name = ref('')
    const date = ref('')

    const fetchEvent = useNamespacedActions<Actions>('events', [ActionTypes.FETCH_EVENTS])[ActionTypes.FETCH_EVENTS]
    const employeeEvent = useNamespacedActions<ActionsEmp>('employees', [ActionTypesEmp.FETCH_EMP])[ActionTypesEmp.FETCH_EMP]
    const componentEvent = useNamespacedActions<ActionsComponent>('components', [ActionTypesComponent.FETCH_COMP])[ActionTypesComponent.FETCH_COMP]
    const supplierEvent = useNamespacedActions<ActionsSupplier>('suppliers', [ActionTypesSupplier.FETCH_SUPPLIEER])[ActionTypesSupplier.FETCH_SUPPLIEER]
    const engineEvent = useNamespacedActions<ActionsEngine>('engines', [ActionTypesEngine.FETCH_ENGINE])[ActionTypesEngine.FETCH_ENGINE]
    const customerEvent = useNamespacedActions<ActionsCustomer>('customers', [ActionTypesCustomer.FETCH_CUSTOMER])[ActionTypesCustomer.FETCH_CUSTOMER]
    const companyEvent = useNamespacedActions<ActionsCompany>('companies', [ActionTypesCompany.FETCH_COMPANY])[ActionTypesCompany.FETCH_COMPANY]
    const findbymonth = useNamespacedGetters<Getters>('events', ['findByMonth']).findByMonth
    const events = computed(() => findbymonth.value(month.value, year.value))

    function handleEventClick(event: any): void {
        show.value = !show.value
        selected.value = event.event.extendedProps.id
        name.value = event.event.extendedProps.name
        date.value = event.event.extendedProps.date
    }

    const calendarOptions = reactive({
      plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
      eventClick: handleEventClick,
        events: events.value,
        initialView: 'dayGridMonth',
       dayMaxEvents: true,
       selectMirror: true,
       selectable: true,
       weekends: true
    })

    onMounted(async () => {
        await employeeEvent()
        await componentEvent()
        await supplierEvent()
        await engineEvent()
        await customerEvent()
        await companyEvent()
        await fetchEvent()
    })
    function closeModal(): void{
        show.value = false
    }
</script>

<template>
    <FullCalendar :options="{...calendarOptions, events}"/>
    <AppModalEvent v-show="show" :id="selected" :name="name" :date="date" @close="closeModal"/>
</template>
