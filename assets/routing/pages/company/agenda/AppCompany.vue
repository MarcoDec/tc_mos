<script lang="ts" setup>
import {Actions, ActionTypes} from "../../../../store/hr/events";
import AppAgendaMonthWrapper from "../../../../components/company/agenda/agendaMonth/AppAgendaMonthWrapper.vue";
import AppTabs from "../../../../components/bootstrap-5/tab/AppTabs.vue";
import AppTab from "../../../../components/bootstrap-5/tab/AppTab.vue";
import YearCalendar from "../../../../components/company/agenda/YearCalendar.vue";
import {useNamespacedActions} from "vuex-composition-helpers";
import {onMounted} from "vue";

const fetchEvent = useNamespacedActions<Actions>('events', [ActionTypes.FETCH_EVENTS])[ActionTypes.FETCH_EVENTS]

onMounted(async () => {
  await fetchEvent()
})
</script>
<template>
  <h1>agenda</h1>
  <AppTabs>
    <AppTab active title="Agenda Mensuel">
      <AppAgendaMonthWrapper />
      <slot/>
    </AppTab>
    <AppTab title="Agenda Annuel">
      <YearCalendar/>
      <slot/>
    </AppTab>
  </AppTabs>
</template>
<style scoped>

</style>