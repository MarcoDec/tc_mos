import { defineStore } from "pinia";
import generatePrice from "./price";

export default defineStore("prices", {
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
              id: 3,
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
              id: 2,
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
      for (const price of response) this.items.push(generatePrice(price, this));
    },
  },
  getters: {
    rows(state) {
      const rows = [];

      for (const id of Object.keys(state.items)) {
        console.log('id', id);

        const row = [`prices/${id}`];
        console.log('row', row);
        const prices = [...this.items.prices];
        console.log('price-->', prices);

        const first = prices.shift();
        if (typeof first === "string") row.push(first);
        rows.push(row);
        for (const price of prices) rows.push([price]);
      }
      return rows;
    },
  },
  state: () => ({ items: [] }),
});
