<script lang="ts" setup>
    import {computed, ref, inject} from 'vue'

    const fields = inject<TableField[]>('fields', [])
    // const search = ref(true)
    // const row = computed(() => (search.value ? 'AppRowsTableSearch' : 'AppRowsTableAdd'))
    const rows= computed(() => {
        const ranks = []
        let current = fields
        do {
            console.log('current',current);
            
            ranks.push(current)
            current = current.map(field => Array.isArray(field.children)&& field.children.length > 0 ? field.children: []).flat()
        } while (current.length > 0)
        return ranks
    })
    
    
    // function toggle(): void {
    //     search.value = !search.value
    // }
</script>

<template>
    <thead class="table-dark">
        <AppRowsTableFields v-for="row in rows" :fields="row"/>
        <!-- <component :is="row" @toggle="toggle"/> -->
    </thead>
</template>
