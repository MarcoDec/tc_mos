const formatter = new Intl.NumberFormat('fr')

export function format(num) {
    return formatter.format(num)
}
