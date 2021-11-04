<script lang="ts" setup>
    import {useNamespacedActions, useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
    import {UsersActionTypes} from '../../../store/security/action-types'
    import {UsersMutationTypes} from '../../../store/security/mutation-types'
    import {ref} from 'vue'
    const name = useNamespacedState('users', ['username']).username
    const password = ref<string | null>(null)
    const username = ref<string | null>(null)
    const actions = useNamespacedActions('users', [UsersActionTypes.FETCH_USERS])[UsersActionTypes.FETCH_USERS]
    const mutations = useNamespacedMutations('users', [UsersMutationTypes.SET_USER])[UsersMutationTypes.SET_USER]
    console.log('name---->', name)
    async function handleClick(): Promise<void> {
        await actions({
            password: password.value,
            username: username.value
        })
        await mutations({
            username: username.value
        })

    }
</script>

<template>
    <AppRow>
        <h3>Connexion</h3>

        <AppCard class="bg-blue col">
            <div>
                <div class="form-group">
                    <label>Identifiant</label>
                    <input
                        v-model="username"
                        type="text"
                        class="form-control form-control-lg"
                        required/>
                </div>
                <div class="form-group">
                    <label>Mot De Passe</label>
                    <input
                        v-model="password"
                        type="password"
                        class="form-control form-control-lg"
                        required/>
                </div>
                <button
                    class="btn btn-block btn-dark btn-lg"
                    @click="handleClick">
                    Connexion
                </button>
            </div>
        </AppCard>
    </AppRow>
</template>
