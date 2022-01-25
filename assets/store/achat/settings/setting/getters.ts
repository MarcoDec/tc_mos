import type {DeepReadonly} from '../../../../types/types'
import type {State} from '.'

export type Getters = {
  
}

type GettersValues = DeepReadonly<{[key in keyof Getters]: ReturnType<Getters[key]>}>

export const getters: Getters = {
    
}
