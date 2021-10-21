<script lang="ts" setup>
    import {defineProps} from 'vue'
    import {useManager} from '../../../store/repository/RepositoryManager'

    const props = defineProps<{field: string}>()
    const storedField = useManager().fields.find(props.field)
</script>

<template>
    <AppRow css-class="mb-3">
        <AppLabel>
            {{ storedField?.label }}
        </AppLabel>
        <AppCol>
            <AppInput :form="storedField?.form" :name="storedField?.name" :type="storedField?.type"/>
        </AppCol>
    </AppRow>
</template>
