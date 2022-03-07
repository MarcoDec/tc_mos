<script lang="ts" setup>
    import {computed, defineEmits, defineProps,reactive} from 'vue'
    import type {TableField} from '../../../types/app-rows-table'

    const emit = defineEmits<(e: 'click', field: TableField) => void>()
    const props = defineProps<{asc: boolean, field: TableField, sort: string, rowspan:number}>()
    console.log('props',props);
    
    const down = computed(() => ({'text-secondary': props.field.name !== props.sort || props.asc}))
    const up = computed(() => ({'text-secondary': props.field.name !== props.sort || !props.asc}))
    
    function childLength(field:TableField):number{
         
        if (Array.isArray(field.children)&& field.children.length > 0 ){
            let somme = 2
            for (const walkedField of field.children)
                somme += childLength(walkedField) 
            return somme
        }
        return  1
    }

    const colspan = computed(() => childLength(props.field))
    const safeRowSpan = computed(() => Array.isArray(props.field.children)&& props.field.children.length > 0 ? 1 : props.rowspan )

    function click(): void {
        emit('click', props.field)
    }
</script>

<template>
    <th :colspan="colspan" :rowspan="safeRowSpan" @click="click">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
            <span v-if="field.sort" class="d-flex flex-column">
                <Fa :class="down" icon="caret-up"/>
                <Fa :class="up" icon="caret-down"/>
            </span>
        </span>
    </th>
</template>
