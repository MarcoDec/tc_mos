<script setup>
    import {computed, ref} from 'vue'
    import AppCardShow from '../AppCardShow.vue'
    import MyTree from '../MyTree.vue'
    import {useParametersStore} from '../../stores/parameters/parameters'

    const props = defineProps({
        attachmentElementLabel: {required: true, type: String},
        elementApiUrl: {required: true, type: String},
        elementAttachmentStore: {required: true, type: Object},
        elementId: {required: true, type: Number},
        elementParameterName: {required: true, type: String},
        elementStore: {required: true, type: Object}
    })
    //region Déclaration des variables
    const currentElementData = ref({})
    const isError = ref(false)
    const violations = ref([])
    const fichiersFields = ref([])
    const rootFolder = ref({})
    const folders = ref([])
    const foldersId = ref([])
    const files = ref([])
    const parametersStore = useParametersStore()
    //const fetchElementAttachment = useComponentAttachmentStore()
    //const fetchElementAttachment = props.elementAttachmentStore
    //const useFetchElementStore = useComponentListStore()
    const elementAttachments = computed(() =>
        props.elementAttachmentStore.elementAttachments.map(attachment => ({
            category: attachment.category,
            color: '#43abd7',
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
        currentElementData.value = props.elementStore.component
        const folderList = parametersStore.parameter.value.split(',').map(x => ({
            text: x,
            value: x
        }))
        await props.elementAttachmentStore.fetchByElement(props.elementId)
        fichiersFields.value = [
            {label: 'Catégorie', name: 'category', options: {options: folderList}, type: 'select'},
            {label: 'Fichier', multiple: true, name: 'file', type: 'file'}
        ]
        //Etape 1 - nodes = noeuds de type fichier
        files.value = elementAttachments.value
        files.value.forEach(file => {
            getAllPaths(file.category).forEach(folderPath => {
                if (foldersId.value.indexOf(folderPath) === -1) {
                    foldersId.value.push(folderPath)
                }
            })
        })
        //Etape 2 - nodes = noeuds de type dossier
        folders.value = foldersId.value
            .filter(folder => folder !== '')
            .map(folder =>
                ({
                    category: folder,
                    children: [],
                    color: '#ffc107',
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
            color: '#ffc107',
            icon: 'folder',
            id: 1,
            label: `Pièces jointes (${elementAttachments.value.length})`,
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
    async function updateFichiers() {
        const ins = document.getElementById('addFichiers-file').files.length
        violations.value = []
        const results = []
        for (let x = 0; x < ins; x++) {
            const form = document.getElementById('addFichiers')
            const formData = new FormData(form)
            const data = {
                category: formData.get('category'),
                file: document.getElementById('addFichiers-file').files[x]
            }
            data[props.attachmentElementLabel] = props.elementApiUrl
            results.push(props.elementAttachmentStore.ajout(data))
        }
        isError.value = false
        Promise.allSettled(await results)
            .then(resultats => {
                // eslint-disable-next-line array-callback-return
                resultats.forEach(result => {
                    if (result.status === 'fulfilled') console.log('Fichier bien chargé')
                    else {
                        isError.value = true
                        const err = {
                            message: result.reason
                        }
                        violations.value.push(err)
                    }
                })
                initializeData()
            })
    }
    //endregion
    //region Chargement des données / Variables
    //await parametersStore.getByName('COMPONENT_ATTACHMENT_CATEGORIES')
    await parametersStore.getByName(props.elementParameterName)
    await initializeData()
    //endregion
</script>

<template>
    <div>
        <MyTree :node="rootFolder"/>
        <AppCardShow
            id="addFichiers"
            :fields="fichiersFields"
            :component-attribute="currentElementData"
            title="Ajouter des nouveaux Fichiers"
            @update="updateFichiers"/>
        <div v-if="isError" class="alert alert-danger" role="alert">
            <ul>
                <li v-for="violation in violations" :key="violation">
                    {{ violation.message }}
                </li>
            </ul>
        </div>
    </div>
</template>

