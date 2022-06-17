const COL_MIN = 1
const COL_MAX = 12

export function colValidator(value: number): boolean {
    return value >= COL_MIN && value <= COL_MAX
}
