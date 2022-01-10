<script lang="ts" setup>
import {onMounted, onUnmounted, ref} from "vue";
import store from "../../../../store";
import {events} from '../../../../store/hr/events'
import {employee} from '../../../../store/hr/employees'
import {component} from '../../../../store/purchase/components'
import MonthCalendar from "../../../../components/company/agenda/agendaMonth/MonthCalendar.vue";

const wrapper = ref(false)
onMounted(() => {
  store.registerModule('employees', employee)
  store.registerModule('components', component)
  store.registerModule('events', events)

  wrapper.value = true
})
onUnmounted(() => {
  store.unregisterModule('events')
})
</script>

<template>
<MonthCalendar v-if="wrapper"/>
</template>

<style scoped>

</style>