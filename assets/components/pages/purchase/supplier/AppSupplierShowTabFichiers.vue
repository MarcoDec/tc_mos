<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../MyTree.vue'
    import {useSupplierAttachmentStore} from '../../../../stores/supplier/supplierAttachement'
    import {useSuppliersStore} from '../../../../stores/supplier/suppliers'

    const currentSupplierData = ref({})
    const fetchSuppliersStore = useSuppliersStore()
    currentSupplierData.value = fetchSuppliersStore.supplier
    const fetchSupplierAttachmentStore = useSupplierAttachmentStore()
    await fetchSupplierAttachmentStore.fetchBySupplier(currentSupplierData.value.id)
    const fichiersfields = [
        {label: 'Catégorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
    const isError = ref(false)
    const violations = ref([])
    const supplierAttachment = computed(() =>
        fetchSupplierAttachmentStore.supplierAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(),
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: supplierAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${supplierAttachment.value.length})`
        }
        return data
    })
    async function updateFichiers(value) {
        const suppliersId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)
        const data = {
            category: formData.get('category'),
            file: formData.get('file'),
            supplier: `/api/suppliers/${suppliersId}`
        }
        try {
            await fetchSupplierAttachmentStore.ajout(data)
            isError.value = false
        } catch (error) {
            const err = {
                message: error
            }
            violations.value.push(err)
            isError.value = true
        }
    }
</script>

<template>
    <AppTab
        id="gui-start-files"
        title="Fichiers"
        icon="laptop"
        tabs="gui-start">
        <AppCardShow
            id="addFichiers"
            :fields="fichiersfields"
            :component-attribute="currentSupplierData"
            @update="updateFichiers(fetchSuppliersStore.supplier)"/>
        <div v-if="isError" class="alert alert-danger" role="alert">
            <div v-for="violation in violations" :key="violation">
                <li>{{ violation.message }}</li>
            </div>
        </div>
        <MyTree :node="treeData"/>
    </AppTab>
</template>
