<script setup>
    import generateEmployee from '../../../../../stores/hr/employee/employee'
    import {useEmployeeStore} from '../../../../../stores/hr/employee/employees'
    const fetchEmployeeStore = useEmployeeStore()
    const optionsGenre = [
        {text: 'female', value: 'female'},
        {text: 'male', value: 'male'}
    ]
    const options = [
        {text: 'married', value: 'married'},
        {text: 'single', value: 'single'},
        {text: 'windowed', value: 'windowed'}
    ]
    const generalFields = [
        {label: 'Nom', name: 'surname', type: 'text'},
        {label: 'Prenom', name: 'name', type: 'text'},
        {
            label: 'Genre',
            name: 'gender',
            options: {
                label: value =>
                    optionsGenre.find(option => option.type === value)?.text ?? null,
                options: optionsGenre
            },
            type: 'select'
        },
        {
            label: 'Situation',
            name: 'situation',
            options: {
                label: value =>
                    options.find(option => option.type === value)?.text ?? null,
                options
            },
            type: 'select'
        },
        {label: 'Email', name: 'getEmail', type: 'text'},
        {label: 'Date arrivée', name: 'getEntryDate', type: 'date'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]
    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const data = {
            name: formData.get('name'),
            surname: formData.get('surname'),
            gender: formData.get('gender'),
            situation: formData.get('situation'),
            address: {
                email: formData.get('getEmail')
            },
            notes: formData.get('notes') ? formData.get('notes') : null,
            entryDate: formData.get('getEntryDate')
        }
        const item = generateEmployee(value)
        await item.update(data)
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="generalFields"
        :component-attribute="fetchEmployeeStore.employee"
        @update="updateGeneral"/>
</template>
