<script setup>
    import {computed, onMounted, ref} from 'vue'
    import {BModal} from 'bootstrap-vue-next'
    import VJstree from 'v-VJstree'
    import VueContext from 'vue-context'

    const props = defineProps({
        expirationDirs: {default: () => [], type: Array},
        // eslint-disable-next-line vue/no-boolean-default
        hasRight: {default: true, type: Boolean},
        rootDir: {default: 'upload', type: String},
        // eslint-disable-next-line vue/no-boolean-default
        toInitialize: {default: true, type: Boolean}
    })

    const loading = ref(true)
    const dir = ref([]) // File
    const key = ref(0)
    const children = ref(null)
    const menu = ref(VueContext)
    const currentExpirationDate = ref(null)
    const currentPath = ref(null)
    const errorUnique = ref('')
    const errors = ref({})
    const previewSrc = ref('')
    const formExpirableFile = ref(HTMLFormElement)
    const formFile = ref(HTMLFormElement)
    const formFolder = ref(HTMLFormElement)
    const formRenameFolder = ref(HTMLFormElement)
    const formUpdate = ref(HTMLFormElement)
    const inputFile = ref(HTMLInputElement)
    const addExpirableFileModal = ref(BModal)
    const addFolderModal = ref(BModal)
    //const errorModal = ref(BModal)
    const previewModal = ref(BModal)
    const removeModal = ref(BModal)
    const renameFolderModal = ref(BModal)
    const updateModal = ref(BModal)
    const tree = ref(VJstree)

    //region Fonctions initiales
    const currentExt = computed(() => {
        if (currentPath.value === null) return null
        return currentPath.value.slice(currentPath.value.lastIndexOf('.'))
    })
    const currentFullName = computed(() => {
        if (currentPath.value === null) return null
        return currentPath.value.slice(currentPath.value.lastIndexOf('/') + 1)
    })
    const currentName = computed(() => {
        if (currentPath.value === null) return null
        return currentPath.value.substring(0, currentPath.value.lastIndexOf('.')).slice(currentPath.value.lastIndexOf('/') + 1)
    })
    function getChild(path) {
        const subPath = path.slice(start.length)
        for (const child of children) {
            const node = child.getChild(subPath)
            if (node !== null) return node
        }
        return null
    }

    function isDir(path) {
        const child = getChild(path)
        console.log(child)
        return true //TODO: A faire
    }
    function isExpirablePath(path) {
        // eslint-disable-next-line no-undefined
        if (path !== undefined)
            for (const localDir of props.expirationDirs)
                if (path.startsWith(localDir))
                    return true
        return false
    }
    const isCurrentDir = computed(() => currentPath.value !== null && isDir(currentPath))
    const isCurrentExpirable = computed(() => currentPath.value !== null && isExpirablePath(currentPath))
    const isNotCurrentPathRoot = computed(() => currentPath.value !== null && currentPath.value !== props.rootDir)
    const isNotSubCurrentPathRoot = computed(() => isNotCurrentPathRoot.value && RegExp(`^${props.rootDir}/.+/.+`).test(currentPath))
    //endregion

    async function addFile(form) {
        const body = new FormData(form)
        console.log(body)
        //TODO: Modifier addFile
    }
    async function addFolder(form) {
        const body = new FormData(form)
        console.log(body)
        //TODO: Modifier addFolder
    }
    function findDirContent(path) {
        console.log(path)
        return [] //TODO: A faire
    }

    function refresh() {
        key.value++
    }

    // eslint-disable-next-line no-unused-vars
    function isLeaf() {
        return true //TODO: A faire
    }

    // eslint-disable-next-line no-shadow
    function initialize(file, {path, children}) {
        const child = file.getChild(path)
        if (child !== null) {
            // eslint-disable-next-line array-callback-return
            children.forEach(c => child.appendChild(c))
            child.initialized = true
        }
    }

    async function preview(path) {
        const body = new FormData()
        body.append('path', path)
        console.log(body)//TODO: A faire
    }

    function remove(path){
        const data = new FormData()
        data.append('_method', 'DELETE')
        data.append('path', path)
        return state.connection.remove({}, data).then(() => {
            const reset = new FormData()
            const localDir = path.substring(0, path.lastIndexOf('/'))
            reset.append('dir', localDir)
            return dispatch('reinitialize', reset).then(() => commit('leaf', localDir))
        })
    }

    async function rename(form){
        const body = new FormData(form)
        const {data} = await state.connection.rename({}, body)
        return new Promise((resolve, reject) => {
            if (data.status) {
                const path = body.get('path')
                const reset = new FormData()
                reset.append('dir', path.substring(0, path.lastIndexOf('/')))
                resolve(dispatch('reinitialize', reset))
            } else
                reject(data.message)
        })
    }
    async function renameFolder(dispatch, form) {
        const body = new FormData(form)
        const {data} = await state.connection.renameFolder({}, body)
        return new Promise((resolve, reject) => {
            if (data.status) {
                const path = body.get('path')
                const reset = new FormData()
                reset.append('dir', path.substring(0, path.lastIndexOf('/')))
                resolve(dispatch('reinitialize', reset))
            } else reject(data.message)
        })
    }

    function getPath(node){
        const parts = []
        let next = false
        do {
            next = false
            parts.push(node.data.text)
            // eslint-disable-next-line no-param-reassign
            node = node.$parent
            for (const c of node.classes)
                if (c['tree-node']) {
                    next = true
                    break
                }
        } while (next)
        return parts.reverse().reduce((path, name) => `${path}/${name}`)
    }

    function setConnection(file, connection){
        file.connection = connection
    }
    function setRootName(file, name){
        file.text = name
    }
    function download() {
        // eslint-disable-next-line no-new
        new Downloader({url: `/${currentPath.value}`})
    }
    function hasError(id, field) {
        // eslint-disable-next-line no-undefined
        return errors[id] === undefined || errors[id][field] === undefined ? null : false
    }
    function getError(id, field) {
        return hasError(id, field) === null ? null : errors[id][field]
    }
    function isExpirableNode(node) {
        return isExpirablePath(FileManager.getPath(node))
    }
    function onAddExpirableFile(ok) {
        loading.value = true
        errors.value = {}
        const data = new FormData(formExpirableFile)
        // @ts-ignore
        if (data.get('file').name.length === 0)
            errors[`add-expirable-file-${currentPath.value}`] = {file: 'Veuillez choisir un fichier'}
        if (data.get('expiration-date').length === 0)
            errors[`add-expirable-file-${currentPath.value}`] = {
                ...errors[`add-expirable-file-${currentPath.value}`],
                'expiration-date': 'Veuillez sélectionner une date'
            }
        // eslint-disable-next-line no-undefined
        if (errors[`add-expirable-file-${currentPath.value}`] === undefined)
            addFile(formExpirableFile).then(
                () => {
                    formExpirableFile.value.reset()
                    ok()
                    refresh()
                    loading.value = false
                }
            )
        // .catch(
        //     message => {
        //         errorUnique.value = message
        //         errorModal.value.show()
        //     }
        // )
        else his.loading = false
    }
    function onAddFile() {
        loading.value = true
        addFile(formFile).then(() => {
            formFile.value.reset()
            refresh()
            loading.value = false
        })
    // .catch(message => {
    //     errorUnique.value = message
    //     errorModal.value.show()
    // })
    }
    function onAddFileMenuClick() {
        if (isCurrentExpirable.value)
            addExpirableFileModal.value.show()
        else
            inputFile.value.click()
    }
    function onAddFolder(ok) {
        loading.value = true
        errors.value = {}
        const data = new FormData(formFolder.value)
        if (isValidFilename(data.get('folder')))
            addFolder(formFolder).then(() => {
                formFolder.value.reset()
                ok()
                refresh()
                loading.value = false
            })
        // .catch(message => {
        //     errorUnique.value = message
        //     errorModal.value.show()
        // })
        else {
            errors[`add-folder-${currentPath.value}`] = {folder: 'Nom de dossier invalide'}
            loading.value = false
        }
    }
    function onClick(node, item) {
        const path = getPath(node)
        const child = getChild(path)
        if (child !== null && !child.isLeaf) {
            if (!child.initialized) {
                loading.value = true
                findDirContent(path).then(({data}) => {
                    initialize({children: data, path})
                    refresh()
                    loading.value = false
                })
                // eslint-disable-next-line no-negated-condition
            } else if (!item.opened)
                item.openChildren()
            else
                item.closeChildren()
        }
    }
    function onContextMenuShow(e, model, node){
        menu.value.close(e)
        currentExpirationDate.value = model.expirationDate
        currentPath.value = FileManager.getPath(node)
        if (isNotCurrentPathRoot.value && (isNotSubCurrentPathRoot.value || isCurrentDir))
            menu.value.open(e)
    }
    function onErrorModalClose() {
        errorUnique.value = ''
        loading.value = false
    }
    function onRemove(ok) {
        loading.value = true
        remove(currentPath.value).then(() => {
            ok()
            refresh()
            loading.value = false
        })
    }
    function onRename(ok) {
        loading.value = true
        errors.value = {}
        const data = new FormData(formUpdate)
        // @ts-ignore
        if (data.get('new-name').length === 0)
            errors[`update-${currentPath.value}`] = {'new-name': 'Veuillez saisir un nouveau nom'}
        if (data.has('expiration-date') && data.get('expiration-date').length === 0)
            errors[`update-${currentPath.value}`] = {
                ...errors[`update-${currentPath.value}`],
                'expiration-date': 'Veuillez sélectionner une date'
            }
        // eslint-disable-next-line no-undefined
        if (errors[`update-${currentPath.value}`] === undefined)
            rename(formUpdate.value)
                .then(() => {
                    formUpdate.value.reset()
                    ok()
                    refresh()
                    loading.value = false
                })
        // .catch(message => {
        //     errorUnique.value = message
        //     errorModal.value.show()
        // })
        else loading.value = false
    }
    function onRenameFolder(ok) {
        loading.value = true
        errors.value = {}
        const data = new FormData(formRenameFolder.value)
        if (isValidFilename(data.get('new-name')))
            renameFolder(formRenameFolder.value).then(() => {
                formRenameFolder.value.reset()
                ok()
                refresh()
                loading.value = false
            })
        // .catch(message => {
        //     errorUnique.value = message
        //     errorModal.value.show()
        // })
        else {
            errors[`rename-folder-${currentPath.value}`] = {'new-name': 'Nom de fichier invalide'}
            loading.value = false
        }
    }
    function onPreview() {
        loading.value = true
        preview(currentPath.value)
            .then(src => {
                previewSrc.value = src
                previewModal.value.show()
                loading.value = false
            })
        // .catch(message => {
        //     errorUnique.value = message
        //     errorModal.value.show()
        // })
    }
    //region Evènements Composant
    onMounted(() => {
        if (props.toInitialize) {
            setConnection($resource('/attachment/', {}, $resourceActions))
            setRootName(props.rootDir)
            findDirContent(props.rootDir).then(({data}) => {
                initialize({children: data, path: props.rootDir})
                refresh()
                loading.value = false
            })
        }
        loading.value = false
    })
    //endregion
