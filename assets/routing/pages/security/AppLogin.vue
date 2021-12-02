<script lang="ts" setup>
import {ActionTypes, Mutations, MutationTypes, State} from '../../../store/security'
import type {Actions} from '../../../store/security'
import type {FormField} from '../../../types/bootstrap-5'
import {ref} from 'vue'
import router from '../../router'
import {
  useNamespacedActions,
  useNamespacedMutations,
  useNamespacedState
} from 'vuex-composition-helpers'
import {useLoading} from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

const fields: FormField[] = [
  {label: 'Identifiant', name: 'username'},
  {label: 'Mot de passe', name: 'password', type: 'password'}
]

const formData = ref<{ password: string | null, username: string | null }>({password: null, username: null})
const fetchUsers = useNamespacedActions<Actions>('users', [ActionTypes.FETCH_USERS])[ActionTypes.FETCH_USERS]
//const error = useNamespacedMutations<Mutations>('users', [MutationTypes.ERROR])[MutationTypes.ERROR]
const showError = useNamespacedState<State>('users', ['error']).error
const msgError = useNamespacedState<State>('users', ['msgError']).msgError
const status = useNamespacedState<State>('users', ['status']).status

async function handleClick(): Promise<void> {
  let loader = useLoading();
  loader.show({
    canCancel: true,
    onCancel: onCancel,
  });
  setTimeout(() => {
    loader.hide()
  },1000)

    await fetchUsers(formData.value)
    await router.push({name: 'home'})

}


const onCancel =() => {
  console.log('User cancelled the loader.')
};


</script>

<template>
  <AppRow>
    <div class="alert alert-danger" role="alert" v-if="showError">
      <span class="badge bg-danger">Erreur {{ status }}</span>
      {{ msgError }}
    </div>
    <AppCard class="bg-blue col">
      <AppForm v-model:values="formData" :fields="fields" @submit="handleClick"/>
    </AppCard>
  </AppRow>
</template>
