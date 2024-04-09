<script setup>
    import {computed, ref} from 'vue'
    import generateEmployee from '../../../../../stores/hr/employee/employee'
    import {useEmployeeStore} from '../../../../../stores/hr/employee/employees'

    const emit = defineEmits(['update', 'update:modelValue'])
    const isError2 = ref(false)
    const violations2 = ref([])

    const fetchEmployeeStore = useEmployeeStore()
    const optionsEmployee = computed(() =>
        fetchEmployeeStore.items.map(op => {
            const text = op.name
            const value = op['@id']
            return {text, value}
        }))
    const optionsTeams = computed(() =>
        fetchEmployeeStore.teams.map(team => {
            const text = team.name
            const value = team['@id']
            return {text, value}
        }))
    const productionFields = [

        {
            big: true,
            label: 'Manager *',
            name: 'manager',
            options: {
                label: value =>
                    optionsEmployee.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsEmployee.value
            },
            type: 'select'
        },
        {
            label: 'Equipe',
            name: 'team',
            options: {
                label: value =>
                    optionsTeams.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsTeams.value
            },
            type: 'select'
        }
    ]

    async function updateProduction(value) {
        const form = document.getElementById('addProduction')
        const formData = new FormData(form)
        const data = {
            manager: val.value,
            team: formData.get('team')
        }
        isError2.value = false
        violations2.value = []
        try {
            const item = generateEmployee(value)
            await item.updateProd(data)
            isError2.value = false
        } catch (error) {
            const err = {
                message: error
            }
            violations2.value.push(err)
            isError2.value = true
        }
    }
    const val = ref(Number(fetchEmployeeStore.employee.manager))
    async function input(value) {
        val.value = value.manager
        emit('update:modelValue', val.value)
    }
</script>

<template>
    <div class="container">
        <AppCardShow
            id="addProduction"
            :fields="productionFields"
            :component-attribute="fetchEmployeeStore.employee"
            @update="updateProduction(fetchEmployeeStore.employee)"
            @update:model-value="input"/>
        <div v-if="isError2" class="alert alert-danger" role="alert">
            <ul v-for="violation in violations2" :key="violation">
                <li>{{ violation.message }}</li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
    .container {
        height: fit-content;
    }
</style>

