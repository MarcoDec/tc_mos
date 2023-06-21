<script setup>
    import {computed, ref} from 'vue'
    import {useSupplierListCommandeStore} from './storeProvisoir/supplierListCommande'
    import {useRoute} from 'vue-router'
    // import useField from '../../../stores/field/field'

    const roleuser = ref('reader')
    let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const supplierId = maRoute.params.id_supplier

    const storeSupplierListCommande = useSupplierListCommandeStore()
    storeSupplierListCommande.setIdSupplier(supplierId)
    await storeSupplierListCommande.fetch()
    const itemsTable = ref(storeSupplierListCommande.itemsSupplierCommande)
    const formData = ref({
        reference: null, dateDemandee: null, dateConfirmation: null, derniereReception: null, statutFournisseur: null, supplementFret: null, commentaire: null, infoPublic: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'reference',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date Demandée',
            name: 'dateDemandee',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de Confirmation',
            name: 'dateConfirmation',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière Réception',
            name: 'derniereReception',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut de Cmde Fournisseur',
            name: 'statutFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Supplément Fret',
            name: 'supplementFret',
            sort: true,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Commentaire',
            name: 'commentaire',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Info Public',
            name: 'infoPublic',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    // const parent = {
    //     //$id: `${warehouseId}Stock`
    //     $id: 'supplierCommande'
    // }
    // const storeUnit = useField(fieldsForm[8], parent)
    // //storeUnit.fetch()

    // fieldsForm[8].measure.code = storeUnit.measure.code
    // fieldsForm[8].measure.value = storeUnit.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'reference',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date Demandée',
            name: 'dateDemandee',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de Confirmation',
            name: 'dateConfirmation',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière Réception',
            name: 'derniereReception',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut de Cmde Fournisseur',
            name: 'statutFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Supplément Fret',
            name: 'supplementFret',
            sort: true,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Commentaire',
            name: 'commentaire',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Info Public',
            name: 'infoPublic',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            reference: null,
            dateDemandee: null,
            dateConfirmation: null,
            derniereReception: null,
            statutFournisseur: null,
            supplementFret: null,
            commentaire: null,
            infoPublic: null
        }
        formData.value = itemsNull
    }

    async function ajoutSupplierCommande(){
        //const form = document.getElementById('addSupplierCommande')
        //const formData1 = new FormData(form)

        const itemsAddData = {
            reference: formData.value.reference,
            dateDemandee: formData.value.dateDemandee,
            dateConfirmation: formData.value.dateConfirmation,
            derniereReception: formData.value.derniereReception,
            statutFournisseur: formData.value.statutFournisseur,
            supplementFret: formData.value.supplementFret,
            commentaire: formData.value.commentaire,
            infoPublic: formData.value.infoPublic
            //quantite: {code: formData1.get('quantite[code]'), value: formData1.get('quantite[value]')}
        }
        violations = await storeSupplierListCommande.addSupplierCommande(itemsAddData)

        if (violations.length > 0){
            isPopupVisible.value = true
        } else {
            AddForm.value = false
            updated.value = false
            isPopupVisible.value = false
            itemsTable.value = [...storeSupplierListCommande.itemsSupplierCommande]
        }
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            reference: null,
            dateDemandee: null,
            dateConfirmation: null,
            derniereReception: null,
            statutFournisseur: null,
            supplementFret: null,
            commentaire: null,
            infoPublic: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            reference: item.reference,
            dateDemandee: item.dateDemandee,
            dateConfirmation: item.dateConfirmation,
            derniereReception: item.derniereReception,
            statutFournisseur: item.statutFournisseur,
            supplementFret: item.supplementFret,
            commentaire: item.commentaire,
            infoPublic: item.infoPublic
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeSupplierListCommande.deleted(id)
        itemsTable.value = [...storeSupplierListCommande.itemsSupplierCommande]
    }
    async function getPage(nPage){
        await storeSupplierListCommande.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeSupplierListCommande.itemsSupplierCommande]
    }
    async function trierAlphabet(payload) {
        await storeSupplierListCommande.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        // let comp = ''
        // if (typeof inputValues.composant !== 'undefined'){
        //     comp = inputValues.composant
        // }

        // let prod = ''
        // if (typeof inputValues.produit !== 'undefined'){
        //     prod = inputValues.produit
        // }

        const payload = {
            reference: inputValues.reference ?? '',
            dateDemandee: inputValues.dateDemandee ?? '',
            dateConfirmation: inputValues.dateConfirmation ?? '',
            derniereReception: inputValues.derniereReception ?? '',
            statutFournisseur: inputValues.statutFournisseur ?? '',
            supplementFret: inputValues.supplementFret ?? '',
            commentaire: inputValues.commentaire ?? '',
            infoPublic: inputValues.infoPublic ?? ''
        }

        // if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
        //     payload.quantite.value = ''
        // }
        // if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
        //     payload.quantite.code = ''
        // }
        await storeSupplierListCommande.filterBy(payload)
        itemsTable.value = [...storeSupplierListCommande.itemsSupplierCommande]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeSupplierListCommande.fetch()
    }
</script>

<template>
    <AppCol class="d-flex justify-content-between mb-2">
        <AppBtn variant="success" label="Ajout" @click="ajoute">
            <Fa icon="plus"/>
            Ajouter
        </AppBtn>
    </AppCol>
    <AppRow>
        <AppCol>
            <AppCardableTable
                :current-page="storeSupplierListCommande.currentPage"
                :fields="tabFields"
                :first-page="storeSupplierListCommande.firstPage"
                :items="itemsTable"
                :last-page="storeSupplierListCommande.lastPage"
                :min="AddForm"
                :next-page="storeSupplierListCommande.nextPage"
                :pag="storeSupplierListCommande.pagination"
                :previous-page="storeSupplierListCommande.previousPage"
                :user="roleuser"
                form="formSupplierCardableTable"
                @update="update"
                @deleted="deleted"
                @get-page="getPage"
                @trier-alphabet="trierAlphabet"
                @search="search"
                @cancel-search="cancelSearch"/>
        </AppCol>
        <AppCol v-if="AddForm && !updated" class="col-7">
            <AppCard class="bg-blue col" title="">
                <AppRow>
                    <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                </AppRow>
                <br/>
                <AppFormCardable id="addSupplierCommande" :fields="fieldsForm" :model-value="formData" label-cols/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSupplierCommande">
                        <Fa icon="plus"/> Ajouter
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
