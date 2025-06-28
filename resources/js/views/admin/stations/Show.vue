<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Станция: {{ station.serial_number }}</h1>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>Информация о станции</v-card-title>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>ID</v-list-item-title>
                                    <v-list-item-subtitle>{{ station.id }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Серийный номер</v-list-item-title>
                                    <v-list-item-subtitle>{{ station.serial_number }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Поле</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-btn text color="primary" @click="viewField(station.field)" v-if="station.field">
                                            {{ station.field.name }}
                                        </v-btn>
                                        <span v-else>Не указано</span>
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Ферма</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-btn text color="primary" @click="viewFarm(station.field.farm)" v-if="station.field && station.field.farm">
                                            {{ station.field.farm.name }}
                                        </v-btn>
                                        <span v-else>Не указано</span>
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Статус</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="station.is_active ? 'success' : 'error'" small>
                                            {{ station.is_active ? 'Активна' : 'Неактивна' }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Последний сигнал</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatDateTime(station.last_seen) }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Батарея</v-list-item-title>
                                    <v-list-item-subtitle>{{ station.battery_level }}%</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Сигнал</v-list-item-title>
                                    <v-list-item-subtitle>{{ station.signal_strength }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'StationShow',
    data() {
        return {
            station: {}
        }
    },
    async mounted() {
        await this.fetchStation()
    },
    methods: {
        async fetchStation() {
            try {
                const response = await axios.get(`/api/v1/stations/${this.$route.params.id}`)
                this.station = response.data
            } catch (error) {
                console.error('Ошибка загрузки станции:', error)
                alert('Ошибка при загрузке станции')
                this.$router.push({ name: 'stations.index' })
            }
        },
        viewField(field) {
            this.$router.push({ name: 'fields.show', params: { id: field.id } })
        },
        viewFarm(farm) {
            this.$router.push({ name: 'farms.show', params: { id: farm.id } })
        },
        formatDateTime(dateString) {
            if (!dateString) return 'Не указано'
            return new Date(dateString).toLocaleString('ru-RU')
        }
    }
}
</script> 