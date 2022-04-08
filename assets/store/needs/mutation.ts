import type { State } from ".";

export const mutations = {
  needs(state: State, needs: []): void {
    state.needs = needs;
    console.log("mmm", state.needs);
  },
  show(state: State): void {
      const lenght = state.needs.length
    for (let i = 0; i < 5 && i < lenght; i++)
      state.displayed.push(state.needs.shift());
    state.page++;
    console.log("sssss", state.displayed);
  },
};

export declare type Mutations = typeof mutations;
