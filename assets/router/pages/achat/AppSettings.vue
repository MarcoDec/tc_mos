<script setup>
    import AppTable from '../../../components/table/AppTable'
    import generateItems from '../../../stores/table/items'
    import {onMounted} from 'vue'
    import {useTableMachine} from '../../../machine'

    const machineSettings = useTableMachine('machine-settings')
    const settingsItems = generateItems('supplier-items-settings')
    onMounted(async () => {
        settingsItems.items = [
            {
                id: 1,
                name: 'bbb',
                valeur: 'vvv'
            },
            {
                id: 2,
                name: 'aaaa',
                valeur: 'xxxx'
            }
        ]
    })
    const fields = [
        {
            label: 'Nom',
            name: 'name',
            sort: true,
            update: false
        },
        {
            label: 'Valeur',
            name: 'valeur',
            sort: true,
            update: false
        }
    ]
</script>

<template>
    <div class="row">
        <div class="col-1">
            <h1><Fa icon="cogs"/></h1>
        </div>
        <div class="col">
            <h1>Settings</h1>
        </div>
    </div>
    <AppTable
        id="settings"
        :fields="fields"
        :machine="machineSettings"
        :store="settingsItems"/>
</template>
