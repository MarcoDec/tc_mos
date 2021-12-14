import type {Slot, VNode} from 'vue'
import type {DeepReadonly} from '../types/types'
import type {FunContext} from '../types/vue'

export function useSlots(context: DeepReadonly<FunContext>): VNode[][] {
    const children: VNode[][] = []
    for (const slot of Object.values(context.slots))
        if (typeof slot !== 'undefined')
            children.push((slot as Slot)())
    return children
}
