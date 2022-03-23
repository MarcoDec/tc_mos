<script lang="ts" setup>
import type { FormField, FormValue } from "../../../../types/bootstrap-5";
import {
  computed,
  ComputedRef,
  defineEmits,
  onMounted,
  reactive,
  ref,
} from "@vue/runtime-core";
import {
  useNamespacedActions,
  useNamespacedGetters,
} from "vuex-composition-helpers";
import { Actions, Getters } from "../../../../store/attributs";
import { Actions as ActionFamily, Getters as GettersFamily } from "../../../../store/famillies";

const emit = defineEmits<(e: "update:modelValue", value: FormValue) => void>();


const fetchCountry = useNamespacedActions<ActionFamily>("famillies", ["getFamily",]).getFamily;
const findByAttribut = useNamespacedActions<Actions>("attributs", ["findByAttribut",]).findByAttribut;

const options = useNamespacedGetters<GettersFamily>("famillies", ["options"]).options;
const listFields = useNamespacedGetters<Getters>("attributs", ["fields"]).fields;

const val = reactive<{ composant: string | null }>({ composant: null });

console.log("hello", listFields.value);
console.log("val===", val);

const fields = computed<FormField[]>(()=> [
  {
    label: "Composant",
    name: "composant",
    tab: true,
    icon: "puzzle-piece",
    active: true,
    children: [
      {
        label: "General",
        name: "General",
        children: [
          { label: "Désignation ", name: "designation" },
          {
            label: "Famille",
            name: "famille",
            type: "select",
            options: options.value,
          },
          { label: "Unité", name: "unite", type: "select" },
          { label: "poids (g) ", name: "code" },
        ],
      },
      {
        label: "Fabricant",
        name: "Fabricant",
        children: [
          { label: "Fabricant ", name: "Fabricant " },
          { label: "Référence du Fabricant", name: "refF" },
        ],
      },
    ],
  },
  {
    label: "Fournisseur",
    name: "Fournisseur",
    tab: true,
    icon: "puzzle-piece",
    children: [
      { label: "Fournisseur ", name: "gestion", type: "select" },
      { label: "Référence Fournisseur", name: "refFournisseur" },
      { label: "Incoterms   ", name: "incoterms", type: "select" },
      { label: "Délai de livraison moyen (jour ouvrés) ", name: "delai" },
      { label: "Moq ", name: "moq", type: "number" },
      { label: "Quantité par conditionnement ", name: "qte" },
      { label: "Type de conditionnement ", name: "type" },
    ],
  },
  {
    label: "Prix",
    name: "Prix",
    tab: true,
    icon: "puzzle-piece",
    children: [
      fieldPrix
    ],
  },
  {
    label: "Attributs",
    name: "Attributs",
    tab: true,
    icon: "puzzle-piece",
    children: listFields.value
  },
]);


const fieldPrix: FormField = {
  children: [
    { label: "Désignation ", name: "designation" },
    { label: "Famille", name: "famille", type: "select" },
    { label: "Unité", name: "unite", type: "select" },
    { label: "Poids (g) ", name: "code" },
  ],
  label: "Prix ",
  name: "prix",
  btn: true
};

const fieldsPrix = ref<FormField[][]>([]);

function click() {
  const tab = { ...fieldPrix };
  tab.label += fieldsPrix.value.push([tab]);
}




function input(e: Readonly<Event>): void {
  emit("update:modelValue", (e.target as HTMLInputElement).value)
  val.composant = (e.target as HTMLInputElement).value
  findByAttribut(val.composant)
}
onMounted( () => {
   fetchCountry();
   click
});
</script>

<template>
  <AppForm id="login" :fields="fields" v-model="val" @input="input" @click="click">
  </AppForm>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
.overflow-auto {
  overflow: initial !important;
}
</style>
