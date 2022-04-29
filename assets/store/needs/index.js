import { actions } from './actions';
import { getters } from './getters';
import { mutations } from './mutations';
export function generateNeeds(state) {
    return { actions, getters, mutations, namespaced: true, state };
}