import {computed} from 'vue'

export function useSlots(fields) {
    const createSlots = computed(() => {
        const slots = fields.map(field => ({name: field.name, slot: `create(${field.name})`}))
        slots.push({name: 'btn', slot: 'create(btn)'})
        return slots
    })
    const searchSlots = computed(() => fields.map(field => ({name: field.name, slot: `search(${field.name})`})))
    return {
        createSlots,
        searchSlots,
        slots: computed(() => [...createSlots.value, ...searchSlots.value])
    }
}
