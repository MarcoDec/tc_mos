<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import FormRepository from '../../../store/repository/bootstrap-5/form/FormRepository'

    const props = withDefaults(
        defineProps<{action: string, id: string, submitLabel?: string}>(),
        {submitLabel: 'Enregistrer'}
    )

    const form = FormRepository.find(props.id)
    const fields = computed(() => form?.fields ?? [])
</script>

<template>
    <form :id="id" :action="action" autocomplete="off" method="post">
        <AppFormGroup v-for="field in fields" :key="field.name" :field="field"/>
        <AppBtn class="float-end" type="submit">
            {{ submitLabel }}
        </AppBtn>
    </form>
</template>
