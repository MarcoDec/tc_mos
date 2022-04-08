import type { ComputedGetters, State } from ".";
import type { StoreActionContext } from "..";

declare type ActionContext = StoreActionContext<State, ComputedGetters>;

export const actions = {
  async load(
    { commit, dispatch }: ActionContext,
    playload: number
  ): Promise<void> {
    const needs = [
      {
        id: 1,
        ref: "111444",
        total: "888888",
      },
      {
        id: 2,
        ref: "8888888",
        total: "9999999",
      },
      {
        id: 3,
        ref: "788855",
        total: "4445555544",
      },
      {
        id: 4,
        ref: "111111",
        total: "33333",
      },
      {
        id: 5,
        ref: "111111",
        total: "33333",
      },
      {
        id: 6,
        ref: "111111",
        total: "33333",
      },
      {
        id: 7,
        ref: "111111",
        total: "33333",
      },
      {
        id: 8,
        ref: "111111",
        total: "33333",
      },
      {
        id: 9,
        ref: "111111",
        total: "33333",
      },
      {
        id: 10,
        ref: "111111",
        total: "33333",
      },
      {
        id: 11,
        ref: "111111",
        total: "33333",
      },
    ];

    console.log("aaaa", needs);

    commit("needs", needs);
  },
  async show(
    { commit, getters }: ActionContext,
    infinite: { loaded: () => void; complete: () => void }
  ): Promise<void> {
    console.log("je suis ici");

    commit("show");
    console.log("je suis ici222", getters.hasNeeds);

    if (getters.hasNeeds) {
        console.log("ifff");

      infinite.loaded();
    } else {
        console.log("elseee");

      infinite.complete();
    }
  },
};

export declare type Actions = typeof actions;
