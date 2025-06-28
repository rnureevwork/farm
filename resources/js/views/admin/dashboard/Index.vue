<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Meteo Dashboard</h1>
                </v-col>
            </v-row>

            <!-- Статистика -->
            <v-row>
                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center">
                        <v-card-text>
                            <v-icon size="48" color="primary" class="mb-2">mdi-thermometer</v-icon>
                            <div class="text-h4 font-weight-bold">{{ stats.total_stations || 0 }}</div>
                            <div class="text-subtitle-2">Всего станций</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center">
                        <v-card-text>
                            <v-icon size="48" color="success" class="mb-2">mdi-check-circle</v-icon>
                            <div class="text-h4 font-weight-bold">{{ stats.active_stations || 0 }}</div>
                            <div class="text-subtitle-2">Активных станций</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center">
                        <v-card-text>
                            <v-icon size="48" color="info" class="mb-2">mdi-grid</v-icon>
                            <div class="text-h4 font-weight-bold">{{ stats.total_fields || 0 }}</div>
                            <div class="text-subtitle-2">Всего полей</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center">
                        <v-card-text>
                            <v-icon size="48" color="warning" class="mb-2">mdi-alert</v-icon>
                            <div class="text-h4 font-weight-bold">{{ stats.new_alerts || 0 }}</div>
                            <div class="text-subtitle-2">Новых уведомлений</div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Последние уведомления и данные -->
            <v-row>
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Последние уведомления</span>
                            <v-btn
                                color="primary"
                                small
                                @click="$router.push({ name: 'alerts.index' })"
                            >
                                Все уведомления
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <v-list v-if="recentAlerts.length > 0" dense>
                                <v-list-item 
                                    v-for="alert in recentAlerts" 
                                    :key="alert.id"
                                    @click="viewAlert(alert)"
                                    class="cursor-pointer"
                                >
                                    <v-list-item-icon>
                                        <v-icon :color="getAlertColor(alert.status)">
                                            mdi-alert-circle
                                        </v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title class="text-subtitle-2">
                                            {{ alert.type }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ alert.station?.field?.farm?.name }} - {{ alert.station?.field?.name }}
                                        </v-list-item-subtitle>
                                        <v-list-item-subtitle class="text-caption">
                                            {{ formatDate(alert.created_at) }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-chip :color="getAlertColor(alert.status)" small>
                                            {{ getAlertStatusText(alert.status) }}
                                        </v-chip>
                                    </v-list-item-action>
                                </v-list-item>
                            </v-list>
                            <div v-else class="text-center pa-4">
                                <v-icon size="48" color="grey">mdi-alert-circle-outline</v-icon>
                                <div class="text-subtitle-1 mt-2">Нет уведомлений</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Последние данные -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Последние данные</span>
                            <v-btn
                                color="primary"
                                small
                                @click="$router.push({ name: 'stations.index' })"
                            >
                                Все станции
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <v-list v-if="latestData.length > 0" dense>
                                <v-list-item 
                                    v-for="data in latestData" 
                                    :key="data.id"
                                    @click="viewStationData(data.station)"
                                    class="cursor-pointer"
                                >
                                    <v-list-item-icon>
                                        <v-icon color="info">mdi-thermometer</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title class="text-subtitle-2">
                                            {{ data.station?.field?.farm?.name }} - {{ data.station?.field?.name }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            <v-chip x-small color="error" class="mr-1">
                                                {{ data.temperature }}°C
                                            </v-chip>
                                            <v-chip x-small color="info" class="mr-1">
                                                {{ data.humidity }}%
                                            </v-chip>
                                            <v-chip x-small color="brown">
                                                {{ data.soil_moisture }}%
                                            </v-chip>
                                        </v-list-item-subtitle>
                                        <v-list-item-subtitle class="text-caption">
                                            {{ formatDate(data.timestamp) }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                            <div v-else class="text-center pa-4">
                                <v-icon size="48" color="grey">mdi-database-outline</v-icon>
                                <div class="text-subtitle-1 mt-2">Нет данных</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Быстрые действия -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>Быстрые действия</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" sm="6" md="3">
                                    <v-btn
                                        block
                                        color="primary"
                                        @click="$router.push({ name: 'stations.create' })"
                                        class="mb-2"
                                    >
                                        <v-icon left>mdi-plus</v-icon>
                                        Добавить станцию
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <v-btn
                                        block
                                        color="success"
                                        @click="$router.push({ name: 'fields.create' })"
                                        class="mb-2"
                                    >
                                        <v-icon left>mdi-plus</v-icon>
                                        Добавить поле
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <v-btn
                                        block
                                        color="info"
                                        @click="$router.push({ name: 'farms.create' })"
                                        class="mb-2"
                                    >
                                        <v-icon left>mdi-plus</v-icon>
                                        Добавить ферму
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <v-btn
                                        block
                                        color="warning"
                                        @click="$router.push({ name: 'alerts.index' })"
                                        class="mb-2"
                                    >
                                        <v-icon left>mdi-alert</v-icon>
                                        Просмотр уведомлений
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
    name: 'Dashboard',
    setup() {
        const stats = ref({})
        const recentAlerts = ref([])
        const latestData = ref([])
        const loading = ref(true)

        const fetchDashboardData = async () => {
            try {
                const response = await axios.get('/api/v1/dashboard')
                stats.value = response.data.stats
                recentAlerts.value = response.data.recent_alerts
                latestData.value = response.data.latest_data
            } catch (error) {
                console.error('Ошибка загрузки дашборда:', error)
            } finally {
                loading.value = false
            }
        }

        const formatDate = (date) => {
            return new Date(date).toLocaleString('ru-RU')
        }

        const getAlertColor = (status) => {
            switch (status) {
                case 'new': return 'error'
                case 'acknowledged': return 'warning'
                case 'resolved': return 'success'
                default: return 'grey'
            }
        }

        const getAlertStatusText = (status) => {
            switch (status) {
                case 'new': return 'Новый'
                case 'acknowledged': return 'Подтвержден'
                case 'resolved': return 'Решен'
                default: return status
            }
        }

        const viewAlert = (alert) => {
            // TODO: Реализовать переход к деталям уведомления
            console.log('View alert:', alert)
        }

        const viewStationData = (station) => {
            if (station) {
                // TODO: Реализовать переход к данным станции
                console.log('View station data:', station)
            }
        }

        onMounted(() => {
            fetchDashboardData()
        })

        return {
            stats,
            recentAlerts,
            latestData,
            loading,
            formatDate,
            getAlertColor,
            getAlertStatusText,
            viewAlert,
            viewStationData
        }
    }
}
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style> 