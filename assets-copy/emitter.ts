import mitt from 'mitt'

const emitter = mitt<{error: undefined}>()

export default emitter

export declare type Emitter = typeof emitter
