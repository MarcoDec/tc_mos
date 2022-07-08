<script lang="ts" setup>
import type { Actions, Getters } from "../../../store/famillyProducts";
import type {
  Actions as ActionsClients,
  Getters as GettersClients,
} from "../../../store/clients";
import type { FormField, FormValue } from "../../../types/bootstrap-5";
import { computed, defineEmits, onMounted, reactive } from "vue";
import {
  useNamespacedActions,
  useNamespacedGetters,
} from "vuex-composition-helpers";

const emit = defineEmits<(e: "update:modelValue", value: FormValue) => void>();

const value = reactive({ pays: "fr" });

const getFamilyProduct = useNamespacedActions<Actions>("famillyProducts", [
  "getFamilyProduct",
]).getFamilyProduct;
const fetchCompagnie = useNamespacedActions<ActionsClients>("clients", [
  "getClients",
]).getClients;

const options = useNamespacedGetters<Getters>("famillyProducts", [
  "options",
]).options;
const optionsClients = useNamespacedGetters<GettersClients>("clients", [
  "options",
]).options;

const fields = computed<FormField[]>(() => [
  {
    label: "Famille de Produit ",
    name: "famille",
    options: options.value,
    type: "select",
  },
  { label: "Réf", name: "ref" },
  { label: "Désignation ", name: "desgniation" },

  {
    label: "Type de Produit",
    name: "type",
    options: [
      { text: "Prototype", value: "1" },
      { text: "EI", value: "2" },
      { text: "Série", value: "3" },
      { text: "Piéce de rechange", value: "4" },
    ],
    type: "select",
  },
  {
    label: "Client principal",
    name: "client",
    options: optionsClients.value,
    type: "select",
  },
  { label: "Infos publiques", name: "info" },
  { label: "Volume annuel prévisionnel", name: "volume" },
  { label: "Conditionnement (type)", name: "condition" },
  { label: "Date d'expiration ", name: "date", type: "date" },
]);

function input(e: Readonly<Event>): void {
  emit("update:modelValue", (e.target as HTMLInputElement).value);
  value.pays = (e.target as HTMLInputElement).value;
}
onMounted(async () => {
  await getFamilyProduct();
  await fetchCompagnie();
});
</script>

<template>
  <AppForm
    id="login"
    v-model="value"
    :fields="fields"
    country-field="pays"
    @input="input"
  />
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
.overflow-auto {
  overflow: initial !important;
}
</style>
