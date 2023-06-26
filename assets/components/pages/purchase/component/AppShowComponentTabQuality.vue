<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../MyTree.vue'
    import {useComponentAttachmentStore} from '../../../../stores/component/componentAttachment'
    import {useComponentListStore} from '../../../../stores/component/components'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    const emit = defineEmits(['update:modelValue'])
    const localData = ref({})
    const isError2 = ref(false)
    const violations2 = ref([])
    const useFetchComponentStore = useComponentListStore()
    const fetchComponentAttachment = useComponentAttachmentStore()
    await fetchComponentAttachment.fetchByComponent(idComponent)
    //await useFetchComponentStore.fetchOne(idComponent)
    const Qualitéfields = [
        {label: 'rohs ', name: 'rohs', type: 'boolean'},
        {label: 'rohsAttachment', name: 'rohsAttachment', type: 'file'},
        {label: 'reach', name: 'reach', type: 'boolean'},
        {label: 'reachAttachment', name: 'reachAttachment', type: 'file'},
        {label: 'Notation qualité *', name: 'quality', type: 'rating'}
    ]
    const rohsValue = computed(() => useFetchComponentStore.component.rohs)
    const reachValue = computed(() => useFetchComponentStore.component.reach)
    const attachmentByCategory = computed(() =>
        fetchComponentAttachment.componentAttachments.filter(
            attachment => attachment.category.includes('rohs')
        ))
    const componentAttachmentByCategory = computed(() =>
        attachmentByCategory.value.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))

    const treeDataByRohs = computed(() => {
        const data = {
            children: componentAttachmentByCategory.value,
            icon: 'folder',
            id: 1,
            label: `Attachments Rohs (${componentAttachmentByCategory.value.length})`
        }
        return data
    })

    const attachmentByCategoryReach = computed(() =>
        fetchComponentAttachment.componentAttachments.filter(
            attachment => attachment.category.includes('reach')
        ))
    const componentAttachmentByCategoryReach = computed(() =>
        attachmentByCategoryReach.value.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))

    const treeDataByReach = computed(() => {
        const data = {
            children: componentAttachmentByCategoryReach.value,
            icon: 'folder',
            id: 1,
            label: `Attachments Reach (${componentAttachmentByCategoryReach.value.length})`
        }
        return data
    })
    async function input(value) {
        localData.value = value
        emit('update:modelValue', localData.value)
        console.log('input', localData.value)
        // const data = {
        //     quality: val.value
        // }
        // await useFetchComponentStore.updateQuality(data, idComponent)
        // await useFetchComponentStore.fetchOne(idComponent)
    }
    async function updateQuality() {
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)
        const data = {
            quality: localData.value.quality,
            reach: localData.value.reach,
            rohs: localData.value.rohs
        }
        if (localData.value.rohs) {
            const dataFichierRohs = {
                category: 'rohs',
                component: `/api/components/${idComponent}`,
                file: formData.get('rohsAttachment')
            }
            try {
                await fetchComponentAttachment.ajout(dataFichierRohs)
                await fetchComponentAttachment.fetchByComponent(idComponent)

                isError2.value = false
            } catch (error) {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
        if (reachValue.value) {
            const dataFichierReach = {
                category: 'reach',
                component: `/api/components/${idComponent}`,
                file: formData.get('reachAttachment')
            }
            try {
                await fetchComponentAttachment.ajout(dataFichierReach)
                await fetchComponentAttachment.fetchByComponent(idComponent)

                isError2.value = false
            } catch (error) {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
        await useFetchComponentStore.updateQuality(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
        // eslint-disable-next-line require-atomic-updates
        rohsValue.value = useFetchComponentStore.component.rohs
        // eslint-disable-next-line require-atomic-updates
        reachValue.value = useFetchComponentStore.component.reach
    }
</script>

<template>
    <div>
        <AppCardShow
            id="addQualite"
            :fields="Qualitéfields"
            :component-attribute="useFetchComponentStore.component"
            @update="updateQuality(useFetchComponentStore.component)"
            @update:model-value="input"/>
        <div v-if="isError2" class="alert alert-danger" role="alert">
            <div v-for="violation in violations2" :key="violation">
                <li>{{ violation.message }}</li>
            </div>
        </div>
        <MyTree v-show="rohsValue" :node="treeDataByRohs"/>
        <MyTree v-show="reachValue" :node="treeDataByReach"/>
    </div>
</template>