</script>

<template>
    <BOverlay :opacity=".9" :show="loading" variant="transparent">
        <VJstree :key="key" ref="tree" :data="dir" @item-click="onClick">
            <template #default="{model, vm}">
                <span @contextmenu.prevent="e => onContextMenuShow(e, model, vm)">
                    <template v-if="!model.loading">
                        <span
                            v-if="isExpirableNode(vm)"
                            :class="{'tree-themeicon-custom': !!model.icon}"
                            class="fa-stack tree-icon tree-themeicon"
                            role="presentation">
                            <i v-if="!!model.icon" :class="model.icon" class="fa-stack-1x"/>
                            <i class="fa fa-hourglass-half fa-stack-1x pl-2 pt-2"/>
                        </span>
                        <i v-else :class="vm.themeIconClasses" role="presentation"/>
                    </template>
                    <span v-html="model.text"/>
                    <span v-if="!!model.expirationDate">  {{ model.formattedExpirationDate }}</span>
                </span>
            </template>
        </VJstree>
        <VueContext ref="menu">
            <li v-show="isCurrentDir && hasRight">
                <a href="#" @click.prevent="onAddFileMenuClick">
                    <i class="fa fa-file-upload"/>
                    Ajouter un fichier
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && !isCurrentDir">
                <a href="#" @click.prevent="onPreview">
                    <i class="fa fa-eye"/>
                    Aperçu
                </a>
            </li>
            <li v-show="isCurrentDir && hasRight">
                <a href="#" @click.prevent="addFolderModal.show">
                    <i class="fa fa-folder-plus"/>
                    Créer un dossier
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && hasRight">
                <a v-if="isCurrentDir" href="#" @click.prevent="renameFolderModal.show">
                    <i class="fa fa-edit"/>
                    Renommer
                </a>
                <a v-else href="#" @click.prevent="updateModal.show">
                    <i class="fa fa-edit"/>
                    Modifier
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && hasRight">
                <a href="#" @click.prevent="removeModal.show">
                    <i class="fa fa-trash"/>
                    Supprimer
                </a>
            </li>
            <li v-show="isNotSubCurrentPathRoot && !isCurrentDir">
                <a href="#" @click.prevent="download">
                    <i class="fa fa-file-download"/>
                    Télécharger
                </a>
            </li>
        </VueContext>
        <form v-if="hasRight" class="d-none" enctype="multipart/form-data">
            <input :value="currentPath" name="dir" type="hidden"/>
            <input name="file" type="file" @change="onAddFile"/>
        </form>
        <BModal v-if="hasRight" title="Ajouter un fichier">
            <template #default="{ok}">
                <BOverlay :opacity=".9" :show="loading" variant="transparent">
                    <form
                        :id="`add-expirable-file-${currentPath}`"
                        enctype="multipart/form-data"
                        @submit.prevent="onAddExpirableFile(ok)">
                        <BFormGroup
                            :invalid-feedback="getError(`add-expirable-file-${currentPath}`, 'file')"
                            :state="hasError(`add-expirable-file-${currentPath}`, 'file')"
                            label="Fichier">
                            <BFormFile
                                :state="hasError(`add-expirable-file-${currentPath}`, 'file')"
                                autofocus
                                browse-text="Choisir"
                                drop-placeholder="Glisser le fichier ici"
                                name="file"
                                placeholder="Aucun fichier choisi"
                                required/>
                        </BFormGroup>
                        <BFormGroup
                            :invalid-feedback="getError(`add-expirable-file-${currentPath}`, 'expiration-date')"
                            :state="hasError(`add-expirable-file-${currentPath}`, 'expiration-date')"
                            label="Date d'expiration">
                            <AppFormDatepicker
                                :state="hasError(`add-expirable-file-${currentPath}`, 'expiration-date')"
                                name="expiration-date"
                                required/>
                        </BFormGroup>
                        <input :value="currentPath" name="dir" type="hidden"/>
                    </form>
                </BOverlay>
            </template>
            <template #modal-footer="{cancel}">
                <BButton
                    :disabled="loading"
                    :form="`add-expirable-file-${currentPath}`"
                    type="submit"
                    variant="success">
                    Créer
                </BButton>
                <BButton
                    :disabled="loading"
                    :form="`add-expirable-file-${currentPath}`"
                    type="reset"
                    variant="danger"
                    @click="cancel">
                    Annuler
                </BButton>
            </template>
        </BModal>
        <BModal v-if="hasRight" title="Créer un dossier">
            <template #default="{ok}">
                <BOverlay :opacity=".9" :show="loading" variant="transparent">
                    <BForm :id="`add-folder-${currentPath}`" @submit.prevent="onAddFolder(ok)">
                        <BFormGroup
                            :invalid-feedback="getError(`add-folder-${currentPath}`, 'folder')"
                            :state="hasError(`add-folder-${currentPath}`, 'folder')"
                            label="Nouveau dossier">
                            <BFormInput
                                :state="hasError(`add-folder-${currentPath}`, 'folder')"
                                autofocus
                                name="folder"
                                required/>
                        </BFormGroup>
                        <input :value="currentPath" name="dir" type="hidden"/>
                    </BForm>
                </BOverlay>
            </template>
            <template #modal-footer="{cancel}">
                <BButton :disabled="loading" :form="`add-folder-${currentPath}`" type="submit" variant="success">
                    Créer
                </BButton>
                <BButton
                    :disabled="loading"
                    :form="`add-folder-${currentPath}`"
                    type="reset"
                    variant="danger"
                    @click="cancel">
                    Annuler
                </BButton>
            </template>
        </BModal>
        <BModal v-if="hasRight" :title="`Renommer ${currentFullName}`">
            <template #default="{ok}">
                <BOverlay :opacity=".9" :show="loading" variant="transparent">
                    <BForm
                        :id="`rename-folder-${currentPath}`"
                        @submit.prevent="onRenameFolder(ok)">
                        <BFormGroup
                            :invalid-feedback="getError(`rename-folder-${currentPath}`, 'new-name')"
                            :state="hasError(`rename-folder-${currentPath}`, 'new-name')"
                            label="Nouveau nom">
                            <BFormInput
                                :state="hasError(`rename-folder-${currentPath}`, 'new-name')"
                                autofocus
                                name="new-name"
                                required/>
                        </BFormGroup>
                        <input :value="currentPath" name="path" type="hidden"/>
                    </BForm>
                </BOverlay>
            </template>
            <template #modal-footer="{cancel}">
                <BButton :disabled="loading" :form="`rename-folder-${currentPath}`" type="submit" variant="success">
                    Renommer
                </BButton>
                <BButton
                    :disabled="loading"
                    :form="`rename-folder-${currentPath}`"
                    type="reset"
                    variant="danger"
                    @click="cancel">
                    Annuler
                </BButton>
            </template>
        </BModal>
        <BModal v-if="hasRight" :title="`Modifier ${currentFullName}`">
            <template #default="{ok}">
                <BOverlay :opacity=".9" :show="loading" variant="transparent">
                    <BForm :id="`update-${currentPath}`" @submit.prevent="onRename(ok)">
                        <BFormGroup
                            :invalid-feedback="getError(`update-${currentPath}`, 'new-name')"
                            :state="hasError(`update-${currentPath}`, 'new-name')"
                            label="Nom">
                            <BInputGroup :append="currentExt">
                                <BFormInput
                                    :state="hasError(`update-${currentPath}`, 'new-name')"
                                    :value="currentName"
                                    autofocus
                                    name="new-name"
                                    required/>
                            </BInputGroup>
                        </BFormGroup>
                        <BFormGroup
                            v-if="isCurrentExpirable"
                            :invalid-feedback="getError(`update-${currentPath}`, 'expiration-date')"
                            :state="hasError(`update-${currentPath}`, 'expiration-date')"
                            label="Date d'expiration">
                            <AppFormDatepicker
                                :state="hasError(`update-${currentPath}`, 'expiration-date')"
                                :value="currentExpirationDate"
                                name="expiration-date"
                                required/>
                        </BFormGroup>
                        <input :value="currentPath" name="path" type="hidden"/>
                    </BForm>
                </BOverlay>
            </template>
            <template #modal-footer="{cancel}">
                <BButton :disabled="loading" :form="`update-${currentPath}`" type="submit" variant="success">
                    Modifier
                </BButton>
                <BButton
                    :disabled="loading"
                    :form="`update-${currentPath}`"
                    type="reset"
                    variant="danger"
                    @click="cancel">
                    Annuler
                </BButton>
            </template>
        </BModal>
        <BModal v-if="hasRight" body-bg-variant="danger" footer-bg-variant="danger" header-bg-variant="danger">
            <template #modal-title>
                <BBadge variant="danger">
                    Avertissement suppression
                </BBadge>
            </template>
            <BOverlay :opacity=".9" :show="loading" variant="transparent">
                <BAlert v-if="currentPath !== null" :show="currentPath !== null" variant="danger">
                    Voulez-vous vraiment supprimer <strong>{{ currentPath }}</strong>&nbsp;?
                    <div v-if="isCurrentDir">
                        Tous les sous-dossiers et sous-fichiers seront supprimés.
                    </div>
                </BAlert>
            </BOverlay>
            <template #modal-footer="{cancel, ok}">
                <BButton :disabled="loading" variant="danger" @click.prevent="onRemove(ok)">
                    Supprimer
                </BButton>
                <BButton :disabled="loading" variant="secondary" @click.prevent="cancel">
                    Annuler
                </BButton>
            </template>
        </BModal>
        <BModal
            footer-bg-variant="danger"
            header-bg-variant="danger"
            ok-only
            ok-title="Fermer"
            ok-variant="success"
            body-bg-variant="danger"
            @hidden="onErrorModalClose">
            <template #modal-title>
                <BBadge variant="danger">
                    Erreur
                </BBadge>
            </template>

            <BAlert v-if="errorUnique.length > 0" :show="errorUnique.length > 0" variant="danger">
                {{ errorUnique }}
            </BAlert>
        </BModal>
        <BModal
            :title="`Aperçu de ${currentFullName}`"
            ok-only
            ok-title="Fermer"
            ok-variant="success">
            <BImg :src="previewSrc"/>
        </BModal>
    </BOverlay>
</template>

<style scoped src="./../assets/style/vue-context.css"></style>
