<script setup>
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {useCookies} from '@vueuse/integrations/useCookies'
    import {BImg} from 'bootstrap-vue-next'
    import {computed, ref} from 'vue'
    const emits = defineEmits(['update:filePath'])
    const props = defineProps({
        filePath: {
            type: String,
            default: ''
        },
        imageUpdateUrl: {
            type: String,
            default: ''
        }
    })
    const fileInput = ref(null)
    const token = useCookies(['token']).get('token')
    const imageUrlNoImage = '/img/no-image.png'
    const imageUrlToShow = computed(() => {
        if (typeof props.filePath === 'undefined' || props.filePath === '') {
            return imageUrlNoImage
        }
        return props.filePath
    })
    const handleFileChange = async () => {
        const file = fileInput.value.files[0]
        if (file) {
            const formData = new FormData()
            formData.append('file', file)

            try {
                const response = await fetch(props.imageUpdateUrl, { // Utilisez l'URL réelle ici
                    method: 'POST',
                    body: formData,
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                    // N'ajoutez pas d'en-tête 'Content-Type'. Laissez le navigateur le définir automatiquement.
                })

                if (response.ok) {
                    emits('update:filePath')
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
</script>

<template>
    <div class="image-container m-1 width30">
        <BImg thumbnail fluid :src="imageUrlToShow" alt="Image 1"/>
        <FontAwesomeIcon icon="fa-solid fa-pencil-alt" class="image-edit-icon bg-primary text-white" @click="openFilePicker"/>
        <input ref="fileInput" class="d-none" accept="image/png, image/gif, image/jpeg" type="file" @change="handleFileChange"/>
    </div>
</template>

<style scoped>
    .image-edit-icon {
        position: absolute;
        top:calc(0% + 10px);
        left: 10px;
        transform: translate(-50%, -50%);
        cursor: pointer;
        z-index: 100; /* Assurez qu'il reste au-dessus du contenu */
    }
    .image-container {
        position: relative;
        display: inline-block; /* ou autre selon le besoin */
    }
</style>
