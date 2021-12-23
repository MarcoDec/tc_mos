<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, withDefaults} from 'vue'

    const emit = defineEmits<(e: 'update:value', value: FormValue) => void>()
    const props = withDefaults(
        defineProps<{field: FormField, value?: FormValue, size?: BootstrapSize}>(),
        {size: 'sm', value: ''}
    )

    const sizeClass = computed(() => `form-control-${props.size}`)
    const type = computed(() => (typeof props.field.type !== 'undefined' ? props.field.type : 'text'))

    function input(e: Event): void {
        const target = e.target as HTMLInputElement
        emit('update:value', target.type === 'checkbox' ? target.checked : target.value)
    }
    const checked = computed(() => Boolean(props.value))
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
            <input class="form-check-input" type="checkbox" :checked="checked" @input="input"/>
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
        <input :class="sizeClass" :name="field.name" :type="type" :placeholder="field.label" :value="value" class="form-control" @input="input"/>
    </template>
</template>
