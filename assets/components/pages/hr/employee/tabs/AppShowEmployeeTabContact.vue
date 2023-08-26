<script setup>
    import {computed, ref} from 'vue'
    import AppCollectionTable from '../../../../bootstrap-5/app-collection-table/AppCollectionTable.vue'
    import generateEmployeeContact from '../../../../../stores/hr/employee/employeeContact'
    import {useEmployeeContactsStore} from '../../../../../stores/hr/employee/employeeContacts'
    import {useEmployeeStore} from '../../../../../stores/hr/employee/employees'

    const emit = defineEmits(['error'])
    const fetchEmployeeContactsStore = useEmployeeContactsStore()
    const fetchEmployeeStore = useEmployeeStore()
    const emplId = Number(fetchEmployeeStore.employee.id)
    await fetchEmployeeContactsStore.fetchContactsEmpl(emplId)
    const isShow = ref(false)
    const itemsTable = computed(() =>
        fetchEmployeeContactsStore.employeeContacts.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))
    //Création des variables locales
    const isError2 = ref(false)
    const violations2 = ref([])
    //const hasNoContact = computed(() => fetchEmployeeContactsStore.employeeContacts.length === 0)
    const contactsFields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Prenom', name: 'surname', type: 'text'},
        {label: 'Telephone', name: 'phone', type: 'text'}
    ]
    async function ajout(inputValues) {
        const data = {
            employee: `/api/employees/${emplId}`,
            name: inputValues.name ?? '',
            phone: inputValues.phone ?? '',
            surname: inputValues.surname ?? ''
        }
        try {
            await fetchEmployeeContactsStore.ajout(data, emplId)
            isError2.value = false
        } catch (error) {
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
    }
    async function deleted(id) {
        console.log(id)
        await fetchEmployeeContactsStore.deleted(id)
    }
    async function updateContact(inputValues) {
        const dataUpdate = {
            employee: `/api/employees/${emplId}`,
            name: inputValues.name ?? '',
            phone: inputValues.phone ?? '',
            surname: inputValues.surname ?? ''
        }
        // const form = document.getElementById('addContacts')
        // const formData = new FormData(form)
        // const data = {
        //     employee: `/api/employees/${emplId}`,
        //     name: formData.get('name'),
        //     phone: formData.get('phone'),
        //     surname: formData.get('surname')
        // }
        try {
            const item = generateEmployeeContact(inputValues)
            await item.updateContactEmp(dataUpdate)
            isError2.value = false
        } catch (error) {
            await fetchEmployeeContactsStore.fetchContactsEmpl(emplId)
            itemsTable.value = fetchEmployeeContactsStore.employeeContacts.reduce(
                (acc, curr) => acc.concat(curr),
                []
            )
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
                emit('error', {violation: violations2.value})
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
        //const item = generateEmployeeContact(value)
        //await item.updateContactEmp(data)
    }
</script>

<template>
    <div v-if="fetchEmployeeContactsStore.isLoaded">
        <AppCollectionTable
            v-if="!isShow"
            id="addContacts"
            :allowed-actions="{add: true, cancel: true, search: false}"
            current-page=""
            :fields="contactsFields"
            first-page=""
            form=""
            :items="itemsTable"
            last-page=""
            next-page=""
            previous-page=""
            user=""
            @ajout="ajout"
            @deleted="deleted"
            @update="updateContact"/>
    </div>
</template>
