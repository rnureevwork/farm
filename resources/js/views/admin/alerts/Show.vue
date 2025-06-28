<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Детали уведомления</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card v-if="alert">
                        <v-card-title>
                            <v-chip :color="getStatusColor(alert.status)" class="mr-2">
                                {{ getStatusText(alert.status) }}
                            </v-chip>
                            <v-chip :color="getTypeColor(alert.type)">
                                {{ getTypeText(alert.type) }}
                            </v-chip>
                        </v-card-title>
                        <v-card-text>
                            <div class="mb-2">
                                <strong>Сообщение:</strong> {{ alert.message || '—' }}
                            </div>
                            <div class="mb-2">
                                <strong>Дата:</strong> {{ formatDate(alert.created_at) }}
                            </div>
                            <div class="mb-2">
                                <strong>Ферма:</strong> {{ alert.station?.field?.farm?.name || '—' }}
                            </div>
                            <div class="mb-2">
                                <strong>Поле:</strong> {{ alert.station?.field?.name || '—' }}
                            </div>
                            <div class="mb-2">
                                <strong>Станция:</strong> {{ alert.station?.serial_number || '—' }}
                            </div>
                            <div class="mb-2">
                                <strong>Данные сенсора:</strong>
                                <div v-if="alert.sensor_data">
                                    <div>Температура: {{ alert.sensor_data.temperature }}°C</div>
                                    <div>Влажность: {{ alert.sensor_data.humidity }}%</div>
                                    <div>Влажность почвы: {{ alert.sensor_data.soil_moisture }}%</div>
                                    <div>Давление: {{ alert.sensor_data.pressure }} hPa</div>
                                    <div>Скорость ветра: {{ alert.sensor_data.wind_speed }} м/с</div>
                                    <div>Осадки: {{ alert.sensor_data.rainfall }} мм</div>
                                </div>
                                <div v-else>—</div>
                            </div>
                            <div class="mb-2">
                                <strong>Примечание:</strong> {{ alert.notes || '—' }}
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="primary" @click="$router.go(-1)">Назад</v-btn>
                        </v-card-actions>
                    </v-card>
                    <v-alert v-else type="error">Уведомление не найдено</v-alert>
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
    name: 'AlertShow',
    setup() {
        const route = useRoute()
        const alert = ref(null)

        const fetchAlert = async () => {
            try {
                const response = await axios.get(`/api/v1/alerts/${route.params.id}`)
                alert.value = response.data
            } catch (error) {
                alert.value = null
                console.error('Ошибка загрузки уведомления:', error)
            }
        }

        const formatDate = (date) => {
            return new Date(date).toLocaleString('ru-RU')
        }

        const getStatusColor = (status) => {
            switch (status) {
                case 'new': return 'error'
                case 'acknowledged': return 'warning'
                case 'resolved': return 'success'
                default: return 'grey'
            }
        }
        const getStatusText = (status) => {
            switch (status) {
                case 'new': return 'Новый'
                case 'acknowledged': return 'Подтвержден'
                case 'resolved': return 'Решен'
                default: return status
            }
        }
        const getTypeColor = (type) => {
            switch (type) {
                case 'movement': return 'orange'
                case 'soil_condition': return 'brown'
                case 'weather': return 'blue'
                default: return 'grey'
            }
        }
        const getTypeText = (type) => {
            switch (type) {
                case 'movement': return 'Перемещение'
                case 'soil_condition': return 'Состояние почвы'
                case 'weather': return 'Погода'
                default: return type
            }
        }

        onMounted(() => {
            fetchAlert()
        })

        return {
            alert,
            formatDate,
            getStatusColor,
            getStatusText,
            getTypeColor,
            getTypeText
        }
    }
}
</script> 