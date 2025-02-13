export function formatPrice(value) {
    if (!value || typeof value !== 'number') {
        return '0 ₽';
    }

    return value.toLocaleString('ru-RU', {
        maximumFractionDigits: 2,
    }) + ' ₽';
}