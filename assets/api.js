export default async function api(url, method = 'GET', body = null) {
    const response = await fetch(url, {
        body: JSON.stringify(body),
        headers: {Accept: 'application/ld+json', 'Content-Type': 'application/json'},
        method
    })
    const content = await response.json()
    return {content, status: response.status}
}
