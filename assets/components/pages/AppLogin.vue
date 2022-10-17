<script setup>
    import {assign, useMachine} from '../../composable/xstate'
    import {useRoute, useRouter} from 'vue-router'
    import AppCard from '../AppCard.vue'
    import AppFormGenerator from '../form/AppFormGenerator.vue'
    import useFields from '../../stores/field/fields'
    import useUser from '../../stores/security'

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
</script>

<template>
    <AppOverlay :spinner="state.matches('loading')" class="row">
        <div class="col">
            <AppCard title="Connexion">
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
        </div>
    </AppOverlay>
</template>
