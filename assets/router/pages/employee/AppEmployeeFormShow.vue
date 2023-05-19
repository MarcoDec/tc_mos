<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../components/MyTree.vue'
    import generateEmployee from '../../../stores/employee/employee'
    import generateEmployeeContact from '../../../stores/employee/employeeContact'
    import {useEmployeeAttachmentStore} from '../../../stores/employee/employeeAttachements'
    import {useEmployeeContactsStore} from '../../../stores/employee/employeeContacts'
    import {useEmployeeStore} from '../../../stores/employee/employees'
    import useOptions from '../../../stores/option/options'

    const emit = defineEmits(['update', 'update:modelValue'])
    const isError = ref(false)
    const isError2 = ref(false)
    const violations = ref([])
    const violations2 = ref([])
    const fecthOptions = useOptions('countries')
    const fecthCompanyOptions = useOptions('companies')
    await fecthOptions.fetchOp()
    await fecthCompanyOptions.fetchOp()

    const fetchEmployeeStore = useEmployeeStore()
    const fetchEmployeeContactsStore = useEmployeeContactsStore()
    const fetchEmployeeAttachementStore = useEmployeeAttachmentStore()
    await fetchEmployeeStore.fetch()
    await fetchEmployeeStore.fetchAll()
    await fetchEmployeeStore.fetchTeams()

    await fetchEmployeeAttachementStore.fetch()
    const emplId = Number(fetchEmployeeStore.employee.id)
    await fetchEmployeeContactsStore.fetchContactsEmpl(emplId)

    const employeeAttachment = computed(() =>
        fetchEmployeeAttachementStore.employeeAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: employeeAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${employeeAttachment.value.length})`
        }
        return data
    })
    console.log('itemss==', fetchEmployeeStore)
    const optionsEmployee = computed(() =>
        fetchEmployeeStore.items.map(op => {
            const text = op.name
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsCompany = computed(() =>
        fecthCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsCountries = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))
    const optionsTeams = computed(() =>
        fetchEmployeeStore.teams.map(team => {
            const text = team.name
            const value = team['@id']
            const optionList = {text, value}
            return optionList
        }))
    console.log('optionsTeams', optionsTeams)
    const options = [
        {text: 'married', value: 'married'},
        {text: 'single', value: 'single'},
        {text: 'windowed', value: 'windowed'}
    ]
    const optionsGenre = [
        {text: 'female', value: 'female'},
        {text: 'male', value: 'male'}
    ]
    const Géneralitésfields = [{label: 'Note', name: 'notes', type: 'textarea'}]

    const Productionfields = [
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
        },
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
        }
    ]
    const Accèsfields = [
        {label: 'Identifiant', name: 'username', type: 'text'},
        {label: 'Mot de passe', name: 'plainPassword', type: 'text'},
        {label: 'Badge', name: 'timeCard', type: 'text'},
        {
            label: 'Compagnie',
            name: 'company',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'select'
        },
        {label: 'Activation', name: 'userEnabled', type: 'boolean'}
    ]
    const Fichiersfields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
    const Droitsfields = [{label: 'Role', name: 'embRoles', type: 'text'}]

    const Contactsfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Prenom', name: 'surname', type: 'text'},
        {label: 'Telepone', name: 'phone', type: 'text'}
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
        console.log('dataEmpl', data)
        const item = generateEmployee(value)

        await item.updateHr(data)
    }
    async function updateContact(value) {
        const form = document.getElementById('addContacts')
        const formData = new FormData(form)
        const data = {
            employee: `/api/employees/${emplId}`,
            name: formData.get('name'),
            phone: formData.get('phone'),
            surname: formData.get('surname')
        }

        const item = generateEmployeeContact(value)
        await item.updateContactEmp(data)
    }
    async function updateGeneral(value) {
        // const employeeId = Number(value['@id'].match(/\d+/)[0])

        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const data = {
            notes: formData.get('notes') ? formData.get('notes') : null
        }
        const item = generateEmployee(value)

        await item.update(data)
        //await fetchEmployeeStore.update(data, employeeId)
    }
    async function updateDroits(value) {
        // const employeeId = Number(value['@id'].match(/\d+/)[0])

        const form = document.getElementById('addDroits')
        const formData = new FormData(form)
        const data = {
            embRoles: [formData.get('embRoles')]
        }
        const item = generateEmployee(value)

        await item.updateIt(data)
    }
    async function updateAcces(value) {
        const form = document.getElementById('addAccés')
        const formData = new FormData(form)
        const data = {
            company: formData.get('company'),
            plainPassword: formData.get('plainPassword'),
            timeCard: formData.get('timeCard'),
            userEnabled: JSON.parse(formData.get('userEnabled')),
            username: formData.get('username')
        }
        const item = generateEmployee(value)
        await item.updateIt(data)
    }
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
    const val = ref(Number(fetchEmployeeStore.employee.manager))
    async function input(value) {
        val.value = value.manager
        emit('update:modelValue', val.value)
    }
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
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addGeneralites"
                :fields="Géneralitésfields"
                :component-attribute="fetchEmployeeStore.employee"
                @update="updateGeneral(fetchEmployeeStore.employee)"/>
        </AppTab>
        <AppTab
            id="gui-start-Informations"
            title="Informations personelles"
            icon="circle-info"
            tabs="gui-start">
            <AppCardShow
                id="addInfo"
                :fields="Informationsfields"
                :component-attribute="fetchEmployeeStore.employee"
                @update="update(fetchEmployeeStore.employee)"/>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppCardShow
                v-for="item in fetchEmployeeContactsStore.employeeContacts"
                id="addContacts"
                :key="item.id"
                :fields="Contactsfields"
                :component-attribute="item"
                @update="updateContact(item)"/>
        </AppTab>
        <AppTab
            id="gui-start-droits"
            title="Droits"
            icon="clipboard-check"
            tabs="gui-start">
            <AppCardShow
                id="addDroits"
                :fields="Droitsfields"
                :component-attribute="fetchEmployeeStore.employee"
                @update="updateDroits(fetchEmployeeStore.employee)"/>
        </AppTab>
        <AppTab
            id="gui-start-accés"
            title="Accés"
            icon="arrow-right-to-bracket"
            tabs="gui-start">
            <AppCardShow
                id="addAccés"
                :fields="Accèsfields"
                :component-attribute="fetchEmployeeStore.employee"
                @update="updateAcces(fetchEmployeeStore.employee)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(fetchEmployeeStore.employee)"/>
            <div v-if="isError" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <MyTree :node="treeData"/>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="industry"
            tabs="gui-start">
            <AppCardShow
                id="addProduction"
                :fields="Productionfields"
                :component-attribute="fetchEmployeeStore.employee"
                @update="updateProduction(fetchEmployeeStore.employee)"
                @update:model-value="input"/>
            <div v-if="isError2" class="alert alert-danger" role="alert">
                <div v-for="violation in violations2" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
        </AppTab>
    </AppTabs>
</template>
