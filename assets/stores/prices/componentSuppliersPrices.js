import { defineStore } from "pinia";

export default function generatePrice(price) {
  return defineStore(`price/${price.id}`, {
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
      row: (state) => (fields) => {
        let row = {};
        for (const field of fields) {
          row[field.name] = state[field.name];
        }
        return row;
      },
    },
    state: () => ({ ...price }),
  })();
}
