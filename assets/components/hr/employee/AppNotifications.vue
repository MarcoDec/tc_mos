<script setup>
    import AppDropdown from '../../bootstrap-5/dropdown/AppDropdown.vue'
    import AppNotificationCategory from './AppNotificationCategory.vue'
    import {NotificationRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    defineProps({id: {required: true, type: String}})
    const repo = useRepo(NotificationRepository)
    const categories = computed(() => repo.findByCategories)
    const isEmpty = computed(() => repo.isEmpty)
    const length = computed(() => repo.length)
</script>

<template>
    <AppBtn v-if="isEmpty" :id="id" class="me-2" icon="bell" variant="secondary">
        <Fa icon="bell"/>
        <AppBadge no-absolute tooltip variant="dark">
            {{ length }}
        </AppBadge>
    </AppBtn>
    <AppDropdown v-else :id="id" class="me-1" end>
        <template #toggle="{id: dropdownId}">
            <AppBtn
                :id="dropdownId"
                aria-exanded="false"
                class="dropdown-toggle"
                data-bs-auto-close="outside"
                data-bs-toggle="dropdown">
                <Fa icon="bell"/>
                <AppBadge tooltip>
                    {{ length }}
                </AppBadge>
            </AppBtn>
        </template>
        <AppNotificationCategory
            v-for="[category, notifications] in categories"
            :key="category"
            :category="category"
            :notifications="notifications"/>
    </AppDropdown>
</template>
