<script setup>
    import {computed, ref} from 'vue'
    import {useEmployeeListOFStore} from '../../../../../stores/hr/employee/employeeListOF'
    import {useRoute} from 'vue-router'
    import useField from '../../../../../stores/field/field'

    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const employeeId = maRoute.params.id_employee

    const storeEmployeeListOF = useEmployeeListOFStore()
    storeEmployeeListOF.setIdEmployee(employeeId)
    await storeEmployeeListOF.fetch()

    const itemsTable = ref(storeEmployeeListOF.itemsEmployeeOF)
    const formData = ref({
        of: null, poste: null, startDate: null, duration: null, actualQuantity: null, quantityProduced: null, cadence: null, statut: null, cloture: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Numéro OF',
            name: 'of',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Poste',
            name: 'poste',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de début',
            name: 'startDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fin',
            name: 'duration',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité actuelle',
            name: 'actualQuantity',
            measure: {
                code: null,
                value: null
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité produit',
            name: 'quantityProduced',
            measure: {
                code: null,
                value: null
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cadence',
            name: 'cadence',
            measure: {
                code: null,
                value: null
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut',
            name: 'statut',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Etat',
            name: 'cloture',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const parentQtActuelle = {
        $id: 'employeeOFQtActuelle'
    }
    const storeUnitQtActuelle = useField(fieldsForm[4], parentQtActuelle)
    storeUnitQtActuelle.fetch()

    fieldsForm[4].measure.code = storeUnitQtActuelle.measure.code
    fieldsForm[4].measure.value = storeUnitQtActuelle.measure.value

    const parentQtProduit = {
        $id: 'employeeOFQtProduit'
    }
    const storeUnitQtProduit = useField(fieldsForm[5], parentQtProduit)

    fieldsForm[5].measure.code = storeUnitQtProduit.measure.code
    fieldsForm[5].measure.value = storeUnitQtProduit.measure.value

    const parentQtCadence = {
        $id: 'employeeOFQtCandence'
    }
    const storeUnitQtCadence = useField(fieldsForm[6], parentQtCadence)

    fieldsForm[6].measure.code = storeUnitQtCadence.measure.code
    fieldsForm[6].measure.value = storeUnitQtCadence.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Numéro OF',
            name: 'of',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Poste',
            name: 'poste',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date',
            name: 'startDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité actuelle',
            name: 'actualQuantity',
            measure: {
                code: storeUnitQtActuelle.measure.code,
                value: storeUnitQtActuelle.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité produite',
            name: 'quantityProduced',
            measure: {
                code: storeUnitQtProduit.measure.code,
                value: storeUnitQtProduit.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cadence',
            name: 'duration',
            sort: false,
            type: 'number',
            step: 0.01,
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut',
            name: 'statut',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Etat',
            name: 'cloture',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            of: item.of,
            poste: item.poste,
            startDate: item.startDate,
            actualQuantity: item.actualQuantity,
            quantityProduced: item.quantityProduced,
            cadence: item.cadence,
            statut: item.statut,
            cloture: item.cloture,
            duration: item.duration
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEmployeeListOF.deleted(id)
        itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
    }
    async function getPage(nPage){
        await storeEmployeeListOF.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListOF.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        const payload = {
            of: inputValues.of ?? '',
            poste: inputValues.poste ?? '',
            startDate: inputValues.startDate ?? '',
            duration: inputValues.duration ?? '',
            actualQuantity: inputValues.actualQuantity ?? '',
            quantityProduced: inputValues.quantityProduced ?? '',
            cadence: inputValues.cadence ?? '',
            statut: inputValues.statut ?? '',
            cloture: inputValues.cloture ?? ''
        }

        if (typeof payload.actualQuantity.value === 'undefined' && payload.actualQuantity !== '') {
            payload.actualQuantity.value = ''
        }
        if (typeof payload.actualQuantity.code === 'undefined' && payload.actualQuantity !== '') {
            payload.actualQuantity.code = ''
        }

        if (typeof payload.quantityProduced.value === 'undefined' && payload.quantityProduced !== '') {
            payload.quantityProduced.value = ''
        }
        if (typeof payload.quantityProduced.code === 'undefined' && payload.quantityProduced !== '') {
            payload.quantityProduced.code = ''
        }

        if (typeof payload.cadence.value === 'undefined' && payload.cadence !== '') {
            payload.cadence.value = ''
        }
        if (typeof payload.cadence.code === 'undefined' && payload.cadence !== '') {
            payload.cadence.code = ''
        }
        await storeEmployeeListOF.filterBy(payload)
        itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeEmployeeListOF.fetch()
        itemsTable.value = storeEmployeeListOF.itemsEmployeeOF
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol>
                <p class="bg-light border text-center text-info">
                    Liste des opérations de fabrication associé à l'utilisateur (pic ou opérateur) de moins d'une semaine
                </p>
            </AppCol>
        </AppRow>
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeEmployeeListOF.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListOF.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListOF.lastPage"
                    :min="AddForm"
                    :next-page="storeEmployeeListOF.nextPage"
                    :pag="storeEmployeeListOF.pagination"
                    :previous-page="storeEmployeeListOF.previousPage"
                    :user="roleuser"
                    form="formEmployeeOFCardableTable"
                    :should-see="false"
                    :should-delete="false"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
        </AppRow>
    </div>
</template>

<style scoped>
    .gui-bottom {
        overflow: hidden;
    }
</style>
