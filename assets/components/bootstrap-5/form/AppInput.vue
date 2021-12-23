<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, withDefaults} from 'vue'

    const emit = defineEmits<(e: 'update:value', value: FormValue) => void>()
    const props = withDefaults(
        defineProps<{field: FormField, value?: FormValue, size?: BootstrapSize}>(),
        {size: 'sm', value: ''}
    )
    const checked = computed(() => Boolean(props.value))
    const sizeClass = computed(() => `form-control-${props.size}`)
    const type = computed(() => props.field.type ?? 'text')

    function input(e: Event): void {
        const target = e.target as HTMLInputElement
        emit('update:value', target.type === 'checkbox' ? target.checked : target.value)
    }
</script>

<template>
    <template v-if="type === 'grpbutton'">
        <div id="btnGroup" aria-label="Basic radio toggle button group" class="btn-group btn-group-sm" role="group">
            <input id="btnradio1" autocomplete="off" checked class="btn-check" name="btnradio" type="radio"/>
            <label class="btn btn-outline-success" for="btnradio1"/>

            <input id="btnradio2" autocomplete="off" class="btn-check" name="btnradio" type="radio"/>
            <label class="btn btn-outline-success" for="btnradio2">oui</label>

            <input id="btnradio3" autocomplete="off" class="btn-check" name="btnradio" type="radio"/>
            <label class="btn btn-outline-success" for="btnradio3">non</label>
        </div>
    </template>
    <template v-else-if="type === 'switch'">
        <div class="form-check form-switch">
            <input :checked="checked" class="form-check-input" type="checkbox" @input="input"/>
        </div>
    </template>
    <template v-else-if="type === 'select'">
        <select aria-label="Default select example" class="form-select">
            <option selected>
                Open this select menu
            </option>
            <option v-for="(option, i) in field.options" :key="i" :value="option.value">
                {{ option.text }}
            </option>
        </select>
    </template>
    <template v-else>
        <input :class="sizeClass" :name="field.name" :type="type" :value="value" class="form-control" @input="input"/>
    </template>
</template>
