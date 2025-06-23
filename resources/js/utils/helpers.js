export function formatPrice(value, symbol = ' ₽') {
    value = parseInt(value);
    if (!value || typeof value !== 'number') {
        return '0' + symbol;
    }

    return value.toLocaleString('ru-RU', {
        maximumFractionDigits: 2,
    }) + symbol;
}