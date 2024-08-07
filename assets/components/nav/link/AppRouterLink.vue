<script setup>
    import {useRouter} from 'vue-router'

    const props = defineProps({
        css: {default: null, type: String},
        to: {required: true, type: Object}
    })
    const router = useRouter()
    const handleNavigate = (event, navigate) => {
        if (event.ctrlKey) {
            const url = router.resolve(props.to).href
            window.open(url, '_blank')
        } else {
            navigate()
        }
    }
</script>

<template>
    <RouterLink :to="to" custom>
        <template #default="{navigate}">
            <span :class="css" class="pointer" @click="event => handleNavigate(event, navigate)">
                <slot/>
            </span>
        </template>
    </RouterLink>
</template>
