import {FiniteStateMachine, Repository} from '../modules'

export default class FiniteStateMachineRepository extends Repository {
    use = FiniteStateMachine

    create(id) {
        this.save({id}, id)
    }

    error(id, error, status) {
        const machine = {id, loading: false, status}
        if (status === 422)
            machine.violations = error.violations
        else
            machine.error = error
        this.save(machine)
    }

    load(id) {
        this.reset(id)
        this.save({id, loading: true})
    }
}
