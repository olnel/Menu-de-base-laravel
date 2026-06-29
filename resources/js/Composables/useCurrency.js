import { usePage } from "@inertiajs/vue3";
import { formatCurrency } from "../../Utils/formatCurrency.js";

/**
 * Composable qui expose la devise courante et une fonction de formatage monétaire.
 *
 * @param {object} [defaultOptions] - Options par défaut passées à Intl.NumberFormat
 * @returns {{ devise: string, formatCurrency: (value: number, options?: object) => string }}
 *
 * @example
 *   // Dans un composant Vue
 *   const { devise, formatCurrency } = useCurrency();
 *   formatCurrency(1500);          // "1 500,00 Ar"
 *   formatCurrency(1500, { maximumFractionDigits: 0 }); // "1 500 Ar"
 */
export function useCurrency(defaultOptions = {}) {
    const page = usePage();

    /**
     * Formate une valeur monétaire avec la devise courante des props de la page.
     * L'accès à `page.props.devise` se fait à l'intérieur de la fonction pour
     * que Vue tracke la dépendance réactive lors de l'appel dans le template.
     *
     * @param {number|null|undefined} value - La valeur à formater
     * @param {object} [options] - Options supplémentaires (fusionnées avec defaultOptions)
     * @returns {string}
     */
    const format = (value, options = {}) => {
        return formatCurrency(value, page.props.devise || "MGA", { ...defaultOptions, ...options });
    };

    /**
     * La devise courante extraite des props de la page (non réactive — valeur figée).
     * Pour un usage réactif, préférer utiliser `formatCurrency()` directement.
     */
    const devise = page.props.devise || "MGA";

    return { devise, formatCurrency: format };
}
