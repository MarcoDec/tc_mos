<script lang="ts" setup>
    import {defineEmits, defineProps, withDefaults} from 'vue'
    import {requestForm} from '../../../api'
    import {useManager} from '../../../store/repository/RepositoryManager'

    const emit = defineEmits<(e: 'success', data: Record<string, unknown> | null) => void>()
    const props = withDefaults(
        defineProps<{action: string, id: string, method?: 'delete' | 'get' | 'patch' | 'post'}>(),
        {method: 'post'}
    )
    const fields = useManager().forms.find(props.id)?.fields

    async function submit(e: Event): Promise<void> {
        if (e.target instanceof HTMLFormElement)
            emit('success', await requestForm(e.target, props.method))
    }
</script>

<template>
    <form :id="id" :action="action" autocomplete="off" method="post" @submit.prevent="submit">
        <AppFormGroup v-for="field in fields" :key="field" :field="field"/>
        <AppBtn class="float-end" label="Connexion" type="submit"/>
    </form>
</template>
