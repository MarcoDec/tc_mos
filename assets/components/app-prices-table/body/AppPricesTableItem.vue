<script setup>
import { defineProps, computed } from "vue";
import { isObject } from "@vueuse/core";
import AppPricesTableAddItems from "./AppPricesTableAddItems.vue";


const props = defineProps({
  item: { required: true, type: Object },
  fieldsComponenentSuppliers: { required: true, type: Array },
  fieldsComponenentSuppliersPrices: { required: true, type: Array },
  items: { required: true, type: Array },
});
const nbTr = computed(()=>{
    const nbItems = props.item.prices.length
    if (nbItems>0) return nbItems
    return 1 
})

   function range (n){
    return Array.from({length:n},(value,key)=>key+1)
   }
</script>

<template>
    <tr v-for="(i, index) in range(nbTr)" :key="index">
        <td :rowspan="range(nbTr).length+1" v-if="(index=== 0)">
            <button  class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.id" @click="update">
                <Fa icon="pencil"/>
            </button>
            <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted">
                <Fa icon="trash"/>
            </button>
        </td>
        <template v-for="field in fieldsComponenentSuppliers" :key="field.name">
            <td :rowspan="range(nbTr).length+1" v-if="(index=== 0)&&(field.name !== 'prices')">
                <template v-if="item[field.name] !== null">
                    <div v-if="field.name !== 'prices'">
                        <div v-if="field.type === 'select'">
                            <template v-if="isObject(item[field.name])">
                                <span v-if="field.options.label(item[field.name]['@id']) !== null">{{ field.options.label(item[field.name]['@id']) }}</span>
                                <span v-else>{{ item[field.name] }}</span>
                            </template>
                            <template v-else>
                                <span v-if="field.options.label(item[field.name]) !== null">{{ field.options.label(item[field.name]) }}</span>
                                <span v-else>{{ item[field.name] }}</span>
                            </template>
                        </div>
                        <div v-else-if="field.type === 'measure'">
                            <div class="text-center">
                                {{ item[field.name].value }} {{ item[field.name].code }}
                            </div>
                        </div>
                        <div v-else-if="field.type === 'date'">
                            {{ item[field.name].substring(0, 10) }}
                        </div>
                        <div v-else-if="field.type === 'boolean'">
                            <AppSwitch :id="`${field.name}_${id}`" :disabled="true" :field="field" form="" :model-value="item[field.name]"/>
                        </div>
                        <div v-else-if="field.type === 'multiselect-fetch'">
                            {{ multiSelectResults[field.name] }}
                        </div>
                        <div v-else-if="field.type === 'link'">
                            <a v-if="item[field.name] !== null && item[field.name] !== ''" :href="item[field.name]" target="_blank">Download file</a>
                        </div>
                        <div v-else>
                            <span v-if="isObject(item[field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item[field.name] }}</span>
                            <span v-else>{{ item[field.name] }}</span>
                            
                        </div>
                    </div>  
                </template>
            </td>
            <td v-if="field.name === 'prices'" >
                <button  class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.id" @click="update">
                    <Fa icon="pencil"/>
                </button>
                <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted">
                    <Fa icon="trash"/>
                </button>
            </td>
        </template>
        <td v-for="field in fieldsComponenentSuppliersPrices" :key="field.name">
            <template v-if="item.prices[index][field.name] !== null">
                <div v-if="field.type === 'select'">
                    <template v-if="isObject(item.prices[index][field.name])">
                        <span v-if="field.options.label(item.prices[index][field.name]['@id']) !== null">{{ field.options.label(item.prices[index][field.name]['@id']) }}</span>
                        <span v-else>{{ item.prices[index][field.name] }}</span>
                    </template>
                    <template v-else>
                        <span v-if="field.options.label(item.prices[index][field.name]) !== null">{{ field.options.label(item.prices[index][field.name]) }}</span>
                        <span v-else>{{ item.prices[index][field.name] }}</span>
                    </template>
                </div>
                <div v-else-if="field.type === 'measure'">
                    <div class="text-center">
                        {{ item.prices[index][field.name].value }} {{ item.prices[index][field.name].code }}
                    </div>
                </div>
                <div v-else-if="field.type === 'date'">
                    {{ item.prices[index][field.name].substring(0, 10) }}
                </div>
                <div v-else-if="field.type === 'boolean'">
                    <AppSwitch :id="`${field.name}_${id}`" :disabled="true" :field="field" form="" :model-value="item.prices[index][field.name]"/>
                </div>
                <div v-else-if="field.type === 'multiselect-fetch'">
                    {{ multiSelectResults[field.name] }}
                </div>
                <div v-else-if="field.type === 'link'">
                    <a v-if="item.prices[index][field.name] !== null && item.prices[index][field.name] !== ''" :href="item.prices[index][field.name]" target="_blank">Download file</a>
                </div>
                <div v-else>
                    <span v-if="isObject(item.prices[index][field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item.prices[index][field.name] }}</span>
                    <span v-else>{{ item.prices[index][field.name] }}</span>
                </div>
            </template>
        </td>
    </tr>
    <AppPricesTableAddItems :fields="fieldsComponenentSuppliersPrices" />

</template>
