<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from "@vue/runtime-core";
import {
  useNamespacedActions,
  useNamespacedGetters,
} from "vuex-composition-helpers";
import { Actions } from "../../../../store/countries";
import { Getters } from "../../../../store/countries";
import { FormField, FormValue } from "../../../../types/bootstrap-5";

const emit = defineEmits<(e: "update:modelValue", value: FormValue) => void>();
const value = reactive({pays:''})

const fetchCountry = useNamespacedActions<Actions>("countries", ["fetchCountry",]).fetchCountry
const options = useNamespacedGetters<Getters>("countries", ['options']).options

 const fields = computed<FormField[]>(() => [
  { label: "Groupe", name: "groupe" },
  { label: "Nom ", name: "nom " },
  { label: "Adresse  ", name: "adresse" },
  { label: "complément d'adresse", name: "adresse2" },
  { label: "Code postal ", name: "code" },
  { label: "Pays ", name: "pays ", options: options.value, type: "select" },
  { label: "Téléphone", name: "tel", type: "phone" },
  { label: "Email", name: "email" },
  { label: "Site web", name: "site" },
]);

 const fieldsQte = computed<FormField[]>(() => [
  { label: "Gestion en production", name: "gestion", type: "boolean" },
  { label: "Gest. qualité", name: "qte", type: "boolean" },
  { label: "Incoterms   ", name: "incoterms" },
  { label: "Qualité ", name: "quantite" },
  { label: "Open orders enabled", name: "open", type: "boolean" },

]);

 const fieldsComp = computed<FormField[]>(() => [
  { label: "AR demandé*", name: "Ar", type: "boolean" },
  { label: "Devise*", name: "devise" },
  { label: "TVA*", name: "tva" },
  { label: "Open orders enabled*", name: "open", type: "boolean" },

]);
const fieldsCuivre = computed<FormField[]>(() => [
  { label: "CopperIndex", name: "copperIndex ", type: "number" },
  { label: "CopperType", name: "copperType" , type:"select"},
  { label: "Open orders enabled*", name: "open", type: "boolean" },

]);
function handleClick() {
  console.log("add done");
}
function input(e: Readonly<Event>): void {
  emit("update:modelValue", (e.target as HTMLInputElement).value);
  value.pays = (e.target as HTMLInputElement).value
}
onMounted(async () => {
  await fetchCountry();
});
</script>

<template>
  <AppTabs id="gui-start" class="gui-start-content">
    <AppTab id="gui-start-general" active icon="sitemap" title="Général">
      <AppForm id="login" :fields="fields" @submit="handleClick" v-model="value" country-field="pays" @input="input"> </AppForm>
    </AppTab>
    <AppTab id="gui-start-qte" icon="folder" title="Qualité">
        <AppForm id="login" :fields="fieldsQte" @submit="handleClick"> </AppForm>
    </AppTab>
    <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
        <AppForm id="login" :fields="fieldsComp" @submit="handleClick"> </AppForm>
    </AppTab>
    <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
        <AppForm id="login" :fields="fieldsCuivre" @submit="handleClick"> </AppForm>
    </AppTab>
  </AppTabs>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
