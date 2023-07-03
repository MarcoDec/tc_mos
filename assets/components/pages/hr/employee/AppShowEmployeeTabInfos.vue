<script setup>
    import {computed, ref} from 'vue'
    import generateEmployee from '../../../../stores/employee/employee'
    import {useEmployeeStore} from '../../../../stores/employee/employees'
    import useOptions from '../../../../stores/option/options'

    const fetchEmployeeStore = useEmployeeStore()
    const fetchCountries = useOptions('countries')
    await fetchCountries.fetchOp()
    const violations3 = ref([])
    const isError3 = ref(false)
    const options = [
        {text: 'married', value: 'married'},
        {text: 'single', value: 'single'},
        {text: 'windowed', value: 'windowed'}
    ]
    const optionsCountries = computed(() =>
        fetchCountries.options.map(op => {
            const text = op.text
            const value = op.id
            return {text, value}
        }))
    const optionsGenre = [
        {text: 'female', value: 'female'},
        {text: 'male', value: 'male'}
    ]
    const Informationsfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Prenom', name: 'surname', type: 'text'},
        {label: 'Email', name: 'getEmail', type: 'text'},
        {label: 'Complément d\'adresse', name: 'getAddress', type: 'text'},
        {label: 'Code postal', name: 'getPostal', type: 'text'},
        {label: 'Ville', name: 'getCity', type: 'text'},
        {
            label: 'Pays',
            name: 'getCountry',
            options: {
                label: value =>
                    optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCountries.value
            },
            type: 'select'
        },
        {label: 'Fax', name: 'getPhone', type: 'text'},
        {label: 'Naissance', name: 'getBrith', type: 'date'},
        {label: 'Initiales', name: 'initials', type: 'text'},
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
        {label: 'Numéro social', name: 'socialSecurityNumber', type: 'text'},
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
        {label: 'Étude', name: 'levelOfStudy', type: 'text'},
        {label: 'Date arrivée', name: 'getEntryDate', type: 'date'}
    ]
    async function update(value) {
        const form = document.getElementById('addInfo')
        const formData = new FormData(form)
        const data = {
            address: {
                address: formData.get('getAddress'),
                address2: null,
                city: formData.get('getCity'),
                country: formData.get('getCountry'),
                email: formData.get('getEmail') ? formData.get('getEmail') : null,
                phoneNumber: formData.get('getPhone'),
                zipCode: formData.get('getPostal')
            },
            birthday: formData.get('getBrith'),
            entryDate: formData.get('getEntryDate'),
            gender: formData.get('gender'),
            initials: formData.get('initials'),
            levelOfStudy: formData.get('levelOfStudy'),
            //manager: "/api/employees/3",
            name: formData.get('name'),
            //notes: "Lorem ipsum dolor sit am",
            situation: formData.get('situation'),
            socialSecurityNumber: formData.get('socialSecurityNumber'),
            surname: formData.get('surname')
        }
        try {
            const item = generateEmployee(value)

            await item.updateHr(data)
        } catch (error) {
            if (Array.isArray(error)) {
                violations3.value = error
                isError3.value = true
            } else {
                const err = {
                    message: error
                }
                violations3.value.push(err)
                isError3.value = true
            }
        }
    }
</script>

<template>
    <div>
        <AppCardShow
            id="addInfo"
            :fields="Informationsfields"
            :component-attribute="fetchEmployeeStore.employee"
            @update="update(fetchEmployeeStore.employee)"/>
        <div v-if="isError3" class="alert alert-danger" role="alert">
            <ul v-for="violation in violations3" :key="violation">
                <li>{{ violation.propertyPath }}: {{ violation.message }}</li>
            </ul>
        </div>
    </div>
</template>
