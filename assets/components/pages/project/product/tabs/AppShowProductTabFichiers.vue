<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../../MyTree.vue'
    import {useProductAttachmentStore} from '../../../../../stores/product/productAttachement'
    import {useProductStore} from '../../../../../stores/product/products'

    const fetchProductStore = useProductStore()
    const fetchProductAttachmentStore = useProductAttachmentStore()
    await fetchProductAttachmentStore.fetch()
    const isError = ref(false)
    const violations = ref([])
    const fichiersFields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
    const productAttachment = computed(() =>
        fetchProductAttachmentStore.productAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(),
            url: attachment.url
        })))
    const treeData = computed(() => ({
        children: productAttachment.value,
        icon: 'folder',
        id: 1,
        label: `Attachments (${productAttachment.value.length})`
    }))
    async function updateFichiers(value) {
        const productsId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: formData.get('category'),
            file: formData.get('file'),
            product: `/api/products/${productsId}`
        }
        try {
            await fetchProductAttachmentStore.ajout(data)
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
    <div>
        <AppCardShow
            id="addFichiers"
            :fields="fichiersFields"
            @update="updateFichiers(fetchProductStore.product)"/>
        <div v-if="isError" class="alert alert-danger" role="alert">
            <div v-for="violation in violations" :key="violation">
                <li>{{ violation.message }}</li>
            </div>
        </div>
        <MyTree :node="treeData"/>
    </div>
</template>

