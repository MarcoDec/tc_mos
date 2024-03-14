<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    const props = defineProps({
        actions: {default: () => [], required: true, type: Array},
        defaultAction: {default: null, required: true, type: String}
    })
    const emit = defineEmits(['actionSelected'])
    const isOpen = ref(false)

    const selectedName = ref(props.defaultAction)
    const selectedIcon = computed(() => {
        if (props.actions.find(action => action.name === selectedName.value))
            return props.actions.find(action => action.name === selectedName.value).icon
        return 'eye'
    })
    const selectedColor = computed(() => {
        if (props.actions.find(action => action.name === selectedName.value))
            return props.actions.find(action => action.name === selectedName.value).color
        return '#FFFFFF'
    })

    function toggleDropdown() {
        isOpen.value = !isOpen.value
    }

    function selectAction(action) {
        selectedName.value = action.name
        selectedIcon.value = action.icon
        selectedColor.value = action.color
        isOpen.value = false
        emit('actionSelected', action.name)
    }
</script>

<template>
    <div class="dropdown">
        <div class="dropdown-selection" @click="toggleDropdown">
            <FontAwesomeIcon :icon="selectedIcon" :style="{color: selectedColor}"/>
            <span>{{ selectedName }}</span>
            <i class="fa fa-chevron-down"/>
        </div>
        <div v-if="isOpen" class="dropdown-list">
            <div
                v-for="action in actions"
                :key="action.id"
                class="dropdown-item"
                :style="{color: action.color}"
                @click="selectAction(action)">
                <FontAwesomeIcon :icon="action.icon"/>
                <span>{{ action.name }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .dropdown {
        position: relative;
        user-select: none;
    }

    .dropdown-selection {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
    }

    .dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        //width: 100%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .dropdown-item {
        padding: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dropdown-item:hover {
        background-color: #f0f0f0;
    }
</style>
