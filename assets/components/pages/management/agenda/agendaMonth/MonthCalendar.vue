<script setup>
    import {computed, onMounted, reactive, ref} from 'vue'
    import AppModalEvent from './AppModalEvent.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import FullCalendar from '@fullcalendar/vue3'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import interactionPlugin from '@fullcalendar/interaction'
    import timeGridPlugin from '@fullcalendar/timegrid'
    import useEventsCompany from '../../../../../stores/purchase/eventsCompany/events'
    import useEventsCustomer from '../../../../../stores/selling/eventsCustomer/events'
    import useEventsEmployee from '../../../../../stores/hr/eventsEmployee/events'
    import useEventsEngine from '../../../../../stores/production/eventsEngine/events'
    import useUser from '../../../../../stores/security'

    const today = new Date()
    const month = ref(today.getMonth() + 1)
    const year = ref(today.getFullYear())
    const show = ref(false)
    const selected = ref(0)
    const name = ref('')
    const date = ref('')
    const relation = ref('')
    const relationId = ref('')
    const user = useUser()

    const listEventsCompany = useEventsCompany()
    const listEventsCustomer = useEventsCustomer()
    const listEventsEmployee = useEventsEmployee()
    const listEventsEngine = useEventsEngine()

    const eventsCompany = computed(() => listEventsCompany.findByMonth(month.value, year.value))
    const eventsCustomer = computed(() => listEventsCustomer.findByMonth(month.value, year.value))
    const eventsEmployee = computed(() => listEventsEmployee.findByMonth(month.value, year.value))
    const eventsEngine = computed(() => listEventsEngine.findByMonth(month.value, year.value))
    const events = computed(() => [eventsCompany.value, eventsCustomer.value, eventsEmployee.value, eventsEngine.value].reduce((acc, table) => acc.concat(table), []))

    function handleEventClick(event) {
        show.value = !show.value
        selected.value = event.event.extendedProps.id
        name.value = event.event.extendedProps.name
        date.value = event.event.extendedProps.dateTime
        relation.value = event.event.extendedProps.relation
        relationId.value = event.event.extendedProps.relationId
    }
    const calendarOptions = reactive({
        dayMaxEvents: true,
        eventClick: handleEventClick,
        events,
        initialView: 'dayGridMonth',
        plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
        selectMirror: true,
        selectable: true,
        weekends: true
    })
    onMounted(async () => {
        if (user.isManagementReader) {
            listEventsCompany.fetch()
        }
        if (user.isHrReader) {
            listEventsEmployee.fetch()
        }
        if (user.isSellingReader) {
            listEventsCustomer.fetch()
        }
        if (user.isMaintenanceReader) {
            listEventsEngine.fetch()
        }
    })
    function closeModal() {
        show.value = false
    }
</script>

<template>
    <AppSuspense>
        <FullCalendar :options="{...calendarOptions, events}"/>
        <AppModalEvent
            v-show="show"
            :id="selected"
            :date="date"
            :name="name"
            :relation="relation"
            :relation-id="relationId"
            @close="closeModal"/>
    </AppSuspense>
</template>
