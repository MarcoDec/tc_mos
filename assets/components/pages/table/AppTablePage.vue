<script setup>
    import {tableLoading, useTableMachine} from '../../../composable/xstate'
    import AppTable from '../../table/AppTable.vue'
    import {onUnmounted} from 'vue'
    /* eslint-disable vue/no-setup-props-destructure */
    import useFields from '../../../stores/field/fields'
    import {useRoute, useRouter} from 'vue-router'
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
        enableShow: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        isCompanyFiltered: {required: false, type: Boolean},
        readFilter: {default: '', required: false, type: String},
        showRouteName: {default: '', required: false, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const currentCompany = useUser().company
    const route = useRoute()
    const router = useRouter()
    const machine = useTableMachine(route.name)
    const {slots} = useSlots(props.fields)
    const store = useTable(route.name)
    store.sorted = props.sort.name
    store.sortName = props.sort.sortName ?? props.sort.name
    //region Gestion du filtre permanent par compagnie => isCompanyFiltered
    if (props.isCompanyFiltered) {
        store.readFilter = `?company=${currentCompany}${props.readFilter}`
        store.isCompanyFiltered = true
        store.company = currentCompany
    } else store.readFilter = props.readFilter
    //endregion
    store.apiBaseRoute = props.apiBaseRoute
    store.apiTypedRoutes = props.apiTypedRoutes
    if (props.enableShow) {
        store.enableShow = true
        store.showRouteName = `${route.name}/show`
    }
    await store.fetch()

    const storedFields = useFields(route.name, props.fields)
    await storedFields.fetch()

    onUnmounted(() => {
        store.dispose()
        storedFields.dispose()
    })
    function show(data) {
        console.log('data', data)
        console.log(props.showRouteName, data)
        router.push({name: props.showRouteName, params: {id: data}})
    }
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
            :enable-show="enableShow"
            :fields="storedFields"
            :machine="machine"
            :store="store"
            @show="show">
            <template v-for="s in slots" :key="s.name" #[s.slot]="args">
                <slot :name="s.slot" v-bind="args"/>
            </template>
        </AppTable>
    </AppOverlay>
</template>
