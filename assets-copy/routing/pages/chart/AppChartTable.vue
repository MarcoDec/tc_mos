<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{
        data: {labels: string}
        titleX: string
        titleY: string
    }>()
</script>

<template>
    <table
        class="table table-bordered table-hover table-responsive table-sm table-striped">
        <thead>
            <tr>
                <th>{{ titleX }}</th>
                <th v-for="code in data.labels" :key="code.label">
                    {{ code }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ titleY }}</td>
                <td v-for="(item, index) in data.datasets[0].data" :key="index">
                    {{ item }}
                </td>
            </tr>
        </tbody>
    </table>
</template>
