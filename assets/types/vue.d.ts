import type {DeepReadonly} from './types'
import type {SetupContext} from 'vue'

export type FunContext = DeepReadonly<Omit<SetupContext, 'expose'>>
export type FunProps = Readonly<Record<string, unknown>>
