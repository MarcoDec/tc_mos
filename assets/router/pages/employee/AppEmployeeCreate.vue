<script setup>
    import {computed, ref} from 'vue-demi'
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import {useEmployeesStore} from '../../../stores/employee/employees'
    import useOptions from '../../../stores/option/options'

    const props = defineProps({
        currentCompany: {required: true, type: String},
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })

    const storeEmployeesList = useEmployeesStore()
    let violations = []
    let success = []
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)

    const fecthOptionsCompany = useOptions('companies')
    await fecthOptionsCompany.fetchOp()
    const optionsCompany = computed(() =>
        fecthOptionsCompany.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    console.log('optionsCompany', optionsCompany)

    const levelOptions = [
        {text: 'Opérateur', value: '1'},
        {text: 'Animateur', value: '2'},
        {text: 'Responsable', value: '3'},
        {text: 'Directeur', value: '4'}
    ]

    const fields = computed(() => [
        {label: 'Prenom ', name: 'name', type: 'text'},
        {label: 'Nom', name: 'surname', type: 'text'},
        {label: 'Initiales', name: 'initials', type: 'text'},
        {label: 'Compte utilisateur', name: 'userEnabled', type: 'boolean'},
        {label: 'Identifiant', name: 'username', type: 'text'},
        {label: 'mot de passe', name: 'password', type: 'text'},
        {label: 'Mot de passe simple', name: 'plainPassword', type: 'text'},
        {label: 'Company',
         name: 'company',
         options: {
             label: value =>
                 optionsCompany.value.find(option => option.type === value)?.text ?? null,
             options: optionsCompany.value
         },
         modelValue: props.currentCompany,
         type: 'select'},
        {
            label: 'Niveau',
            name: 'level',
            options: {
                label: value =>
                    levelOptions.find(option => option.type === value)?.text ?? null,
                options: levelOptions
            },
            type: 'select'
        }
    ])

    const employeeData = {}
    function employeeForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(employeeData, key)) {
            if (typeof value[key] === 'object') {
                if (value[key].value !== undefined) {
                    const inputValue = parseFloat(value[key].value)
                    employeeData[key] = {...employeeData[key], value: inputValue}
                }
                if (value[key].code !== undefined) {
                    const inputCode = value[key].code
                    employeeData[key] = {...employeeData[key], code: inputCode}
                }
            } else {
                employeeData[key] = value[key]
            }
        } else {
            employeeData[key] = value[key]
        }
        console.log('employeeData', employeeData)
    }

    async function employeeFormCreate(){
        try {
            const employee = {
                embRoles: employeeData?.level || '',
                initials: employeeData?.initials || '',
                name: employeeData?.name || '',
                password: employeeData?.password || '',
                plainPassword: employeeData?.plainPassword || '',
                surname: employeeData?.surname || '',
                userEnabled: employeeData?.userEnabled || false,
                username: employeeData?.username || '',
                company: employeeData?.company || props.currentCompany
            }
            console.log('employee', employee)
            await storeEmployeesList.addEmployee(employee)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'employée crée'
        } catch (error) {
            violations = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
            console.log('violations', violations)
        }
    }
</script>

<template>
    <AppModal :id="modalId" class="four" :title="title">
        <AppFormJS id="employee" :fields="fields" @update:model-value="employeeForm"/>
        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
            <li>{{ violations }}</li>
        </div>
        <div v-if="isCreatedPopupVisible" class="alert alert-success" role="alert">
            <li>{{ success }}</li>
        </div>
        <template #buttons>
            <AppBtn
                variant="success"
                label="Créer"
                data-bs-toggle="modal"
                :data-bs-target="target"
                @click="employeeFormCreate">
                Créer
            </AppBtn>
        </template>
    </AppModal>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
