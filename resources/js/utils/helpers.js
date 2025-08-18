export function formatPrice(value, symbol = ' ₽') {
    value = Math.round(Number(value));
    if (!value || typeof value !== 'number') {
        return '0' + symbol;
    }

    return value.toLocaleString('ru-RU', {
        maximumFractionDigits: 2,
    }) + symbol;
}