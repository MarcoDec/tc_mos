<script setup>
    import AppCustomerCreate from './AppCustomerCreate.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useCustomers from '../../../stores/customer/customers'
    import {useTableMachine} from '../../../machine'

    const title = 'Créer un client'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineCustomer = useTableMachine('machine-customer')
    const customers = useCustomers()

    const fields = computed(() => [
        {
            create: true,
            label: 'Nom',
            name: 'nom',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        }
    ])
</script>
 
<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title">
            <AppCustomerCreate/>
        </AppModal>
        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineCustomer"
                :store="customers"
                title="La liste de Clients">
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
