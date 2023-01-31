<script setup>
    import {computed, onMounted, reactive, ref} from 'vue'
    import AppModalEvent from './AppModalEvent.vue'
    import FullCalendar from '@fullcalendar/vue3'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import interactionPlugin from '@fullcalendar/interaction'
    import timeGridPlugin from '@fullcalendar/timegrid'
    import useEvents from '../../../../../stores/events/events'
    import useUser from '../../../../../stores/hr/employee/user'

    const month = ref(6)
    const year = ref(2022)
    const show = ref(false)
    const selected = ref(0)
    const name = ref('')
    const date = ref('')
    const relation = ref('')
    const relationId = ref('')

    const listEvents = useEvents()
    const user = useUser()
    console.log('user',user);


    const events = computed(() => listEvents.findByMonth(month.value, year.value))
    if (user.isItAdmin) {
        console.log('admin');
    }

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
        await listEvents.fetch()
    })
    function closeModal() {
        show.value = false
    }
</script>

<template>
    <FullCalendar :options="{...calendarOptions, events}"/>
    <AppModalEvent
        v-show="show"
        :id="selected"
        :date="date"
        :name="name"
        :relation="relation"
        :relation-id="relationId"
        @close="closeModal"/>
</template>
