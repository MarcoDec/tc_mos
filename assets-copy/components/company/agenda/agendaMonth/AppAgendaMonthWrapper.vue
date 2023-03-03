<script lang="ts" setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import {company} from '../../../../store/production/companies'
    import {component} from '../../../../store/purchase/components'
    import {customer} from '../../../../store/selling/customers'
    import {employee} from '../../../../store/hr/employees'
    import {engine} from '../../../../store/production/engines'
    import {events} from '../../../../store/events'
    import {supplier} from '../../../../store/purchase/suppliers'
    import store from '../../../../store'

    const wrapper = ref(false)
    onMounted(() => {
        store.registerModule('employees', employee)
        store.registerModule('components', component)
        store.registerModule('engines', engine)
        store.registerModule('suppliers', supplier)
        store.registerModule('customers', customer)
        store.registerModule('companies', company)
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
