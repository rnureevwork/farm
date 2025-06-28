<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12" class="d-flex justify-space-between align-center">
                    <h1 class="text-h4">Ферма: {{ farm.name }}</h1>
                    <div>
                        <v-btn
                            color="primary"
                            @click="editFarm"
                            class="mr-2"
                        >
                            Редактировать
                        </v-btn>
                        <v-btn
                            color="error"
                            @click="deleteFarm"
                        >
                            Удалить
                        </v-btn>
                    </div>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>Информация о ферме</v-card-title>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>ID</v-list-item-title>
                                    <v-list-item-subtitle>{{ farm.id }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Название</v-list-item-title>
                                    <v-list-item-subtitle>{{ farm.name }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Контактный телефон</v-list-item-title>
                                    <v-list-item-subtitle>{{ farm.contact_phone || 'Не указан' }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Количество полей</v-list-item-title>
                                    <v-list-item-subtitle>{{ farm.fields ? farm.fields.length : 0 }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Дата создания</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatDate(farm.created_at) }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            Поля фермы
                            <v-btn
                                color="primary"
                                small
                                @click="createField"
                            >
                                Добавить поле
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <div v-if="farm.fields && farm.fields.length > 0">
                                <v-expansion-panels>
                                    <v-expansion-panel
                                        v-for="field in farm.fields"
                                        :key="field.id"
                                    >
                                        <v-expansion-panel-header>
                                            <div class="d-flex justify-space-between align-center w-100">
                                                <div>
                                                    <strong>{{ field.name }}</strong>
                                                    <span class="text-grey ml-2">({{ field.area_hectares }} га)</span>
                                                </div>
                                                <div class="d-flex align-center">
                                                    <v-chip
                                                        v-if="field.current_crop && field.current_crop.crop"
                                                        color="success"
                                                        small
                                                        class="mr-2"
                                                    >
                                                        {{ field.current_crop.crop.name }}
                                                    </v-chip>
                                                    <v-btn
                                                        icon
                                                        small
                                                        color="primary"
                                                        @click.stop="viewField(field)"
                                                    >
                                                        <v-icon>mdi-eye</v-icon>
                                                    </v-btn>
                                                </div>
                                            </div>
                                        </v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <div class="pa-2">
                                                <h6 class="text-subtitle-2 mb-2">Метеостанции на поле:</h6>
                                                <div v-if="field.stations && field.stations.length > 0">
                                                    <v-list dense>
                                                        <v-list-item
                                                            v-for="station in field.stations"
                                                            :key="station.id"
                                                            @click="viewStation(station)"
                                                            class="cursor-pointer"
                                                        >
                                                            <v-list-item-icon>
                                                                <v-icon
                                                                    :color="station.is_active ? 'success' : 'error'"
                                                                    small
                                                                >
                                                                    mdi-thermometer
                                                                </v-icon>
                                                            </v-list-item-icon>
                                                            <v-list-item-content>
                                                                <v-list-item-title>{{ station.serial_number }}</v-list-item-title>
                                                                <v-list-item-subtitle>
                                                                    Батарея: {{ station.battery_level }}% | 
                                                                    Сигнал: {{ station.signal_strength }}% |
                                                                    {{ formatDateTime(station.last_seen) }}
                                                                </v-list-item-subtitle>
                                                            </v-list-item-content>
                                                            <v-list-item-action>
                                                                <v-btn
                                                                    icon
                                                                    small
                                                                    color="secondary"
                                                                    @click.stop="viewStationData(station)"
                                                >
                                                    <v-icon>mdi-chart-line</v-icon>
                                                </v-btn>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </div>
                                <div v-else class="text-center pa-4">
                                    <v-icon size="32" color="grey">mdi-thermometer-off</v-icon>
                                    <p class="text-grey mt-2">На поле нет метеостанций</p>
                                    <v-btn
                                        color="primary"
                                        small
                                        @click="addStationToField(field)"
                                    >
                                        Добавить станцию
                                    </v-btn>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <div v-else class="text-center pa-4">
                <v-icon size="48" color="grey">mdi-map-marker-off</v-icon>
                <p class="text-grey mt-2">У этой фермы пока нет полей</p>
            </div>
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
    name: 'FarmsShow',
    data() {
        return {
            farm: {}
        }
    },
    async mounted() {
        await this.fetchFarm()
    },
    methods: {
        async fetchFarm() {
            try {
                const response = await axios.get(`/api/v1/farms/${this.$route.params.id}`)
                this.farm = response.data
            } catch (error) {
                console.error('Ошибка загрузки фермы:', error)
                alert('Ошибка при загрузке фермы')
                this.$router.push({ name: 'farms.index' })
            }
        },
        editFarm() {
            this.$router.push({ name: 'farms.edit', params: { id: this.farm.id } })
        },
        async deleteFarm() {
            if (confirm(`Вы уверены, что хотите удалить ферму "${this.farm.name}"?`)) {
                try {
                    await axios.delete(`/api/v1/farms/${this.farm.id}`)
                    this.$router.push({ name: 'farms.index' })
                } catch (error) {
                    console.error('Ошибка удаления фермы:', error)
                    alert('Ошибка при удалении фермы')
                }
            }
        },
        viewField(field) {
            this.$router.push({ name: 'fields.show', params: { id: field.id } })
        },
        createField() {
            this.$router.push({ 
                name: 'fields.create',
                query: { farm_id: this.farm.id }
            })
        },
        viewStation(station) {
            this.$router.push({ name: 'stations.show', params: { id: station.id } })
        },
        viewStationData(station) {
            this.$router.push({ name: 'stations.data', params: { id: station.id } })
        },
        addStationToField(field) {
            this.$router.push({ 
                name: 'stations.create',
                query: { field_id: field.id }
            })
        },
        formatDate(dateString) {
            if (!dateString) return 'Не указана'
            return new Date(dateString).toLocaleDateString('ru-RU')
        },
        formatDateTime(dateString) {
            if (!dateString) return 'Не указана'
            return new Date(dateString).toLocaleString('ru-RU')
        }
    }
}
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style> 