<script setup>
    import {computed, onMounted} from 'vue-demi'
    import AppSupplierCreate from './AppSupplierCreate.vue'
    import AppTablePage from '../AppTablePage'
    import useCountries from '../../../stores/countries/countries'
    import useSuppliers from '../../../stores/supplier/suppliers'
    import {useTableMachine} from '../../../machine'
    const title = 'Créer un Fournisseur'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineSupplier = useTableMachine('machine-supplier')
    const suppliers = useSuppliers()
    const countries = useCountries()
    onMounted(async () => {
        await countries.fetch()
    })

    const fields = computed(() => [
        {
            create: false,
            label: 'Nom',
            name: 'nom',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Etat',
            name: 'etat',
            sort: false,
            update: true
        }
    ])
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title">
            <AppSupplierCreate/>
        </AppModal>
        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineSupplier"
                :store="suppliers"
                title="La liste de fournisseurs">
                <template #cell(etat)>
                    <AppTrafficLight/>
                </template>
                <template #btn>
                    <AppBtn
                        variant="success"
                        data-bs-toggle="modal"
                        :data-bs-target="target">
                        Créer
                    </AppBtn>
                </template>
            </AppTablePage>
        </div>
    </div>
</template>
