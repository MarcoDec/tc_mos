import Api from '../../Api'
import {defineStore} from 'pinia'

export default function generateInvoiceTimeDue(invoiceTimeDue, root) {
    return defineStore(`invoice-time-due/${invoiceTimeDue.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(fields, data) {
                const response = await new Api(fields).fetch(`/api/invoice-time-dues/${this.id}`, 'PATCH', data)
                if (response.status === 422)
                    throw response.content.violations
                this.$state = {root: this.root, ...response.content}
            }
        },
        state: () => ({root, ...invoiceTimeDue})
    })()
}
