import { defineStore } from "pinia";

export default function generatePrice(prices) {
  return defineStore(`prices/${prices.id}`, {
    actions: {
      blur() {
        this.opened = false;
        this.selected = false;
      },
      dispose() {
        this.$reset();
        this.$dispose();
      },
      focus() {
        this.root.blur();
        this.selected = true;
        this.open();
      },
    },
    getters: {
      row(state) {
        const rows = [state, state.prices[0]];
        console.log("rows", rows);
        for (const suppliers of state.items) 
        console.log("state", suppliers);

        for (let i = 1; i < suppliers.prices.length; i++)
          rows.push([state.prices[i]]);
      },
      rowspan: (state) => state.prices.length + 1,
    },
    state: () => ({
      items: [
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
      ],
    }),
  })();
}
