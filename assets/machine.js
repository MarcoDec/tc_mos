import {assign, createMachine} from 'xstate'
import {useMachine as useXMachine} from '@xstate/vue'

export function useMachine(id) {
    return useXMachine(createMachine({
        context: {violations: []},
        id,
        initial: 'loading',
        states: {
            error: {on: {submit: {actions: [assign({violations: []})], target: 'loading'}}},
            form: {on: {submit: {actions: [assign({violations: []})], target: 'loading'}}},
            loading: {
                on: {
                    fail: {actions: [assign((context, {violations}) => ({violations}))], target: 'error'},
                    success: {target: 'form'}
                }
            }
        }
    }))
}

export function useTableMachine(id) {
    return useXMachine(createMachine({
        context: {updated: null, violations: []},
        id,
        initial: 'search',
        states: {
            create: {
                initial: 'form',
                on: {
                    search: {actions: ['reset'], internal: false, target: 'search'},
                    submit: {actions: ['resetViolations'], internal: false, target: 'create.loading'},
                    update: {actions: ['update'], internal: false, target: 'update'}
                },
                states: {
                    error: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}},
                    form: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}},
                    loading: {
                        on: {
                            fail: {actions: ['violate'], internal: true, target: 'error'},
                            success: {internal: true, target: 'form'}
                        }
                    }
                }
            },
            search: {
                initial: 'loading',
                on: {
                    create: {actions: ['reset'], internal: false, target: 'create'},
                    submit: {actions: ['resetViolations'], internal: false, target: 'search.loading'},
                    update: {actions: ['update'], internal: false, target: 'update'}
                },
                states: {
                    form: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}},
                    loading: {on: {success: {internal: false, target: 'form'}}}
                }
            },
            update: {
                initial: 'form',
                on: {
                    create: {actions: ['reset'], internal: false, target: 'create'},
                    search: {actions: ['reset'], internal: false, target: 'search'},
                    submit: {actions: ['resetViolations'], internal: false, target: 'update.loading'}
                },
                states: {
                    error: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}},
                    form: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}},
                    loading: {
                        on: {
                            fail: {actions: ['violate'], internal: true, target: 'error'},
                            success: {internal: false, target: 'form'}
                        }
                    }
                }
            }
        }
    }, {
        actions: {
            reset: assign({updated: null, violations: []}),
            resetViolations: assign({violations: []}),
            update: assign((context, {updated}) => ({updated, violations: []})),
            violate: assign((context, {violations}) => ({violations}))
        }
    }))
}
