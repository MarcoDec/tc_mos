export function isNumeric(num: string): boolean{
    return /^\d+(,|\.)?\d*$/.test(num)
}

export function toFloat(num: string): number | null{
    return isNumeric(num) ? parseFloat(num) : null
}
