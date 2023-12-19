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
    //region Gestion de la route de visualisation => enableShow
    if (props.enableShow) {
        store.enableShow = true
        store.showRouteName = `${route.name}/show`
    }
    //endregion
    await store.fetch()

    const storedFields = useFields(route.name, props.fields)
    await storedFields.fetch()

    onUnmounted(() => {
        store.dispose()
        storedFields.dispose()
    })
    function show(data) {
        router.push({name: props.showRouteName, params: {id: data}})
    }
    function gotoFirstPage() {
        store.page = 1
        store.fetch()
    }
    function gotoPreviousPage() {
        if (store.page > 1) {
            store.page--
            store.fetch()
        }
    }
    function gotoNextPage() {
        if (store.page < store.totalItems / store.perPage) {
            store.page++
            store.fetch()
        }
    }
    function gotoLastPage() {
        store.page = Math.ceil(store.totalItems / store.perPage)
        store.fetch()
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
    <div class="d-flex flex-row justify-content-center">
        <Fa v-if="store.hydraId !== store.hydraFirst " :brand="false" class="m-2 primary-hover zoom-hover" icon="backward-fast" @click="gotoFirstPage"/>
        <Fa v-if="store.hydraId !== store.hydraFirst " :brand="false" class="m-2 primary-hover zoom-hover" icon="backward-step" @click="gotoPreviousPage"/>
        <div class="pb-0 pt-1">
            Page {{ store.page }}/{{ store.lastPage }}
        </div>
        <Fa v-if="store.hydraId !== store.hydraLast" :brand="false" class="m-2 primary-hover zoom-hover" icon="forward-step" @click="gotoNextPage"/>
        <Fa v-if="store.hydraId !== store.hydraLast" :brand="false" class="m-2 primary-hover zoom-hover" icon="forward-fast" @click="gotoLastPage"/>
    </div>
</template>

<style>
    .shadow-hover:hover {
        box-shadow: 0 0 10px 0 black;
    }
    .zoom-hover:hover {
        transform: scale(1.2);
    }
    .primary-hover:hover {
        color: blue;
    }
</style>
