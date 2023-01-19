<script setup>
    import AppNotification from './AppNotification.vue'
    import {computed} from 'vue'

    const props = defineProps({category: {required: true, type: Object}, send: {required: true, type: Function}})
    const accordion = computed(() => `notifications-${props.category.name}`)
    const target = computed(() => `#${accordion.value}`)

    async function read() {
        props.send('submit')
        await props.category.reading()
        props.send('success')
    }

    async function remove() {
        props.send('submit')
        await props.category.remove()
        props.send('success')
    }
</script>

<template>
    <li class="accordion-item bg-gray-800 border border-2 border-dark">
        <h6 class="accordion-header border border-2 border-dark d-flex">
            <button
                :aria-controls="accordion"
                :data-bs-target="target"
                aria-expanded="false"
                class="accordion-button collapsed fw-bold"
                data-bs-toggle="collapse"
                type="button">
                {{ category.name }}
            </button>
            <AppBtn v-if="!category.read" icon="eye" label="Marquer comme lu" @click="read"/>
            <AppBtn icon="trash" label="Supprimer" variant="danger" @click="remove"/>
        </h6>
        <div :id="accordion" class="accordion-collapse collapse">
            <div class="accordion-body">
                <AppNotification
                    v-for="notification in category.notifications"
                    :key="notification.id"
                    :notification="notification"
                    :send="send"/>
            </div>
        </div>
    </li>
</template>
