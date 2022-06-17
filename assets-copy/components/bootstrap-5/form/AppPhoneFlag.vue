<script lang="ts" setup>
    import type {
        ComputedRef,
        PropType
    } from '@vue/runtime-core'
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {
        computed,
        defineEmits,
        defineProps,
        inject
    } from '@vue/runtime-core'
    import type {Getters} from '../../../store/countries'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    const phoneLabel = useNamespacedGetters<Getters>('countries', ['phoneLabel']).phoneLabel

    defineProps({
        field: {required: true, type: Object as PropType<FormField>},
        modelValue: {
            default: null,
            type: [Boolean, Number, String] as PropType<FormValue>
        }
    })

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
    const country = inject<ComputedRef<string | null>>('country', computed(() => null))
    const labelCountry = computed(() => phoneLabel.value(country.value))
</script>

<template>
    <AppRow class="rowPhone">
        <CountryFlag :country="country" size="normal"/>
        <AppLabel class="labelPhone">
            {{ labelCountry }}
        </AppLabel>

        <AppCol class="colPhone">
            <input
                :id="field.id"
                :name="field.name"
                :value="modelValue"
                class="form-control"
                type="text"
                @input="input"/>
        </AppCol>
    </AppRow>
</template>

<style scoped>
.colPhone {
  padding-left: calc(var(--bs-gutter-x) * -1.5);
}
.rowPhone {
  padding-left: 10px;
  margin-right: calc(0.5 * var(--bs-gutter-x));
}
.labelPhone {
  font-weight: 600;
  margin-top: 6px;
}
.col-2{
    width: 19.666667%;
}
</style>
