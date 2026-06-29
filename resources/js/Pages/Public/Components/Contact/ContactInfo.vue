<script setup>
</script>

<template>
    <div class="bg-gradient-to-br rounded-2xl from-green-700 to-green-800 p-8 h-full text-white">
        <h2 class="text-4xl font-bold mb-6">
            Nos Coordonnées
        </h2>
        <p class="mb-8 text-lg text-gray-100">
            Besoin de nous joindre ? Voici nos informations de contact.
            N'hésitez pas à nous appeler, nous envoyer un
            e-mail ou à nous rendre visite pendant nos horaires d'ouverture.
        </p>

        <div class="space-y-6">
            <!-- Phone -->
            <div class="flex items-center group">
                <div
                    class="mr-4 p-3 w-12 h-12 flex items-center text-[#D9DF26] justify-center rounded-full transition-all duration-300 group-hover:text-white">
                    <font-awesome-icon icon="phone" class="text-3xl" />
                </div>
                <div>
                    <p class="text-base text-gray-200">TÉLÉPHONE</p>
                    <p class="text-3xl font-semibold">(+261) 32 00 000 00</p>
                </div>
            </div>

            <!-- Email -->
            <div class="flex items-center group">
                <div
                    class="mr-4 p-3 w-12 h-12 flex items-center text-[#D9DF26] justify-center rounded-full transition-all duration-300 group-hover:text-white">
                    <font-awesome-icon icon="envelope" class="text-3xl" />
                </div>
                <div>
                    <p class="text-sm text-gray-200">EMAIL</p>
                    <p class="text-lg font-semibold">contact@votreentreprise.com</p>
                </div>
            </div>

            <!-- Address -->
            <div class="flex items-start group">
                <div
                    class="mr-4 p-3 w-12 h-12 flex items-center text-[#D9DF26] justify-center rounded-full transition-all duration-300 group-hover:text-white mt-1">
                    <font-awesome-icon icon="map-marker-alt" class="text-4xl" />
                </div>
                <div>
                    <p class="text-sm text-gray-200">NOS AGENCES</p>
                    <p class="text-lg font-semibold">Basés à Toamasina, Moramanga,</p>
                    <p>Tuléar et Maroantsetra</p>
                </div>
            </div>

            <!-- Opening Hours -->
            <div class="flex items-start group">
                <div
                    class="mr-4 p-3 w-12 h-12 flex items-center text-[#D9DF26] justify-center rounded-full transition-all duration-300 group-hover:text-white mt-1">
                    <font-awesome-icon icon="clock" class="text-3xl" />
                </div>
                <div>
                    <p class="text-sm text-gray-200">HORAIRES D'OUVERTURE</p>

                    <p class="text-lg flex items-center gap-2">
                        <font-awesome-icon icon="chevron-right" class="text-green-400 text-sm" />
                        Lundi - Vendredi : <span class="font-semibold ml-1">9h00 - 19h00</span>
                    </p>

                    <p class="text-lg flex items-center gap-2">
                        <font-awesome-icon icon="chevron-right" class="text-green-400 text-sm" />
                        Samedi : <span class="font-semibold ml-1">10h00 - 14h00</span>
                    </p>

                    <p class="text-lg flex items-center gap-2">
                        <font-awesome-icon icon="chevron-right" class="text-green-400 text-sm" />
                        Dimanche & Jours fériés : <span class="font-semibold ml-1">Fermé</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
