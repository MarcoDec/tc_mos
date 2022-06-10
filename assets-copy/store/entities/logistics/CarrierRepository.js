import {Carrier, EntityRepository} from '../../modules'

export default class CarrierRepository extends EntityRepository {
    use = Carrier
    url = '/api/carriers'

    save(records, vue = null) {
        if (Array.isArray(records)) {
            const saved = []
            for (const record of records)
                saved.push(this.save(record, vue))
            return saved
        }
        if (typeof records.address.id !== 'string') {
            records.address.carrierId = records.id
            records.address.id = `${this.use.entity}-${records.id}`
        }
        return super.save(records, vue)
    }
}
