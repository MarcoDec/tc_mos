<template>
  <FullCalendar :options="calendarOptions" />
</template>

<script lang="ts" setup>
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import {reactive} from "vue";
import timeGridPlugin from '@fullcalendar/timegrid';

const calendarOptions = reactive({
  plugins: [ dayGridPlugin, interactionPlugin, timeGridPlugin ],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  initialView: 'dayGridMonth',
  editable: true,
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true
})
</script>

<style scoped>

</style>