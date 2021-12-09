import * as Cookies from '../../../cookie'
import {MutationTypes} from '.'
import type {RootState} from '../../index'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {fetchApi} from '../../../api'

export enum ActionTypes {
    FETCH_CURRENCY = 'FETCH_CURRENCY',
    UPDATE_DATA = 'UPDATE_DATA',

}

type ActionContext = VuexActionContext<State, RootState>


export const actions = {

    async [ActionTypes.FETCH_CURRENCY]({commit}: ActionContext): Promise<void> {
       const response = await fetchApi('/api/currencies', {
            method: 'get',
            headers: {'Content-Type': 'application/json', "Authorization": 'Bearer ' + Cookies.get('token')}

        })
        const list = response['hydra:member']

        commit(MutationTypes.GET_CURRENCY, list)
    },
    async [ActionTypes.UPDATE_DATA]({commit}: ActionContext,payload: {active: boolean}): Promise<void> {
        const id = Cookies.get('idItem')
        if (typeof id === 'undefined')
            return

        const response = await fetch('/api/currencies/'+ id, {
            body: JSON.stringify({
                active: payload.active,
            }),
            method: 'patch',
            headers: {'Content-Type': ' application/merge-patch+json', "Authorization": 'Bearer ' + Cookies.get('token')}
        })

        console.log('ress --->',response)
    }

}

export type Actions = typeof actions
