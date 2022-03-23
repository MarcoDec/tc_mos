<script lang="ts" setup>
import type { Actions, Getters } from "../../../../store/components";
import { computed, onMounted } from "@vue/runtime-core";
import {
  useNamespacedActions,
  useNamespacedGetters,
} from "vuex-composition-helpers";
import AppComponentCreate from "./AppComponentCreate.vue";
import { useRoute } from "vue-router";

const route = useRoute();
const title = "Créer un Composant";
const modalId = computed(() => "target");
const target = computed(() => `#${modalId.value}`);

const fetchSuppliers = useNamespacedActions<Actions>("components", ["fetchComponent",]).fetchComponent;
const { items } = useNamespacedGetters<Getters>("components", ["items"]);

onMounted(async () => {
  await fetchSuppliers();
});

const fields = [
  {
    create: true,
    filter: true,
    label: "Img",
    name: "img",
    sort: true,
    type: "text",
    update: false
  },
  {

    create: true,
    filter: true,
    label: "Référence",
    name: "ref",
    sort: true,
    type: "text",
    update: true
  },
  {
    create: true,
    filter: true,
    label: "Indice",
    name: "indice",
    sort: true,
    type: "text",
    update: true
  },
  {
    create: true,
    filter: true,
    label: "Désignation",
    name: "designation",
    sort: true,
    type: "text",
    update: true
  },
  {
    create: true,
    filter: true,
    label: "Famille",
    name: "famille",
    sort: true,
    type: "text",
    update: true
  },
  {
    create: true,
    filter: true,
    label: "Fournisseurs",
    name: "fournisseurs",
    sort: true,
    type: "text",
    update: true,
  },
  {
    create: true,
    filter: true,
    label: "Stocks",
    name: "stocks",
    sort: true,
    type: "text",
    update: true,
  },
  {
    create: true,
    filter: true,
    label: "Besoins enregistrés",
    name: "besoin",
    sort: true,
    type: "text",
    update: true,
  },
   {
    create: true,
    filter: true,
    label: "Etat",
    name: "etat",
    sort: true,
    type: "text",
    update: true,
  }
];
</script>

<template>
  <AppRow>
    <h1 class="col">
      <Fa class="me-3" icon="user-tag" />
      Composants
    </h1>
    <AppCol>
      <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
        Créer
      </AppBtn>
    </AppCol>
  </AppRow>

  <AppModal :id="modalId" class="four" :title="title">
    <AppComponentCreate />
  </AppModal>

  <AppCollectionTable
    :id="route.name"
    :fields="fields"
    :items="items"
    pagination
  >
    <template #etat="{ item }">
      <td><AppTrafficLight :item="item" /></td>
    </template>
  </AppCollectionTable>
</template>
