/**
 * Formate un nombre en monnaie avec Intl.NumberFormat.
 *
 * @param {number|null|undefined} value     - La valeur à formater
 * @param {string}                [devise]  - Code devise (ex: "MGA", "EUR", "USD")
 * @param {object}                [options] - Options supplémentaires passées à Intl.NumberFormat
 * @returns {string} La valeur formatée
 *
 * @example
 *   formatCurrency(1500, "MGA")           // "1 500,00 Ar"
 *   formatCurrency(1500, "EUR")           // "1 500,00 €"
 *   formatCurrency(1500, "MGA", { maximumFractionDigits: 0 }) // "1 500 Ar"
 */
export function formatCurrency(value, devise = "MGA", options = {}) {
    const numValue = value ?? 0;
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: devise,
        ...options,
    }).format(numValue);
}
