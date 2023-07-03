<script setup>
    import {computed, ref} from 'vue'
    import AppCardShow from '../../../AppCardShow.vue'
    import MyTree from '../../../MyTree.vue'
    import {useEmployeeAttachmentStore} from '../../../../stores/employee/employeeAttachements'
    import {useEmployeeStore} from '../../../../stores/employee/employees'

    const violations = ref([])
    const isError = ref(false)
    const fetchEmployeeStore = useEmployeeStore()
    const fetchEmployeeAttachementStore = useEmployeeAttachmentStore()
    await fetchEmployeeAttachementStore.fetchOne()
    const Fichiersfields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
    const employeeAttachment = computed(() =>
        fetchEmployeeAttachementStore.employeeAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))
    const treeData = computed(() => ({
        children: employeeAttachment.value,
        icon: 'folder',
        id: 1,
        label: `Attachments (${employeeAttachment.value.length})`
    }))
    async function updateFichiers(value) {
        const employeeId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: formData.get('category'),
            employee: `/api/employees/${employeeId}`,
            file: formData.get('file')
        }
        try {
            await fetchEmployeeAttachementStore.ajout(data)
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
            component-attribute=""
            :fields="Fichiersfields"
            @update="updateFichiers(fetchEmployeeStore.employee)"/>
        <div v-if="isError" class="alert alert-danger" role="alert">
            <ul v-for="violation in violations" :key="violation">
                <li>{{ violation.message }}</li>
            </ul>
        </div>
        <MyTree :node="treeData"/>
    </div>
</template>

