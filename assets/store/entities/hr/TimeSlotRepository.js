import {EntityRepository, TimeSlot} from '../../modules'

export default class TimeSlotRepository extends EntityRepository {
    use = TimeSlot
    url = '/api/time-slots'
}
