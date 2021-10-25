import type {AxiosResponse, Method} from 'axios'
import axios from 'axios'
import mitt from 'mitt'

export const emitter = mitt()

axios.interceptors.response.use(
    response => response,
    error => {
        emitter.emit('error', error)
    }
)

export async function request(data: Record<string, unknown>, method: Method, url: string): Promise<Record<string, unknown> | null> {
    const response = await axios.request<typeof data, AxiosResponse<Record<string, unknown>>>({
        data,
        headers: {
            Accept: 'application/ld+json',
            'Content-Type': 'application/json'
        },
        method,
        url
    })
    return typeof response !== 'undefined' ? response.data : null
}

export async function requestForm(form: HTMLFormElement, method: Method): Promise<Record<string, unknown> | null> {
    const data: Record<string, unknown> = {}
    new FormData(form).forEach((value, key) => {
        data[key] = value
    })
    return request(data, method, form.action)
}
