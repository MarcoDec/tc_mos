<script setup>
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeUnmount, ref} from 'vue'
    import AppBtn from '../../../AppBtn.vue'
    import AppComponentFormShow from './AppComponentFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useComponentListStore} from '../../../../stores/purchase/component/components'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {BImg} from 'bootstrap-vue-next'
    import AppComponentShowInlist from './AppComponentShowInlist.vue'
    import AppShowComponentTabGeneral from './tabs/AppShowComponentTabGeneral.vue'

    import {useCookies} from '@vueuse/integrations/useCookies'

    //region définition des constantes
    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const fetchUnits = useOptions('units')
    const useFetchComponentStore = useComponentListStore()
    const modeDetail = ref(true)
    const isFullScreen = ref(false)
    const fileInput = ref(null)
    const token = useCookies(['token']).get('token')
    //endregion
    //region Chargement des données
    fetchUnits.fetchOp()
    useFetchComponentStore.fetchOne(idComponent)
    //endregion
    //region définition des méthodes
    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const imageUpdateUrl = '/api/components/' + idComponent + '/image'

    const handleFileChange = async () => {
        const file = fileInput.value.files[0]
        if (file) {
            const formData = new FormData()
            formData.append('file', file)

            try {
                console.log(imageUpdateUrl, `Bearer ${token}`)
                const response = await fetch(imageUpdateUrl, { // Utilisez l'URL réelle ici
                    method: 'POST',
                    body: formData,
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                    // N'ajoutez pas d'en-tête 'Content-Type'. Laissez le navigateur le définir automatiquement.
                })

                if (response.ok) {
                    const result = await response.json()
                    // Traitez le résultat ici, par exemple, mettre à jour l'image affichée
                } else {
                    // Gérer les réponses non réussies
                    console.error('Échec de l\'envoi du fichier')
                }
            } catch (error) {
                // Gérer les erreurs de réseau ou de connexion
                console.error('Erreur lors de l’envoi de la requête:', error)
            }
        }
    }
    const openFilePicker = () => {
        fileInput.value.click()
    }
    //endregion
    //region déchargement des données
    onBeforeUnmount(() => {
        useFetchComponentStore.reset()
        fetchUnits.resetItems()
    })
    //endregion
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-left>
                <div class="bg-white border-1 border-dark p-1">
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    <b>{{ useFetchComponentStore.component.code }}</b>: {{ useFetchComponentStore.component.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <div class="image-container m-1" style="width:30%">
                        <BImg thumbnail fluid :src="useFetchComponentStore.component.filePath" alt="Image 1"></BImg>
                        <FontAwesomeIcon icon="fa-solid fa-pencil-alt" class="image-edit-icon bg-primary text-white" @click="openFilePicker"/>
                        <input type="file" ref="fileInput" @change="handleFileChange" style="display: none;" accept="image/png, image/gif, image/jpeg"/>
                    </div>
                    <AppSuspense><AppShowComponentTabGeneral style="width:70%"/></AppSuspense>
                </div>

            </template>
            <template #gui-bottom>
                <div :class="{ 'full-screen': isFullScreen }" class="full-visible-width font-small">
                    <div class="btn-container">
                        <FontAwesomeIcon v-if="isFullScreen" @click="deactivateFullScreen" icon="fa-solid fa-magnifying-glass-minus" />
                        <FontAwesomeIcon v-else @click="activateFullScreen" icon="fa-solid fa-magnifying-glass-plus" />
                    </div>
                    <AppSuspense><AppComponentFormShow v-if="useFetchComponentStore.isLoaded && fetchUnits.isLoaded && modeDetail"/></AppSuspense>
                    <AppSuspense><AppComponentShowInlist v-if="!modeDetail"/></AppSuspense>
                </div>
            </template>
            <template #gui-right/>
        </AppShowGuiGen>
    </AppSuspense>
</template>

<style>
    .selected-detail {
        background-color: #46e046 !important;
        color: white !important;
        border: 1px solid #46e046;
    }
    .border-dark {
        border-bottom: 1px solid grey;
    }
    .full-visible-width {
        min-width:calc(100vw - 35px);
        padding: 2px;
    }
    .full-screen {
        position: fixed;
        top: 10px;
        left: 0;
        width: 95vw;
        height: 100vh;
        z-index: 10000; /* Assurez-vous que c'est au-dessus des autres éléments */
        background-color: white; /* ou toute autre couleur de fond souhaitée */
    }
    .btn-container {
        position: relative;
        float: right;
        background-color: white;
        top: 0;
        right: 0;
        z-index: 10010; /* Assurez qu'il reste au-dessus du contenu */
    }
    .image-edit-icon {
        position: absolute;
        top:calc(0% + 10px);
        left: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        z-index: 100; /* Assurez qu'il reste au-dessus du contenu */
    }
    .image-container {
        position: relative;
        display: inline-block; /* ou autre selon le besoin */
    }
</style>
