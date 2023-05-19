<script setup>
    import {onMounted, onUnmounted} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import AppBtnJS from '../../../components/AppBtnJS'
    import AppFormJS from '../../../components/form/AppFormJS'
    import AppModal from '../../../components/modal/AppModal.vue'
    import AppTableJSinVue from '../../../components/table/AppTableJSinVue.vue'
    import Fa from '../../../components/Fa'
    import api from '../../../api'
    import {useTableMachine} from '../../../machine'
    import useUser from '../../../stores/security'
    import {useWarehouseListItemsStore} from '../../../stores/logistic/warehouses/warehouseListItems'

    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    function refreshStore() {
        storeWarehouseListItems.fetch()
    }

    onMounted(() => {
        document.addEventListener('deletion', refreshStore)
    })

    onUnmounted(() => {
        document.removeEventListener('deletion')
    })

    const route = useRoute()
    console.debug('route', route)
    const router = useRouter()
    const options = {
        delete: true,
        modify: false,
        show: true
    }
    console.debug('options AppWarehouseList', options)
    const addFormName = 'addEntrepots'
    const formfields = [
        {label: 'Nom *', name: 'name', type: 'text'},
        {
            label: 'Famille ',
            name: 'families',
            options: [
                {
                    disabled: false,
                    label: 'Prison',
                    value: 'prison'
                },
                {
                    disabled: false,
                    label: 'Production',
                    value: 'production'
                },
                {
                    disabled: false,
                    label: 'Réception',
                    value: 'réception'
                },
                {
                    disabled: false,
                    label: 'Magasin pièces finies',
                    value: 'magasin pièces finies'
                },
                {
                    disabled: false,
                    label: 'Expédition',
                    value: 'expédition'
                },
                {
                    disabled: false,
                    label: 'Magasin matières premières',
                    value: 'magasin matières premières'
                },
                {
                    disabled: false,
                    label: 'Camion',
                    value: 'camion'
                }
            ],
            optionsList: {
                camion: 'camion',
                expédition: 'expédition',
                'magasin matières premières': 'magasin matières premières',
                'magasin pièces finies': 'magasin pièces finies',
                prison: 'prison',
                production: 'production',
                réception: 'réception'
            },
            type: 'multiselect'
        }
    ]
    let selectedFamilies = []

    async function createNewWarehouse() {
        const form = document.getElementById(addFormName)
        const formData = new FormData(form)
        if (formData.get('name') === '' || selectedFamilies.length === 0) {
            window.alert('Formulaire de création incomplet, merci de renseigner un nom d\'entrepôt non vide et au moins une famille d\'appartenance')
        } else {
            const sentData = {
                company: useUser().company,
                families: selectedFamilies,
                name: formData.get('name')
            }
            await api('/api/warehouses', 'POST', sentData)
            refreshStore()
        }
    }

    const emit = defineEmits(['update'])

    function deletion() {
        console.debug('AppWarehouseList.vue deletion()')
        refreshStore()
    }

    async function update(item) {
        emit('update', item)
        await router.push({name: 'warehouse-show'})
    }

    function updateCreate(item) {
        if (typeof item.families !== 'undefined') {
            selectedFamilies = item.families
        }
    }
    const machine = useTableMachine(route.name)
    const storeWarehouseListItems = useWarehouseListItemsStore()
    storeWarehouseListItems.fetch()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
        <AppBtnJS variant="success" class="btnRight" data-bs-toggle="modal" data-bs-target="#split">
            créer
        </AppBtnJS>
        <AppBtnJS variant="secondary" class="btnRight">
            Flux d'entrepôts
        </AppBtnJS>
    </h1>
    <AppModal id="split" title="Créer un entrepot">
        <AppFormJS :id="addFormName" :fields="formfields" @update:model-value="updateCreate" @submit="refreshStore"/>
        <template #buttons>
            <AppBtnJS class="float-end" variant="success" @click="createNewWarehouse">
                créer
            </AppBtnJS>
        </template>
    </AppModal>
    <AppTableJSinVue
        :id="route.name"
        :fields="fields"
        :store="storeWarehouseListItems"
        :machine="machine"
        :options="options"
        @update="update"
        @deletion="deletion"/>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
