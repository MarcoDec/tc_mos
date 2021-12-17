<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const props = defineProps<{field: FormField, value?: number | string}>()
    const type = computed(() => (typeof props.field.type !== 'undefined' ? props.field.type : 'text'))
    const emit = defineEmits<(e: 'update:value', value: number | string) => void>()

    function input(e: Event): void {
        if (e instanceof InputEvent)
            emit('update:value', (e.target as HTMLInputElement).value)
    }
</script>

<template>
    <template v-if="type === 'grpbutton'">
        <div id="btnGroup" class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
            <input id="btnradio1" type="radio" class="btn-check" name="btnradio" autocomplete="off" checked/>
            <label class="btn btn-outline-success" for="btnradio1"/>

            <input id="btnradio2" type="radio" class="btn-check" name="btnradio" autocomplete="off"/>
            <label class="btn btn-outline-success" for="btnradio2">oui</label>

            <input id="btnradio3" type="radio" class="btn-check" name="btnradio" autocomplete="off"/>
            <label class="btn btn-outline-success" for="btnradio3">non</label>
        </div>
    </template>
    <template v-else-if="type === 'switch'">
        <div class="form-check form-switch">
            <input id="flexSwitchCheckDefault" class="form-check-input" type="checkbox"/>
        </div>
    </template>
    <template v-else-if="type === 'select'">
        <select class="form-select" aria-label="Default select example">
            <option selected>
                Open this select menu
            </option>
            <option v-for="option in field.options" :key="option.value" :value="option.value">
                {{ option.text }}
            </option>
        </select>
    </template>
    <template v-else>
        <input class="form-control" :type="type" :name="field.name" :placeholder="field.label" :value="value" @input="input"/>
    </template>
</template>
