<script setup>
    import {ref} from 'vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import Fa from '../../../../Fa'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'

    const props = defineProps({
        fields: {required: true, type: Array, default: () => []},
        formData: {required: false, type: Object, default: () => ({})},
        icon: {required: false, type: String, default: 'square-plus'},
        id: {required: true, type: String},
        title: {required: false, type: String, default: 'Ajouter un élément'},
        violations: {required: false, type: Array, default: () => []}
    })
    const emits = defineEmits(['input', 'update:modelValue', 'save', 'cancel'])
    const formData = ref(props.formData)
    const fieldsForm = ref(props.fields)
    const isPopupVisible = ref(false)
    const violations = ref(props.violations)
    function cancel(){
        emits('cancel')
    }
    function input(data){
        emits('input', data)
    }
    function save(data) {
        emits('save', data)
    }
</script>

<template>
    <AppCard class="bg-blue col" title="">
        <div class="row">
            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="cancel">
                <Fa icon="angle-double-left"/>
            </button>
            <h4 class="col">
                <slot name="title">
                    <Fa :icon="icon"/>
                    {{ title }}
                </slot>
            </h4>
        </div>
        <AppSuspense>
            <AppFormCardable :id="`form_${id}`" :model-value="formData" :fields="fieldsForm" label-cols @update:model-value="input"/>
        </AppSuspense>
        <div v-if="isPopupVisible" class="violations">
            <slot name="violations">
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="violation in violations" :key="violation">
                            {{ violation.message }}
                        </li>
                    </ul>
                </div>
            </slot>
        </div>
        <div class="btnright row">
            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="save">
                <Fa icon="plus"/> Enregister
            </AppBtn>
        </div>
    </AppCard>
</template>

