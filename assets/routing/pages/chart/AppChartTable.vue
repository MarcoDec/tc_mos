<script lang="ts" setup>
const props = defineProps({
  data: { required: true, type: Array },
  titleX: String,
  titleY: String,
});

</script>

<template>
  <table
    class="table table-hover table-striped table-responsive table-sm table-bordered"
  >
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
