<script setup>
    import {computed, ref} from 'vue'
    import AppCardShow from '../../../AppCardShow.vue'
    import MyTree from '../../../MyTree.vue'
    import {useComponentAttachmentStore} from '../../../../stores/component/componentAttachment'
    import {useComponentListStore} from '../../../../stores/component/components'
    import {useParametersStore} from '../../../../stores/parameters/parameters'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    //region Déclaration des variables
    const currentComponentData = ref({})
    const isError = ref(false)
    const violations = ref([])
    const fichiersFields = ref([])
    const rootFolder = ref({})
    const folders = ref([])
    const foldersId = ref([])
    const files = ref([])
    const parametersStore = useParametersStore()
    const fetchComponentAttachment = useComponentAttachmentStore()
    const useFetchComponentStore = useComponentListStore()
    const componentAttachments = computed(() =>
        fetchComponentAttachment.componentAttachments.map(attachment => ({
            category: attachment.category,
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))
    //endregion
    //region Définition des fonctions
    function getChildrenFolders(node) {
        return folders.value.filter(folder => folder.level === node.level + 1 && folder.category.search(node.category) > -1)
    }
    function getChildrenFiles(node) {
        return files.value.filter(file => file.category === node.category)
    }
    function getAllPaths(category) {
        const splitted = category.split('/')
        const arr = []
        for (let i = 0; i < splitted.length; i++) {
            if (i === 0) {
                arr.push(splitted[i])
            } else {
                arr.push(`${arr[i - 1]}/${splitted[i]}`)
            }
        }
        return arr
    }
    async function initializeData() {
        currentComponentData.value = useFetchComponentStore.component
        const folderList = parametersStore.parameter.value.split(',').map(x => ({
            text: x,
            value: x
        }))
        await fetchComponentAttachment.fetchByComponent(idComponent)
        fichiersFields.value = [
            {label: 'Catégorie', name: 'category', options: {options: folderList}, type: 'select'},
            {label: 'Fichier', name: 'file', type: 'file'}
        ]
        //Etape 1 - nodes = noeuds de type fichier
        files.value = componentAttachments.value
        files.value.forEach(file => {
            getAllPaths(file.category).forEach(folderPath => {
                if (foldersId.value.indexOf(folderPath) === -1) {
                    foldersId.value.push(folderPath)
                }
            })
        })
        //Etape 2 - nodes = noeuds de type dossier
        folders.value = foldersId.value.map(folder =>
            ({
                category: folder,
                children: [],
                icon: 'folder',
                id: folder,
                label: folder.split('/')[folder.split('/').length - 1],
                level: folder.split('/').length
            }))
        //Etape 3 - nodes création de l'arborescence sur base folders
        //region recupération de la profondeur maximale
        const maxLevel = ref(0)
        // eslint-disable-next-line array-callback-return
        folders.value.map(folder => {
            if (folder.level > maxLevel.value) maxLevel.value = folder.level
        })
        //endregion
        rootFolder.value = {
            category: '',
            children: [],
            icon: 'folder',
            id: 1,
            label: `Pièces jointes (${componentAttachments.value.length})`,
            level: 0
        }
        folders.value.push(rootFolder.value)
        // eslint-disable-next-line array-callback-return
        folders.value.map(folder => {
            folder.children = getChildrenFolders(folder)
        })
        //Etape 4 - nodes ajout des noeuds fichier à l'arborescence
        // eslint-disable-next-line array-callback-return
        folders.value.map(folder => {
            const filesInFolder = getChildrenFiles(folder)
            // eslint-disable-next-line array-callback-return
            filesInFolder.forEach(file => folder.children.push(file))
        })
    }
    async function updateFichiers(value) {
        const componentId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)
        const data = {
            category: formData.get('category'),
            component: `/api/components/${componentId}`,
            file: formData.get('file')
        }
        try {
            await fetchComponentAttachment.ajout(data)
            isError.value = false
        } catch (error) {
            const err = {
                message: error
            }
            violations.value.push(err)
            isError.value = true
        }
        await initializeData()
    }
    //endregion
    //region Chargement des données / Variables
    await parametersStore.getByName('COMPONENT_ATTACHMENT_CATEGORIES')
    await initializeData()
    //endregion
    // const route = useRoute()
    // const idComponent = route.params.id_component
    // await fetchComponentAttachment.fetchOne(idComponent)
    // //await useFetchComponentStore.fetchOne(idComponent)
    // const Fichiersfields = [
    //     {label: 'Categorie', name: 'category', type: 'text'},
    //     {label: 'Fichier', name: 'file', type: 'file'}
    // ]
    // const treeData = computed(() => {
    //     const data = {
    //         children: componentAttachment.value,
    //         icon: 'folder',
    //         id: 1,
    //         label: `Attachments (${componentAttachment.value.length})`
    //     }
    //     return data
    // })
    // async function updateFichiers() {
    //     const form = document.getElementById('addFichiers')
    //     const formData = new FormData(form)
    //     const data = {
    //         category: formData.get('category'),
    //         component: `/api/components/${idComponent}`,
    //         file: formData.get('file')
    //     }
    //
    //     try {
    //         await fetchComponentAttachment.ajout(data)
    //         await fetchComponentAttachment.fetchOne(idComponent)
    //
    //         isError.value = false
    //     } catch (error) {
    //         const err = {
    //             message: error
    //         }
    //         violations.value.push(err)
    //         isError.value = true
    //     }
    // }
</script>

<template>
    <div>
        <AppCardShow
            id="addFichiers"
            :fields="fichiersFields"
            :component-attribute="currentComponentData"
            title="Ajouter un nouveau Fichier"
            @update="updateFichiers(useFetchComponentStore.component)"/>
        <div v-if="isError" class="alert alert-danger" role="alert">
            <ul>
                <li v-for="violation in violations" :key="violation">
                    {{ violation.message }}
                </li>
            </ul>
        </div>
        <MyTree :node="rootFolder"/>
    </div>
</template>

