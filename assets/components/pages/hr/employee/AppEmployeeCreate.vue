<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppFormJS from '../../../form/AppFormJS.js'
    import {useEmployeesStore} from '../../../../stores/employee/employees'
    import useOptions from '../../../../stores/option/options'

    const props = defineProps({
        currentCompany: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })
    const emits = defineEmits(['created'])

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

    const empCompany = {company: props.currentCompany}

    const levelOptions = [
        {text: 'Opérateur', value: 'ROLE_LEVEL_OPERATOR'},
        {text: 'Animateur', value: 'ROLE_LEVEL_ANIMATOR'},
        {text: 'Responsable', value: 'ROLE_LEVEL_MANAGER'},
        {text: 'Directeur', value: 'ROLE_LEVEL_DIRECTOR'}
    ]

    const fields = computed(() => [
        {label: 'Prenom ', name: 'name', type: 'text'},
        {label: 'Nom', name: 'surname', type: 'text'},
        {label: 'Initiales', name: 'initials', type: 'text'},
        // {label: 'Compte utilisateur', name: 'userEnabled', type: 'boolean'},
        // {label: 'Identifiant', name: 'username', type: 'text'},
        // {label: 'Mot de passe', name: 'plainPassword', type: 'text'},
        {label: 'Company',
         name: 'company',
         options: {
             label: value =>
                 optionsCompany.value.find(option => option.type === value)?.text ?? null,
             options: optionsCompany.value
         },
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
        },
        {
            label: 'Manager',
            name: 'manager',
            type: 'multiselect-fetch',
            api: '/api/employees',
            filteredProperty: 'getterFilter',
            max: 1,
            permanentFilters: [
                {field: 'company', value: props.currentCompany}
            ]
        }
    ])

    const employeeData = ref({})
    function employeeForm(value) {
        Object.keys(value).forEach(key => {
            if (Object.prototype.hasOwnProperty.call(employeeData.value, key)) {
                if (typeof value[key] === 'object') {
                    if (typeof value[key].value !== 'undefined') {
                        const inputValue = parseFloat(value[key].value)
                        employeeData.value[key] = {...employeeData.value[key], value: inputValue}
                    }
                    if (typeof value[key].code !== 'undefined') {
                        const inputCode = value[key].code
                        employeeData.value[key] = {...employeeData.value[key], code: inputCode}
                    }
                } else {
                    employeeData.value[key] = value[key]
                }
            } else {
                employeeData.value[key] = value[key]
            }
        })
    }

    async function employeeFormCreate(){
        try {
            const employee = {
                embRoles: [employeeData.value.level],
                initials: employeeData.value.initials ?? '',
                name: employeeData.value.name ?? '',
                // password: employeeData.value.password ?? '',
                // plainPassword: employeeData.value.plainPassword ?? '',
                surname: employeeData.value.surname ?? '',
                // userEnabled: employeeData.value.userEnabled ?? false,
                // username: employeeData.value.username ?? '',
                company: employeeData.value.company ?? props.currentCompany,
                manager: employeeData.value.manager ?? null
            }
            await storeEmployeesList.addEmployee(employee)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'employée crée'
            emits('created')
        } catch (error) {
            violations = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
        }
    }
</script>

<template>
    <AppModal :id="modalId" class="four" title="Création d'un nouvel employé">
        <AppFormJS id="employee" :fields="fields" :model-value="empCompany" @update:model-value="employeeForm"/>
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
