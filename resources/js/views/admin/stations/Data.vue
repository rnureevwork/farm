<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Данные станции</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            Последние показания сенсоров
                        </v-card-title>
                        <v-card-text>
                            <v-data-table
                                :headers="headers"
                                :items="sensorData"
                                :loading="loading"
                                class="elevation-1"
                            >
                                <template v-slot:item.timestamp="{ item }">
                                    {{ formatDate(item.timestamp) }}
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
import { useRoute } from 'vue-router'
import axios from 'axios'

export default {
    name: 'StationData',
    setup() {
        const route = useRoute()
        const sensorData = ref([])
        const loading = ref(true)

        const headers = [
            { text: 'Время', value: 'timestamp' },
            { text: 'Температура (°C)', value: 'temperature' },
            { text: 'Влажность (%)', value: 'humidity' },
            { text: 'Влажность почвы (%)', value: 'soil_moisture' },
            { text: 'Давление (hPa)', value: 'pressure' },
            { text: 'Скорость ветра (м/с)', value: 'wind_speed' },
            { text: 'Направление ветра (°)', value: 'wind_direction' },
            { text: 'Осадки (мм)', value: 'rainfall' },
            { text: 'Батарея (%)', value: 'battery' },
            { text: 'Сигнал (%)', value: 'signal' }
        ]

        const fetchData = async () => {
            loading.value = true
            try {
                const response = await axios.get(`/api/v1/stations/${route.params.id}/data`)
                sensorData.value = response.data.data || response.data
            } catch (error) {
                console.error('Ошибка загрузки данных станции:', error)
            } finally {
                loading.value = false
            }
        }

        const formatDate = (date) => {
            return new Date(date).toLocaleString('ru-RU')
        }

        onMounted(() => {
            fetchData()
        })

        return {
            sensorData,
            loading,
            headers,
            formatDate
        }
    }
}
</script> 