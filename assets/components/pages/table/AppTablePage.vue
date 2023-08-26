<script setup>
    import {tableLoading, useTableMachine} from '../../../composable/xstate'
    import AppTable from '../../table/AppTable.vue'
    import {onUnmounted} from 'vue'
    /* eslint-disable vue/no-setup-props-destructure */
    import useFields from '../../../stores/field/fields'
    import {useRoute} from 'vue-router'
    import {useSlots} from '../../../composable/table'
    import useTable from '../../../stores/table/table'
    import useUser from '../../../stores/security'

    const props = defineProps({
        apiBaseRoute: {default: '', type: String},
        apiTypedRoutes: {
            // eslint-disable-next-line no-empty-function
            default: () => {},
            type: Object
        },
        brands: {type: Boolean},
        disableAdd: {type: Boolean},
        disableRemove: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        isCompanyFiltered: {required: false, type: Boolean},
        readFilter: {default: '', required: false, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const currentCompany = useUser().company
    const route = useRoute()
    const machine = useTableMachine(route.name)
    const {slots} = useSlots(props.fields)
    const store = useTable(route.name)
    store.sorted = props.sort.name
    store.sortName = props.sort.sortName ?? props.sort.name
    if (props.isCompanyFiltered) {
        store.readFilter = `?company=${currentCompany}${props.readFilter}`
        store.isCompanyFiltered = true
        store.company = currentCompany
    } else store.readFilter = props.readFilter
    store.apiBaseRoute = props.apiBaseRoute
    store.apiTypedRoutes = props.apiTypedRoutes
    await store.fetch()

    const storedFields = useFields(route.name, props.fields)
    await storedFields.fetch()

    onUnmounted(() => {
        store.dispose()
        storedFields.dispose()
    })
</script>

<template>
    <AppOverlay :spinner="tableLoading.some(machine.state.value.matches)">
        <div class="row">
            <h1 class="col">
                <Fa :brands="brands" :icon="icon"/>
                <span class="ms-2">{{ title }}</span>
            </h1>
        </div>
        <AppTable
            :id="route.name"
            :disable-add="disableAdd"
            :disable-remove="disableRemove"
            :fields="storedFields"
            :machine="machine"
            :store="store">
            <template v-for="s in slots" :key="s.name" #[s.slot]="args">
                <slot :name="s.slot" v-bind="args"/>
            </template>
        </AppTable>
    </AppOverlay>
</template>
