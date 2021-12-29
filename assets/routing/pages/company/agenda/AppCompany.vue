<script lang="ts" setup>
import AppTabs from "../../../../components/bootstrap-5/tab/AppTabs.vue";
import AppTab from "../../../../components/bootstrap-5/tab/AppTab.vue";
import MonthCalendar from "../../../../components/company/agenda/MonthCalendar.vue";
import YearCalendar from "../../../../components/company/agenda/YearCalendar.vue";
import {useNamespacedActions} from "vuex-composition-helpers";
import {Actions, ActionTypes} from "../../../../store/company/events";
import {onMounted} from "vue";

const fetchEvent = useNamespacedActions<Actions>('event', [ActionTypes.FETCH_EVENTS])[ActionTypes.FETCH_EVENTS]

onMounted(async () => {
  await fetchEvent()
})
</script>
<template>
  <h1>agenda</h1>
  <AppTabs>
    <AppTab active title="Agenda Mensuel">
      <MonthCalendar/>
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