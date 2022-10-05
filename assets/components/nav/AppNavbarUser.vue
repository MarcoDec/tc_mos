<script setup>
    import useUser from '../../stores/security'

    const user = useUser()
</script>

<template>
    <div class="text-white">
        <span class="me-1 navbar-text">
            <Fa icon="user-circle"/>
        </span>
        <span class="me-1 navbar-text">
            {{ user.name }}
        </span>
    </div>
</template>
