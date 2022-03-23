import type {State as RootState} from '..'
import type {State} from '.'
import type {State as Attributs} from './attribut'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateAttribut} from './attribut'

type ActionContext = VuexActionContext<State, RootState>


export const actions = {
    async findByAttribut({dispatch}: ActionContext, familly: string): Promise<void> {
       if (familly === '1'){
        console.log('ici le valeur 1');
        const response : Attributs[] = [
            {
                id: 1,
                name: 'Couleur',
            },
            {
                id: 2,
                name: 'Voltage',
            },
            {
                id: 3,
                name: 'Nombre des brins',
            }
        ]
        const attributs = []
        for (const list of response)
        attributs.push(dispatch(
                'registerModule',
                {module: generateAttribut(list), path: ['attributs', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(attributs)
       }
       else if (familly === '2'){
        console.log('ici le valeur 2');
        const response : Attributs[] = [
            {
                id: 1,
                name: 'Dimension',
            },
            {
                id: 2,
                name: 'Couleur',
            },
            {
                id: 3,
                name: 'Puissance',
            }
        ]
        const attributs = []
        for (const list of response)
        attributs.push(dispatch(
                'registerModule',
                {module: generateAttribut(list), path: ['attributs', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(attributs)
       }
       else{
        console.log('ici le valeur default');

        const response : Attributs[] = [
           
        ]
        const attributs = []
        for (const list of response)
        attributs.push(dispatch(
                'registerModule',
                {module: generateAttribut(list), path: ['attributs', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(attributs)
       }
    },
    
}

export type Actions = typeof actions
