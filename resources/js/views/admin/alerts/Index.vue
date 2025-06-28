<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Уведомления</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-data-table
                                :headers="headers"
                                :items="alerts"
                                :loading="loading"
                                :search="search"
                                :options.sync="options"
                                :server-items-length="totalAlerts"
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

                                <template v-slot:item.status="{ item }">
                                    <v-chip :color="getStatusColor(item.status)" small>
                                        {{ getStatusText(item.status) }}
                                    </v-chip>
                                </template>

                                <template v-slot:item.type="{ item }">
                                    <v-chip :color="getTypeColor(item.type)" small>
                                        {{ getTypeText(item.type) }}
                                    </v-chip>
                                </template>

                                <template v-slot:item.created_at="{ item }">
                                    {{ formatDate(item.created_at) }}
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <v-btn icon small @click="viewAlert(item)">
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                    <v-btn icon small @click="updateStatus(item)" color="primary">
                                        <v-icon>mdi-check</v-icon>
                                    </v-btn>
                                    <v-btn icon small @click="deleteAlert(item)" color="error">
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
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
    name: 'AlertsIndex',
    setup() {
        const router = useRouter()
        const alerts = ref([])
        const loading = ref(true)
        const search = ref('')
        const totalAlerts = ref(0)
        const options = ref({
            page: 1,
            itemsPerPage: 20
        })

        const headers = [
            { text: 'Тип', value: 'type' },
            { text: 'Статус', value: 'status' },
            { text: 'Ферма', value: 'station.field.farm.name' },
            { text: 'Поле', value: 'station.field.name' },
            { text: 'Станция', value: 'station.serial_number' },
            { text: 'Сообщение', value: 'message' },
            { text: 'Дата', value: 'created_at' },
            { text: 'Действия', value: 'actions', sortable: false }
        ]

        const fetchAlerts = async () => {
            loading.value = true
            try {
                const params = {
                    page: options.value.page,
                    per_page: options.value.itemsPerPage,
                    search: search.value
                }
                const response = await axios.get('/api/v1/alerts', { params })
                alerts.value = response.data.data
                totalAlerts.value = response.data.total
            } catch (error) {
                console.error('Ошибка загрузки уведомлений:', error)
            } finally {
                loading.value = false
            }
        }

        const viewAlert = (alert) => {
            router.push({ name: 'alerts.show', params: { id: alert.id } })
        }

        const updateStatus = async (alert) => {
            const newStatus = alert.status === 'new' ? 'acknowledged' : 'resolved'
            try {
                await axios.put(`/api/v1/alerts/${alert.id}`, { status: newStatus })
                await fetchAlerts()
            } catch (error) {
                console.error('Ошибка обновления статуса:', error)
            }
        }

        const deleteAlert = async (alert) => {
            if (confirm('Вы уверены, что хотите удалить это уведомление?')) {
                try {
                    await axios.delete(`/api/v1/alerts/${alert.id}`)
                    await fetchAlerts()
                } catch (error) {
                    console.error('Ошибка удаления уведомления:', error)
                }
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

        watch([search, options], () => {
            fetchAlerts()
        }, { deep: true })

        onMounted(() => {
            fetchAlerts()
        })

        return {
            alerts,
            loading,
            search,
            totalAlerts,
            options,
            headers,
            viewAlert,
            updateStatus,
            deleteAlert,
            formatDate,
            getStatusColor,
            getStatusText,
            getTypeColor,
            getTypeText
        }
    }
}
</script> 