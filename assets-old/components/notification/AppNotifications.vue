<script setup>
    import {computed, onMounted} from 'vue'
    import AppDropdown from '../nav/AppDropdown.vue'
    import AppNotificationCategory from './AppNotificationCategory.vue'
    import useNotifications from '../../stores/notification/notifications'

    defineProps({id: {required: true, type: String}})

    const notifications = useNotifications()

    const length = computed(() => notifications.length)
    const isEmpty = computed(() => notifications.isEmpty)
    const categories = computed(() => notifications.findByCategories)
    const variant = computed(() => (length.value > 0 ? 'danger' : 'dark'))

    onMounted(async () => {
        await notifications.fetch()
    })
</script>

<template>
    <AppBtn v-if="isEmpty" :id="id" class="me-2" icon="bell" variant="secondary">
        <Fa icon="bell"/>
        <AppBadge :variant="variant" no-absolute tooltip>
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
                <AppBadge :variant="variant" tooltip>
                    {{ length }}
                </AppBadge>
            </AppBtn>
        </template>
        <AppNotificationCategory
            v-for="[category, notification] in categories"
            :key="category"
            :category="category"
            :notifications="notification"/>
    </AppDropdown>
</template>
