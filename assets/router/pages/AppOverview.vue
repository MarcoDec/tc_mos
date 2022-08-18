<script setup>
    import {useOverviewStore} from '../../stores/overview/overview'
    const storeOverview = useOverviewStore()
</script>

<template>
    <div class="bg-secondary card">
        <div class="row">
            <div class="col">
                count: {{ storeOverview.count }}
            </div>
            <div class="col">
                <div v-for="(item, index) in storeOverview.items" :key="index" class="col">
                    <span> {{ item.name }} : {{ item.value }}  </span>
                </div>
            </div>
        </div>
    </div>
</template>
