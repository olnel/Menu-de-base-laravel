<x-mail::message>
# Bienvenue sur TMS DNA, {{ $prenom }} !

Votre espace d'essai **gratuit de 15 jours** pour **{{ $societe }}** est prêt.
Vous avez accès à **toutes les fonctionnalités** de la plateforme sans restriction.

---

## Vos informations de connexion

| | |
|---|---|
| **Adresse de votre espace** | [{{ $domaine }}]({{ $domaineUrl }}) |
| **Identifiant (email)** | {{ $login }} |
| **Mot de passe** | `password` |

<x-mail::button :url="$domaineUrl">
Accéder à mon espace TMS DNA
</x-mail::button>

---

## Votre période d'essai

Votre accès gratuit est valable jusqu'au **{{ $trialEndDate }}**.
À l'issue de cette période, vous pourrez choisir un abonnement directement depuis votre espace.

> **Conseil :** Pensez à changer votre mot de passe dès votre première connexion.

---

Besoin d'aide ? Contactez-nous à [contact@dna.mg](mailto:contact@dna.mg) ou WhatsApp au +261 34 58 85 286.

Cordialement,
**L'équipe DNA Webhosting**
</x-mail::message>
