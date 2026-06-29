// Configuration du menu
export const LOUINE_MENU = [
    {
        title: "Accueil",
        key: "home",
        icon: ['fas', 'fa-home'],
        route: route('home'),
    },
    {
        title: "Prêt à porter",
        key: "pret-a-porter",
        icon: ['fas', 'fa-tshirt'],
        sous_menu: [
            {title: "Femme", key: "femme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Homme", key: "homme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Enfants", key: "enfant", route: route('femme'), icon: ['fas', 'fa-shirt']},
        ],
    },
    {
        title: "Chaussures",
        key: "chaussures",
        icon: ['fas', 'fa-shoe-prints'],
        sous_menu: [
            {title: "Femme", key: "femme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Homme", key: "homme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Enfants", key: "enfant", route: route('femme'), icon: ['fas', 'fa-shirt']},
        ],
    },
    {
        title: "Sacs",
        key: "sacs",
        icon: ['fas', 'fa-shopping-bag'],
        sous_menu: [
            {title: "Femme", key: "femme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Homme", key: "homme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Enfants", key: "enfant", route: route('femme'), icon: ['fas', 'fa-shirt']},
        ],
    },
    {
        title: "Accessoires",
        key: "accessoires",
        icon: ['fas', 'fa-gem'],
        sous_menu: [
            {title: "Femme", key: "femme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Homme", key: "homme", route: route('femme'), icon: ['fas', 'fa-shirt']},
            {title: "Enfants", key: "enfant", route: route('femme'), icon: ['fas', 'fa-shirt']},
        ],
    },
    {
        title: "À propos de nous",
        key: "a-propos",
        icon: ['fas', 'fa-info-circle'],
        route: route('about'),
    },
    {
        title: "FAQ",
        key: "faq",
        icon: ['fas', 'fa-question-circle'],
        route: route('faq'),
    },
    {
        title: "Contact Us",
        key: "contact",
        icon: ['fas', 'fa-envelope'],
        route: route('contact'),
    },
];
