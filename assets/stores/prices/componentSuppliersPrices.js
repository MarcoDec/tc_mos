import api from '../../api'
import { defineStore } from "pinia";

export const useComponentSuppliersPricesStore = defineStore('componentSuppliersPrices', {
  actions: {
    async fetchPricesByComponent(idComponent){
      const response = await api(`/api/supplier-component-prices?component=${idComponent}`, 'GET')
      this.itemPricesComponent = await response['hydra:member']
      return this.itemPricesComponent
    }
         
  },
  getters: {
  },
  state: () => ({
    itemPricesComponent: [],
  }),
});
