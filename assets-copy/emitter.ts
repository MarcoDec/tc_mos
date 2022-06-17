import mitt from 'mitt'

const emitter = mitt<{error: undefined}>()

export default emitter

export type Emitter = typeof emitter
