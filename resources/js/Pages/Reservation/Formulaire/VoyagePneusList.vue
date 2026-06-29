<template>
    <div class="p-4 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <font-awesome-icon icon="fa-circle-dot" class="mr-2 text-primary" />
                Liste des pneus du voyage
            </h3>
            <a-tag color="blue">Total pneus: {{ pneus.length }}</a-tag>
        </div>

        <a-table 
            :dataSource="pneus" 
            :columns="columns" 
            :pagination="false" 
            bordered 
            size="middle"
            class="pneu-voyage-table"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'numero_serie'">
                    <span class="font-mono font-bold">{{ record.numero_serie }}</span>
                </template>

                <template v-if="column.key === 'provenance'">
                    <a-tag :color="record.provenance === 'Véhicule' ? 'blue' : 'green'" class="!rounded-full px-3">
                        <font-awesome-icon :icon="record.provenance === 'Véhicule' ? 'fa-truck' : 'fa-trailer'" class="mr-1" />
                        {{ record.provenance }}
                    </a-tag>
                </template>

                <template v-if="column.key === 'etat'">
                    <a-tag :color="getEtatColor(record.etat)" :bordered="false">
                        {{ record.etat?.toUpperCase() || 'INCONNU' }}
                    </a-tag>
                </template>

                <template v-if="column.key === 'is_secours'">
                    <div class="flex items-center gap-2">
                        <a-switch 
                            v-model:checked="record.is_secours" 
                            @change="(val) => updateSecours(record, val)"
                            checked-children="Oui"
                            un-checked-children="Non"
                        />
                        <span v-if="record.is_secours" class="text-xs text-orange-500 italic">(Ne compte pas les voyages)</span>
                    </div>
                </template>

                <template v-if="column.key === 'voyage_count'">
                    <div class="flex flex-col items-center">
                        <span class="text-lg font-bold" :class="record.is_secours ? 'text-gray-400 line-through' : 'text-primary'">
                            {{ record.voyage_count }}
                        </span>
                        <span class="text-[10px] text-gray-500 uppercase">Voyages finis</span>
                    </div>
                </template>
            </template>
        </a-table>

        <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-md text-sm text-blue-700">
            <font-awesome-icon icon="fa-info-circle" class="mr-2" />
            Les pneus affichés ici sont ceux actuellement montés sur le véhicule 
            <strong>{{ voyage.matricule_vehicule }}</strong> et la remorque <strong>{{ voyage.numero_remorque }}</strong>. 
            Le nombre de voyages est automatiquement incrémenté à chaque voyage validé.
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { message } from 'ant-design-vue';

const props = defineProps({
    voyage: { type: Object, required: true }
});

const pneus = computed(() => {
    return (props.voyage.pneus || []).map(pneu => {
        // Déterminer la provenance et la position si possible (basé sur le véhicule/remorque actuel)
        let provenance = 'Inconnu';
        let position = 'Non spécifié';

        if (props.voyage.vehicule?.element_vehicules) {
            const el = props.voyage.vehicule.element_vehicules.find(e => e.numero_serie === pneu.numero_serie);
            if (el) {
                provenance = 'Véhicule';
                position = el.libelle;
            }
        }

        if (provenance === 'Inconnu' && props.voyage.remorque?.element_remorques) {
            const el = props.voyage.remorque.element_remorques.find(e => e.numero_serie === pneu.numero_serie);
            if (el) {
                provenance = 'Remorque';
                position = el.libelle;
            }
        }

        return {
            ...pneu,
            is_secours: !!pneu.pivot?.is_secours,
            provenance,
            position
        };
    });
});

const columns = [
    { title: 'N° Série', dataIndex: 'numero_serie', key: 'numero_serie', width: '150px' },
    { title: 'Position', dataIndex: 'position', key: 'position' },
    { title: 'Provenance', key: 'provenance', align: 'center' },
    { title: 'État', key: 'etat', align: 'center' },
    { title: 'Pneu de secours pour ce voyage ?', key: 'is_secours', width: '250px' },
    { title: 'Statistiques', key: 'voyage_count', align: 'center', width: '120px' },
];

const getEtatColor = (etat) => {
    const colors = {
        'neuf': 'green',
        'bon': 'blue',
        'moyen': 'orange',
        'use': 'red',
        'a_remplacer': 'red',
        'rechappe': 'purple'
    };
    return colors[etat?.toLowerCase()] || 'default';
};

const updateSecours = (record, val) => {
    router.put(route('voyages.pneus.secours', record.id), {
        voyage_id: props.voyage.id,
        is_secours: val
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            message.success(`Statut du pneu ${record.numero_serie} mis à jour pour ce voyage.`);
        },
        onError: () => {
            message.error("Erreur lors de la mise à jour du statut.");
            record.is_secours = !val; // Rollback UI
        }
    });
};
</script>

<style scoped>
.pneu-voyage-table :deep(.ant-table-thead > tr > th) {
    @apply bg-gray-50 text-gray-600 font-semibold;
}
</style>
