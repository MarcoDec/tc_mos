<script setup>
    import AppDropdown from '../../bootstrap-5/dropdown/AppDropdown.vue'
    import AppNotificationCategory from './AppNotificationCategory.vue'
    import {NotificationRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    defineProps({id: {required: true, type: String}})
    const repo = useRepo(NotificationRepository)
    const categories = computed(() => repo.findByCategories)
</script>

<template>
    <AppDropdown :id="id" class="me-1">
        <template #toggle="{id: dropdownId}">
            <AppBtn
                :id="dropdownId"
                aria-exanded="false"
                class="dropdown-toggle"
                data-bs-auto-close="outside"
                data-bs-toggle="dropdown">
                <Fa icon="bell"/>
            </AppBtn>
        </template>
        <AppNotificationCategory
            v-for="[category, notifications] in categories"
            :key="category"
            :category="category"
            :notifications="notifications"/>
    </AppDropdown>
</template>
