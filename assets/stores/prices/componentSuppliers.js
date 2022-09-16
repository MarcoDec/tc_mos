import { defineStore } from "pinia";
import generateComponentSupplier from "./componentSupplier";

export default defineStore("componentSuppliers", {
  actions: {
    async fetch() {
      const response = [
        {
          delai: 5,
          delete: true,
          id: 1,
          indice: 1,
          moq: 1,
          name: "CAMION FR-MD",
          poidsCu: "bbbbb",
          prices: [
            {
              delete: false,
              id: 1,
              price: 1000,
              quantite: 100,
              ref: "afsfsfss",
              update: true,
              update2: false,
            },
            {
              delete: false,
              id: 2,
              price: 1000,
              quantite: 30,
              ref: "azertsscssy",
              update: true,
              update2: false,
            },
          ],
          proportion: "aaaaaa",
          reference: "ccc",
          update: true,
          update2: false,
        },
        {
          delai: 15,
          delete: true,
          id: 2,
          indice: 2,
          moq: 2,
          name: "CAMION",
          poidsCu: "aaaa",
          prices: [
            {
              delete: false,
              id: 1,
              price: 100,
              quantite: 50,
              ref: "azerty",
              update: true,
              update2: false,
            },
            {
              delete: false,
              id: 2,
              price: 100,
              quantite: 50,
              ref: "azerty",
              update: true,
              update2: false,
            },
            {
              delete: false,
              id: 3,
              price: 100,
              quantite: 50,
              ref: "azerty",
              update: true,
              update2: false,
            },
            {
              delete: false,
              id: 4,
              price: 100,
              quantite: 50,
              ref: "azerty",
              update: true,
              update2: false,
            },
          ],
          proportion: "vvvvvv",
          reference: "wwwww",
          update: true,
          update2: false,
        },
      ];
      for (const price of response)
        this.items.push(generateComponentSupplier(price, this));
    },
  },
  getters: {
    rows: (state) => (fields) => {
      let rows = [];
      for (const item of state.items) {
        rows = rows.concat(item.rows(fields));
      }
      rows.push(fields);
      return rows;
    },
  },
  state: () => ({
    items: [],
  }),
});
