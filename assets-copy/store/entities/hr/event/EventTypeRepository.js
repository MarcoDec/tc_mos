import {EntityRepository, EventType} from '../../../modules'

export default class EventTypeRepository extends EntityRepository {
    use = EventType
    url = '/api/event-types'
}
