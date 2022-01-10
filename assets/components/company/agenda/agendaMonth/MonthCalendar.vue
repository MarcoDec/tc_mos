<script lang="ts" setup>
import {Actions, ActionTypes, Getters} from "../../../../store/hr/events";
import {Actions as ActionsEmp,ActionTypes as ActionTypesEmp} from '../../../../store/hr/employees'
import {Actions as ActionsComponent,ActionTypes as ActionTypesComponent} from '../../../../store/purchase/components'
import {computed, onMounted, reactive, ref} from "vue";
import {
  useNamespacedActions,
  useNamespacedGetters,
} from "vuex-composition-helpers";
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid';
import AppModalEvent from "../../../bootstrap-5/modal/AppModalEvent.vue";

const month = ref(1)
const year = ref(2022)
const show = ref(false)
const selected = ref(0)
const name = ref('')
const date = ref('')

const fetchEvent = useNamespacedActions<Actions>('events', [ActionTypes.FETCH_EVENTS])[ActionTypes.FETCH_EVENTS]
const employeeEvent = useNamespacedActions<ActionsEmp>('employees', [ActionTypesEmp.FETCH_EMP])[ActionTypesEmp.FETCH_EMP]
const componentEvent = useNamespacedActions<ActionsComponent>('components', [ActionTypesComponent.FETCH_COMP])[ActionTypesComponent.FETCH_COMP]
const findbymonth = useNamespacedGetters<Getters>('events', ['findByMonth']).findByMonth
const events = computed(() => findbymonth.value(month.value, year.value))

function handleEventClick(event: any) {
  show.value = !show.value
  selected.value = event.event.extendedProps.id
  name.value = event.event.extendedProps.name
  date.value = event.event.extendedProps.date
}

const calendarOptions = reactive({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
  events:events.value
  ,
  eventClick: handleEventClick,
  initialView: 'dayGridMonth',
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true
})

onMounted(async () => {
  await employeeEvent()
  await componentEvent()
  await fetchEvent()

})
function closeModal(): void{
  show.value= false
}
</script>

<template>
  <FullCalendar :options="{...calendarOptions, events}"/>
    <AppModalEvent :id="selected" :name="name" :date="date" v-show="show" @close="closeModal"/>
</template>

<style scoped>

</style>