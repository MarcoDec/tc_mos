<script setup>
    //import Tree from 'vue3-tree'
    import {computed} from 'vue'
    import generateEmployee from '../../../stores/employee/employee'
    import {useEmployeeAttachmentStore} from '../../../stores/employee/employeeAttachements'
    import {useEmployeeStore} from '../../../stores/employee/employees'
    import useOptions from '../../../stores/option/options'

    const fecthOptions = useOptions('countries')
    await fecthOptions.fetch()

    const fetchEmployeeContactStore = useEmployeeStore()
    const fetchEmployeeAttachementStore = useEmployeeAttachmentStore()
    await fetchEmployeeContactStore.fetch()
    await fetchEmployeeContactStore.fetchTeams()
    await fetchEmployeeAttachementStore.fetch()
    console.log('fetchEmployeeContactStore', fetchEmployeeContactStore)
    console.log('AttachementStore', fetchEmployeeAttachementStore.employeeAttachment)
    console.log('fetchTeams', fetchEmployeeContactStore.teams)
    const optionsCountries = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))
    // const attachement = computed(() =>
    //   fetchEmployeeAttachementStore.employeeAttachment.map((attachemnt) => {
    //   console.log('list',attachement)
    //   return attachement.category
    //  })
    // )
    // console.log('attachement', attachement);
    //  const data = ref([
    //       {
    //         id: 1,
    //         label: "Animal"
    //       },
    //       {
    //         id: 6,
    //         label: "People",
    //       },
    //     ]);
    //     const searchText = ref("");
    //     const onNodeExpanded = (node, state) => {
    //       console.log("state: ", state);
    //       console.log("node: ", node);
    //     };

    //     const onUpdate = (nodes) => {
    //       console.log("nodes:", nodes);
    //     };

    //     const onNodeClick = (node) => {
    //       console.log('click ici',node);
    //     };
    const optionsTeams = computed(() =>
        fetchEmployeeContactStore.teams.map(team => {
            const text = team.name
            const value = team.id
            const optionList = {text, value}
            return optionList
        }))
    const options = [
        {text: 'married', value: 'married'},
        {text: 'single', value: 'single'},
        {text: 'windowed', value: 'windowed'}
    ]
    const optionsGenre = [
        {text: 'female', value: 'female'},
        {text: 'male', value: 'male'}
    ]
    const Productionfields = [
        {
            label: 'équipe',
            name: 'équipe',
            options: {
                label: value =>
                    optionsTeams.value.find(option => option.type === value)?.text ?? null,
                options: optionsTeams.value
            },
            type: 'select'
        }
    ]
    const Accèsfields = [
        {label: 'identifiant', name: 'identifiant', type: 'text'},
        {label: 'Mot de passe', name: 'MotDePasse', type: 'text'}
    ]
    const Fichiersfields = [{label: 'Fichier', name: 'file', type: 'file'}]
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
        {label: 'Date arrivée', name: 'timeCard', type: 'date'}
    ]

    async function update(value) {
        const form = document.getElementById('addInfo')
        const formData = new FormData(form)
        const data = {
            address: {
                address: formData.get('getAddress'),
                address2: '',
                city: formData.get('getCity'),
                country: formData.get('getCountry'),
                email: formData.get('getEmail'),
                phoneNumber: formData.get('getPhone'),
                zipCode: formData.get('getPostal')
            },
            birthday: formData.get('getBrith'),
            //entryDate: "2021-01-12",
            gender: formData.get('gender'),
            initials: formData.get('initials'),
            levelOfStudy: formData.get('levelOfStudy'),
            //manager: "/api/employees/3",
            //name: "Super",
            //notes: "Lorem ipsum dolor sit am",
            situation: formData.get('situation'),
            socialSecurityNumber: formData.get('socialSecurityNumber')
            //surname: "Roosevelt",
        }
        const item = generateEmployee(value)

        await item.update(data)
    }
    function updateFichiers(value) {
        const employeeId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: 'doc',
            employee: `/api/employees/${employeeId}`,
            file: formData.get('file')
        }

        fetchEmployeeAttachementStore.ajout(data)
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
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab
            id="gui-start-Informations"
            title="Informations personelles"
            icon="circle-info"
            tabs="gui-start">
            <AppCardShow
                id="addInfo"
                :fields="Informationsfields"
                :component-attribute="fetchEmployeeContactStore.employee"
                @update="update(fetchEmployeeContactStore.employee)"/>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppCardShow id="addContacts"/>
        </AppTab>
        <AppTab
            id="gui-start-droits"
            title="Droits"
            icon="clipboard-check"
            tabs="gui-start">
            <AppCardShow id="addDroits"/>
        </AppTab>
        <AppTab
            id="gui-start-accés"
            title="Accés"
            icon="arrow-right-to-bracket"
            tabs="gui-start">
            <AppCardShow id="addAccés" :fields="Accèsfields"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(fetchEmployeeContactStore.employee)"/>
            <!-- <Tree
        :nodes="data"
        :search-text="searchText"
        :use-checkbox="false"
        :use-icon="true"
        use-row-delete
        show-child-count
        @nodeExpanded="onNodeExpanded"
        @update:nodes="onUpdate"
        @nodeClick="onNodeClick"
      /> -->
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="industry"
            tabs="gui-start">
            <AppCardShow id="addProduction" :fields="Productionfields"/>
        </AppTab>
    </AppTabs>
</template>
