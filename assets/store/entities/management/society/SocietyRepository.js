import {EntityRepository, Society} from '../../../modules'

export default class SocietyRepository extends EntityRepository {
    use = Society
    url = '/api/societies'

    save(records, vue = null) {
        if (Array.isArray(records)) {
            const saved = []
            for (const record of records)
                saved.push(this.save(record, vue))
            return saved
        }
        if (typeof records.address.id !== 'string') {
            records.address.societyId = records.id
            records.address.id = `${this.use.entity}-${records.id}`
        }
        return super.save(records, vue)
    }
}
