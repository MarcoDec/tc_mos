<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps, ref} from 'vue'
    import type {FormValues} from '../../types/bootstrap-5'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const formData = ref<FormValues>({
        date: null, equipement: null, fait: false, intervenant: null, name: null, type: null
    })
    const form = ref(false)
    const updated = ref(false)

    function ajoute(): void {
        form.value = true
    }
    function annule(): void {
        form.value = false
        updated.value = false
        const itemsNull = {
            date: null,
            equipement: null,
            fait: false,
            intervenant: null,
            name: null,
            type: null
        }
        formData.value = itemsNull
    }
    function update(item: TableItem): void {
        updated.value = true
        const itemsData = {
            date: item.date,
            equipement: item.equipement,
            fait: item.fait,
            intervenant: item.intervenant,
            name: item.name,
            type: item.type
        }
        formData.value = itemsData
    }

    const items: TableItem[] = [
        {
            date: '2019-03-04 16:41:08',
            delete: true,
            equipement: 'MDB-002',
            fait: true,
            intervenant: null,
            name: 'aaaaaaaa',
            type: 'request',
            update: false,
            update2: true
        },
        {
            date: '2019-03-04 16:30:59',
            delete: true,
            equipement: 'MA-016',
            fait: true,
            intervenant: null,
            name: 'bbbbbbbbbb',
            type: 'request',
            update: false,
            update2: true
        }
    ]
</script>

<template>
    <div class="row">
        <div class="col-11">
            <h1>
                <Fa :icon="icon"/>
                {{ title }}
            </h1>
        </div>
        <div class="col">
            <button class="btn btn-success" @click="ajoute">
                <Fa icon="plus"/> Ajouter
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <AppCollectionTable :id="route.name" :fields="fields" :items="items" @update="update"/>
        </div>
        <div v-if="form || updated " class="col">
            <AppCard class="bg-blue col">
                <div class="row">
                    <button id="btnRetour" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 v-if="form" class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                    <h4 v-else class="col">
                        <Fa icon="pencil-alt"/>  Modification
                    </h4>
                </div>
                <br/>
                <AppForm :fields="fields" :model-value="formData">
                    <template v-if="form" #buttons>
                        <AppBtn class="float-end" variant="success" size="sm">
                            <Fa icon="plus"/> Ajouter
                        </AppBtn>
                    </template>
                    <template v-else #buttons>
                        <AppBtn class="float-end" variant="success" size="sm">
                            <Fa icon="pencil-alt"/> Modifier
                        </AppBtn>
                    </template>
                </AppForm>
            </AppCard>
        </div>
    </div>
</template>
