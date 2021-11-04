<script lang="ts" setup>
    import {ActionTypes} from '../../../store/security/actions'
    import type {Actions} from '../../../store/security/actions'
    import {ref} from 'vue'
    import {useNamespacedActions} from 'vuex-composition-helpers'

    const password = ref<string | null>(null)
    const username = ref<string | null>(null)

    const fetchUsers = useNamespacedActions<Actions>('users', [ActionTypes.FETCH_USERS])[ActionTypes.FETCH_USERS]

    async function handleClick(): Promise<void> {
        await fetchUsers({password: password.value, username: username.value})
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
                        class="form-control form-control-lg"
                        required
                        type="text"/>
                </div>
                <div class="form-group">
                    <label>Mot De Passe</label>
                    <input
                        v-model="password"
                        class="form-control form-control-lg"
                        required
                        type="password"/>
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
