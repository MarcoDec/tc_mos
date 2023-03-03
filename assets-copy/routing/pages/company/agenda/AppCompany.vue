<script lang="ts" setup>
    import {ActionTypes} from '../../../../store/events'
    import type {Actions} from '../../../../store/events'
    import {onMounted} from 'vue'
    import {useNamespacedActions} from 'vuex-composition-helpers'

    const fetchEvent = useNamespacedActions<Actions>('events', [ActionTypes.FETCH_EVENTS])[ActionTypes.FETCH_EVENTS]

    onMounted(async () => {
        await fetchEvent()
    })
</script>

<template>
    <h1>agenda</h1>
    <AppTabs>
        <AppTab active title="Agenda Mensuel">
            <AppAgendaMonthWrapper/>
            <slot/>
        </AppTab>
        <AppTab title="Agenda Annuel">
            <slot/>
        </AppTab>
    </AppTabs>
</template>
