<script setup>
    import {assign, useMachine} from '../../composable/xstate'
    import {useRoute, useRouter} from 'vue-router'
    import {onUnmounted} from 'vue'
    import useFields from '../../stores/field/fields'
    import useUser from '../../stores/security'
    import {AppOverlay} from '../index'

    const route = useRoute()
    const router = useRouter()
    const user = useUser()
    const fields = useFields(route.name, [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ])
    const form = `${route.name}-form`
    const {send, state} = useMachine({
        context: {error: null},
        id: form,
        initial: 'form',
        states: {
            error: {on: {submit: {target: 'loading'}}},
            form: {on: {submit: {target: 'loading'}}},
            loading: {
                on: {
                    fail: {
                        actions: [assign({error: (context, {error}) => error})],
                        target: 'error'
                    },
                    success: {target: 'logged'}
                }
            },
            logged: {type: 'final'}
        }
    })

    async function submit(data) {
        send('submit')
        try {
            await user.connect(data)
            send('success')
            await router.push({name: 'home'})
        } catch (error) {
            send('fail', {error})
        }
    }

    onUnmounted(() => {
        fields.dispose()
    })
</script>

<template>
    <AppOverlay :spinner="state.matches('loading')" class="row centered-form">
        <AppCard class="m-auto" title="Connexion">
            <div v-if="state.matches('error')" class="alert alert-danger" role="alert">
                {{ state.context.error }}
            </div>
            <AppFormGenerator
                :id="form"
                :disabled="state.matches('loading')"
                :fields="fields"
                submit-label="Connexion"
                @submit="submit"/>
        </AppCard>
    </AppOverlay>
</template>

<style scoped>
    .centered-form {
        display: flex;
        justify-content: center; /* Centre horizontalement */
        align-items: center; /* Centre verticalement */
        height: 70vh; /* Utilise 100% de la hauteur de la fenêtre */
    }
    .m-auto {
        width: auto;
        min-width: 550px;
    }
    /* Media Query pour les tablettes */
    @media (max-width: 768px) {
        .centered-form {
            height: 80vh; /* Ajuste la hauteur pour les tablettes */
        }
        .m-auto {
            min-width: 300px; /* Largeur minimale plus petite pour les tablettes */
        }
    }

    /* Media Query pour les mobiles */
    @media (max-width: 576px) {
        .centered-form {
            height: auto; /* Hauteur auto pour éviter les problèmes avec le clavier virtuel */
            padding: 20px; /* Ajoute un peu de padding */
        }
        .m-auto {
            min-width: auto; /* Permet au formulaire d'être plus flexible */
            width: 100%; /* Utilise toute la largeur disponible */
            max-width: 500px; /* Une largeur maximale pour éviter que le formulaire ne soit trop large sur les petits écrans */
        }
    }
</style>
