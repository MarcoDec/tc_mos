import {assign, createMachine} from 'xstate'
import {useMachine as useXMachine} from '@xstate/vue'

export function useMachine(id) {
    return useXMachine(createMachine({
        context: {violations: []},
        id,
        initial: 'loading',
        predictableActionArguments: true,
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

export const tableLoading = ['create.loading', 'search.loading', 'update.loading']

export function useTableMachine(id) {
    return useXMachine(createMachine({
        context: {updated: null, violations: []},
        id,
        initial: 'search',
        predictableActionArguments: true,
        states: {
            create: {
                initial: 'form',
                on: {
                    search: {actions: ['reset'], internal: false, target: 'search'},
                    update: {actions: ['update'], internal: false, target: 'update'}
                },
                states: {
                    error: {on: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}}},
                    form: {on: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}}},
                    loading: {
                        on: {
                            fail: {actions: ['violate'], internal: true, target: 'error'},
                            success: {internal: true, target: 'form'}
                        }
                    }
                }
            },
            search: {
                initial: 'form',
                on: {
                    create: {actions: ['reset'], internal: false, target: 'create'},
                    update: {actions: ['update'], internal: false, target: 'update'}
                },
                states: {
                    form: {on: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}}},
                    loading: {on: {success: {internal: true, target: 'form'}}}
                }
            },
            update: {
                initial: 'form',
                on: {
                    create: {actions: ['reset'], internal: false, target: 'create'},
                    search: {actions: ['reset'], internal: false, target: 'search'},
                    update: {actions: ['update'], internal: false, target: 'update.form'}
                },
                states: {
                    error: {on: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}}},
                    form: {on: {submit: {actions: ['resetViolations'], internal: true, target: 'loading'}}},
                    loading: {
                        on: {
                            fail: {actions: ['violate'], internal: true, target: 'error'},
                            success: {internal: true, target: 'form'}
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
