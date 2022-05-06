<script setup>
    import {FiniteStateMachineRepository, NodeRepository} from '../../store/modules'
    import {computed, onMounted, onUnmounted, ref, watch} from 'vue'
    import {useRepo, useRouter} from '../../composition'

    const emit = defineEmits(['submit'])
    const {id: route} = useRouter()
    const props = defineProps({
        fields: {required: true, type: Array},
        id: {default: null, type: String},
        modelValue: {default: () => ({}), type: Object},
        repo: {required: true, type: Function},
        success: {default: 'Créer', type: String},
        title: {default: 'Créer une famille', type: String},
        update: {type: Boolean}
    })
    const safeId = computed(() => props.id ?? route)
    const form = computed(() => `${safeId.value}-form`)
    const stateRepo = useRepo(FiniteStateMachineRepository)
    const state = computed(() => stateRepo.find(form.value))
    const isOnError = computed(() => state.value?.isOnError ?? false)
    const value = ref({...props.modelValue})

    async function submit(body) {
        if (props.update)
            await NodeRepository.create(body, props.repo, form.value)
        else
            emit('submit', body)
    }

    onMounted(() => {
        stateRepo.create(form.value)
    })

    onUnmounted(() => {
        stateRepo.destroy(form.value, form.value)
    })

    watch(() => props.modelValue, modelValue => {
        if (!isOnError.value)
            value.value = modelValue
    })
</script>

<template>
    <AppCard :id="safeId" :title="title" class="bg-blue">
        <AppForm :id="form" :fields="fields" :model-value="value" :state-machine="form" multipart @submit="submit">
            <template #start>
                <slot name="start"/>
            </template>
            <AppBtn type="submit" variant="success">
                <Fa icon="plus"/>
                {{ success }}
            </AppBtn>
            <slot/>
        </AppForm>
    </AppCard>
</template>
