import { defineStore } from "pinia";
import generatePrice from "./componentSuppliersPrices";

export default function generateComponentSupplier(cs) {
  return defineStore(`component-suppliers/${cs.id}`, {
    actions: {
      push() {
        this.prices.push(generatePrice(prices.id));
      },
      blur() {
        this.opened = false;
        this.selected = false;
      },
      dispose() {
        this.$reset();
        this.$dispose();
      },
      async remove() {
        this.prices.remove(cs.id);
        this.dispose();
      },
      focus() {
        this.root.blur();
        this.selected = true;
        this.open();
      },
    },
    getters: {
      rowspan: (state) => state.prices.length + 1,
      row: (state) => (fields) => {
        let row = { rowspan: this.rowspan };
        for (const field of fields) {
          if (typeof field.children == "undefined")
            row[field.name] = state[field.name];
          else row = { ...row, ...state[field.name][0].row(field.children) };
        }
        return row;
      },
      rows(state) {
        return (fields) => {
          const rows = [this.row(fields)];
          console.log('aaa');
          const priceFields = fields.find(
            (field) => field.name === "prices"
          ).children;
          for (let i = 1; i < state.prices; i++)
            rows.push(state.prices[i].row(priceFields));
          rows.push(priceFields);
          return rows;
        };
      },
    },
    state: () => ({
      ...cs,
      prices: cs.prices.map((price) => generatePrice(price)),
    }),
  })();
}
