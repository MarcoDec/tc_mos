<script setup>
    import {computed, ref} from 'vue'
    import AppCardShow from '../../../AppCardShow.vue'
    import MyTree from '../../../MyTree.vue'
    import {useComponentAttachmentStore} from '../../../../stores/component/componentAttachment'
    import {useComponentListStore} from '../../../../stores/component/components'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    const isError = ref(false)
    const violations = ref([])
    const fetchComponentAttachment = useComponentAttachmentStore()
    const useFetchComponentStore = useComponentListStore()
    await fetchComponentAttachment.fetchOne(idComponent)
    //await useFetchComponentStore.fetchOne(idComponent)
    const Fichiersfields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
    const componentAttachment = computed(() =>
        fetchComponentAttachment.componentAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: componentAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${componentAttachment.value.length})`
        }
        return data
    })
    async function updateFichiers() {
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)
        const data = {
            category: formData.get('category'),
            component: `/api/components/${idComponent}`,
            file: formData.get('file')
        }

        try {
            await fetchComponentAttachment.ajout(data)
            await fetchComponentAttachment.fetchOne(idComponent)

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
            :fields="Fichiersfields"
            @update="updateFichiers(useFetchComponentStore.component)"/>
        <div v-if="isError" class="alert alert-danger" role="alert">
            <div v-for="violation in violations" :key="violation">
                <li>{{ violation.message }}</li>
            </div>
        </div>
        <MyTree :node="treeData"/>
    </div>
</template>

