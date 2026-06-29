<script setup>
import SousMenuPrincipale from "@/Pages/Menu/SousMenuPrincipale.vue";
import StatCard from "@/Pages/Dashboard/Partials/StatCard.vue";
import LeaderboardCard from "@/Pages/Dashboard/Partials/LeaderboardCard.vue";

defineProps({

})

</script>

<template>
    <SousMenuPrincipale title="Dashboard-" selectedMenu="dashboard">
        Bonjour {{ $page.props.auth.user.name }} !!

<!--        <div class="mx-auto py-8">-->
<!--            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">-->
<!--                <StatCard-->
<!--                    title="Partie Jouer"-->
<!--                    :value="nbr.partie_jouer?.value.toLocaleString('FR-fr')"-->
<!--                    icon="puzzle-piece"-->
<!--                    :desc="nbr.partie_jouer?.desc"-->
<!--                />-->
<!--                <StatCard-->
<!--                    title="Joueurs Total"-->
<!--                    :value="nbr.joeurs?.value.toLocaleString('FR-fr')"-->
<!--                    icon="users"-->
<!--                    :desc="nbr.joeurs?.desc"-->
<!--                />-->
<!--                <StatCard-->
<!--                    title="Questions Répondues"-->
<!--                    :value="nbr.question?.value.toLocaleString('FR-fr')"-->
<!--                    icon="bullseye"-->
<!--                    :desc="nbr.question?.desc"-->
<!--                />-->
<!--                <StatCard-->
<!--                    title="Score Moyen"-->
<!--                    :value="nbr.score?.value.toLocaleString('FR-fr')"-->
<!--                    icon="trophy"-->
<!--                    :desc="nbr.score?.desc"-->
<!--                />-->
<!--            </div>-->

<!--            &lt;!&ndash; Leaderboards &ndash;&gt;-->
<!--            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">-->
<!--                <LeaderboardCard-->
<!--                    title="Meilleurs Partie"-->
<!--                    :items="top_parties"-->
<!--                    icon="crown"-->
<!--                />-->
<!--                <LeaderboardCard-->
<!--                    title="Emplacement Populaires"-->
<!--                    :items="top_positions"-->
<!--                    icon="trophy"-->
<!--                />-->
<!--            </div>-->
<!--        </div>-->
    </SousMenuPrincipale>
</template>
