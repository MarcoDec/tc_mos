<script setup>
    import {computed, onMounted, ref} from 'vue-demi'
    import AppSupplierCreate from './AppSupplierCreate.vue'
    import AppSuspense from '../../../components/AppSuspense.vue'
    // import useCountries from '../../../stores/countries/countries'
    import useSuppliers from '../../../stores/supplier/suppliers'
    import { faUserTag } from '@fortawesome/free-solid-svg-icons';
    const title = 'Créer un Fournisseur'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const storeSuppliersList = useSuppliers()
    const suppliersList = ref([]);
    const AddForm = ref(false)

    onMounted(async () => {
      await storeSuppliersList.fetch();
      console.log('storeSuppliersList.suppliers', storeSuppliersList);
      suppliersList.value = storeSuppliersList.suppliers.map(supplier => ({
        '@id': supplier['@id'],
        name: supplier.name,
        state: supplier.embState.state 
      }));
      console.log('suppliersList.value ', suppliersList.value );
    });
    async function deleted(id){
        await storeSuppliersList.delated(id)
    }

    const fields = computed(() => [
        {
            create: false,
            label: 'Nom',
            name: 'name',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Etat',
            name: 'state',
            sort: false,
            update: true
        }
    ])
    async function getPage(nPage){
        await storeSuppliersList.itemsPagination(nPage)
    }

</script>

<template>
    <div class="row">
    <AppCol cols="11" class="d-flex justify-content-between mb-2">
        <h1>
            <Fa :icon="faUserTag"/>
            {{ title }}
        </h1>
    </AppCol>
    <AppCol  class="d-flex justify-content-between mt-2">
        <AppBtn
            variant="success"
            label="Créer"
            data-bs-toggle="modal"
            :data-bs-target="target">
            Créer
        </AppBtn>
    </AppCol>
    </div>
    <div class="row">
        <AppModal  :id="modalId" class="four" :title="title">
            <AppSupplierCreate/>
            <template #buttons>
                <AppBtn
                    variant="success"
                    label="Créer"
                    data-bs-toggle="modal"
                    :data-bs-target="target"
                    @click="supplierFormCreate">
                    Créer
                </AppBtn>
            </template>
        </AppModal>
        <div class="col">
            <AppSuspense>
                <AppCardableTable 
                    :current-page="storeSuppliersList.currentPage"
                    :fields="fields"
                    :first-page="storeSuppliersList.firstPage"
                    :items="suppliersList"
                    :last-page="storeSuppliersList.lastPage"
                    :next-page="storeSuppliersList.nextPage"
                    :pag="storeSuppliersList.pagination"
                    :previous-page="storeSuppliersList.previousPage"
                    user="roleuser"
                    form="formSupplierCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppSuspense>
        </div>
    </div>
</template>
