<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12" class="d-flex justify-space-between align-center">
                    <h1 class="text-h4">Метеостанции</h1>
                    <v-btn color="primary" @click="$router.push({ name: 'stations.create' })">
                        <v-icon left>mdi-plus</v-icon>
                        Добавить станцию
                    </v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-data-table
                                :headers="headers"
                                :items="stations"
                                :loading="loading"
                                :search="search"
                                class="elevation-1"
                            >
                                <template v-slot:top>
                                    <v-text-field
                                        v-model="search"
                                        label="Поиск"
                                        prepend-inner-icon="mdi-magnify"
                                        clearable
                                    ></v-text-field>
                                </template>

                                <template v-slot:item.is_active="{ item }">
                                    <v-chip :color="item.is_active ? 'success' : 'error'" small>
                                        {{ item.is_active ? 'Активна' : 'Неактивна' }}
                                    </v-chip>
                                </template>

                                <template v-slot:item.battery_level="{ item }">
                                    <v-progress-linear
                                        :value="item.battery_level"
                                        :color="getBatteryColor(item.battery_level)"
                                        height="20"
                                    >
                                        <template v-slot:default>
                                            {{ item.battery_level }}%
                                        </template>
                                    </v-progress-linear>
                                </template>

                                <template v-slot:item.signal_strength="{ item }">
                                    <v-progress-linear
                                        :value="item.signal_strength"
                                        :color="getSignalColor(item.signal_strength)"
                                        height="20"
                                    >
                                        <template v-slot:default>
                                            {{ item.signal_strength }}%
                                        </template>
                                    </v-progress-linear>
                                </template>

                                <template v-slot:item.last_seen="{ item }">
                                    {{ formatDate(item.last_seen) }}
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <v-btn icon small @click="viewData(item)">
                                        <v-icon>mdi-chart-line</v-icon>
                                    </v-btn>
                                    <v-btn icon small @click="editStation(item)">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn icon small @click="deleteStation(item)" color="error">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
    name: 'StationsIndex',
    setup() {
        const router = useRouter()
        const stations = ref([])
        const loading = ref(true)
        const search = ref('')

        const headers = [
            { text: 'Серийный номер', value: 'serial_number' },
            { text: 'Ферма', value: 'field.farm.name' },
            { text: 'Поле', value: 'field.name' },
            { text: 'Статус', value: 'is_active' },
            { text: 'Батарея', value: 'battery_level' },
            { text: 'Сигнал', value: 'signal_strength' },
            { text: 'Последний сигнал', value: 'last_seen' },
            { text: 'Действия', value: 'actions', sortable: false }
        ]

        const fetchStations = async () => {
            try {
                const response = await axios.get('/api/v1/stations')
                stations.value = response.data
            } catch (error) {
                console.error('Ошибка загрузки станций:', error)
            } finally {
                loading.value = false
            }
        }

        const viewData = (station) => {
            router.push({ name: 'stations.data', params: { id: station.id } })
        }

        const editStation = (station) => {
            router.push({ name: 'stations.edit', params: { id: station.id } })
        }

        const deleteStation = async (station) => {
            if (confirm('Вы уверены, что хотите удалить эту станцию?')) {
                try {
                    await axios.delete(`/api/v1/stations/${station.id}`)
                    await fetchStations()
                } catch (error) {
                    console.error('Ошибка удаления станции:', error)
                }
            }
        }

        const formatDate = (date) => {
            if (!date) return 'Нет данных'
            return new Date(date).toLocaleString('ru-RU')
        }

        const getBatteryColor = (level) => {
            if (level > 50) return 'success'
            if (level > 20) return 'warning'
            return 'error'
        }

        const getSignalColor = (level) => {
            if (level > 70) return 'success'
            if (level > 30) return 'warning'
            return 'error'
        }

        onMounted(() => {
            fetchStations()
        })

        return {
            stations,
            loading,
            search,
            headers,
            viewData,
            editStation,
            deleteStation,
            formatDate,
            getBatteryColor,
            getSignalColor
        }
    }
}
</script> 