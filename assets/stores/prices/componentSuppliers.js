import api from '../../api'
import { defineStore } from "pinia";
import { useComponentSuppliersPricesStore } from "./componentSuppliersPrices";

export const useComponentSuppliersStore = defineStore('componentSuppliers', {
  actions: {
    async fetch() {
      const response = await api('/api/supplier-components', 'GET')
      this.items = await response['hydra:member']
      console.log('items', this.items)
    },
    async fetchByComponent(idComponent){
      const response = await api(`/api/supplier-components?component=${idComponent}`, 'GET')
      this.items = await response['hydra:member']
      console.log('response', response);

    },
    async fetchBySupplier(idSupplier){
      const response = await api(`/api/supplier-components?supplier=${idSupplier}`, 'GET')
      this.items = await response['hydra:member']
    },
    async fetchPricesById(id) {
      const pricesStore = useComponentSuppliersPricesStore();
      const prices = await pricesStore.fetchPricesByComponent(id);
      return prices;
    },
    async fetchPricesForItems() {
        const promises = this.items.map(async (item) => {
          const prices = await this.fetchPricesById(item["@id"]);
          return { ...item, prices };
        })
      const itemsWithPrices = await Promise.all(promises)
      this.itemsPrices= itemsWithPrices
      return itemsWithPrices;
    } 
  },
  getters: {
    componentSuppliersItems: state => state.itemsPrices.map(item => {
      const componentSupplier = {
          '@id': item['@id'],
          proportion: item.proportion,
          delai: {
            code: item.deliveryTime.code,
            value: item.deliveryTime.value
          },
          moq: {
            code: item.moq.code,
            value: item.moq.value
          },
          poidsCu:  {
            code: item.copperWeight.code,
            value: item.copperWeight.value
          },
          packaging:  {
            code: item.packaging.code,
            value: item.packaging.value
          },
          packagingKind: item.packagingKind,
          incoterms: item.incoterms,
          reference: item.code,
          indice: item.index,
          prices: item.prices
      }
      return componentSupplier
  })
   
  },
  state: () => ({
    items: [],
    itemsPrices: []
  }),
});
