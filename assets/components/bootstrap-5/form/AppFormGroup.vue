<script lang="ts" setup>
    import Field from '../../../store/entity/bootstrap-5/form/Field'
    import type {PropType} from 'vue'
    import {defineProps} from 'vue'

    defineProps({
        field: {
            required: true,
            type: Object as PropType<Field>,
            validator(field: unknown): boolean {
                return field instanceof Field
            }
        }
    })
</script>

<template>
    <div class="mb-3">
        <input :type="field.type" class="form-control"/>
    </div>
</template>
