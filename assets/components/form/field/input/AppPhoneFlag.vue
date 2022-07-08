<script setup>
    import {computed, inject} from 'vue-demi'
    import useCountries from '../../../../stores/countries/countries'

    const emit = defineEmits('update:modelValue')
    const countries = useCountries()

    defineProps({
        field: {required: true, type: Object},
        modelValue: {
            default: null,
            type: [Boolean, Number, String]
        }
    })

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
    const country = inject(
        'country',
        computed(() => null)
    )

    const labelCountry = computed(() => countries.phoneLabel(country.value))
</script>

<template>
    <div class="row">
        <div class="col-2">
            <span><CountryFlag :country="country" size="normal"/></span>
            <span class="labelCountry"> {{ labelCountry }}</span>
        </div>
        <div class="col-10">
            <input
                :id="field.id"
                :name="field.name"
                :value="modelValue"
                class="form-control"
                type="text"
                @input="input"/>
        </div>
    </div>
</template>

<style scoped>
 .labelCountry{
   margin-left: 10px;
    font-weight: 600;
    padding-top: 10px;
 }
</style>
