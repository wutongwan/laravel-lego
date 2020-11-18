export function initGridSort(field) {
    const key = `__lego_orders[${field.getAttribute('data-sort')}]`
    const direction = field.getAttribute('data-sort-direction')
    const url = new URL(location.href)

    if (url.searchParams.get(key) === direction) {
        // 再次点击时取消排序
        url.searchParams.delete(key)
        field.style.color = 'gray'
    } else {
        // 第一次点击时，添加排序参数
        url.searchParams.set(key, direction)
    }
    field.href = url.toString()
}
