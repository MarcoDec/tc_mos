import type {SetupContext} from 'vue'

export type FunContext = Omit<SetupContext, 'expose'>
export type FunProps = Record<string, unknown>
