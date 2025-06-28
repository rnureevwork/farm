<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12" class="d-flex justify-space-between align-center">
                    <h1 class="text-h4">Поле: {{ field.name }}</h1>
                    <div>
                        <v-btn
                            color="primary"
                            @click="editField"
                            class="mr-2"
                        >
                            Редактировать
                        </v-btn>
                        <v-btn
                            color="error"
                            @click="deleteField"
                        >
                            Удалить
                        </v-btn>
                    </div>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>Информация о поле</v-card-title>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>ID</v-list-item-title>
                                    <v-list-item-subtitle>{{ field.id }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Название</v-list-item-title>
                                    <v-list-item-subtitle>{{ field.name }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Ферма</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="viewFarm(field.farm)"
                                            v-if="field.farm"
                                        >
                                            {{ field.farm.name }}
                                        </v-btn>
                                        <span v-else>Не указана</span>
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Площадь</v-list-item-title>
                                    <v-list-item-subtitle>{{ field.area_hectares }} га</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Текущая культура</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <span v-if="field.current_crop && field.current_crop.crop">
                                            {{ field.current_crop.crop.name }}
                                            ({{ formatDate(field.current_crop.planting_date) }} - {{ formatDate(field.current_crop.harvest_date) }})
                                        </span>
                                        <span v-else class="text-grey">Не указана</span>
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Дата создания</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatDate(field.created_at) }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>Границы поля</v-card-title>
                        <v-card-text>
                            <div v-if="field.boundary && field.boundary.length > 0">
                                <v-list>
                                    <v-list-item
                                        v-for="(coord, index) in field.boundary"
                                        :key="index"
                                    >
                                        <v-list-item-title>Точка {{ index + 1 }}</v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ coord[0] }}, {{ coord[1] }}
                                        </v-list-item-subtitle>
                                    </v-list-item>
                                </v-list>
                            </div>
                            <div v-else class="text-center pa-4">
                                <v-icon size="48" color="grey">mdi-map-marker-off</v-icon>
                                <p class="text-grey mt-2">Границы поля не указаны</p>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- История севооборота -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            История севооборота
                            <v-btn
                                color="primary"
                                small
                                @click="showCropRotationDialog = true"
                            >
                                Добавить культуру
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <v-data-table
                                :headers="cropRotationHeaders"
                                :items="field.crop_rotations || []"
                                :loading="loading"
                                class="elevation-1"
                            >
                                <template v-slot:item.crop.name="{ item }">
                                    {{ item.crop ? item.crop.name : 'Не указана' }}
                                </template>
                                <template v-slot:item.planting_date="{ item }">
                                    {{ formatDate(item.planting_date) }}
                                </template>
                                <template v-slot:item.harvest_date="{ item }">
                                    {{ item.harvest_date ? formatDate(item.harvest_date) : '—' }}
                                </template>
                                <template v-slot:item.status="{ item }">
                                    <v-chip
                                        :color="getCropStatusColor(item)"
                                        small
                                    >
                                        {{ getCropStatusText(item) }}
                                    </v-chip>
                                </template>
                                <template v-slot:item.actions="{ item }">
                                    <v-btn
                                        icon
                                        small
                                        color="primary"
                                        @click="editCropRotation(item)"
                                    >
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        small
                                        color="error"
                                        @click="deleteCropRotation(item)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Метеостанции -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            Метеостанции на поле
                            <v-btn
                                color="primary"
                                small
                                @click="addStation"
                            >
                                Добавить станцию
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <v-data-table
                                :headers="stationHeaders"
                                :items="field.stations || []"
                                :loading="loading"
                                class="elevation-1"
                            >
                                <template v-slot:item.is_active="{ item }">
                                    <v-chip
                                        :color="item.is_active ? 'success' : 'error'"
                                        small
                                    >
                                        {{ item.is_active ? 'Активна' : 'Неактивна' }}
                                    </v-chip>
                                </template>
                                <template v-slot:item.last_seen="{ item }">
                                    {{ formatDateTime(item.last_seen) }}
                                </template>
                                <template v-slot:item.actions="{ item }">
                                    <v-btn
                                        icon
                                        small
                                        color="primary"
                                        @click="viewStation(item)"
                                    >
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        small
                                        color="secondary"
                                        @click="viewStationData(item)"
                                    >
                                        <v-icon>mdi-chart-line</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        small
                                        color="warning"
                                        @click="moveStation(item)"
                                    >
                                        <v-icon>mdi-arrow-right</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Диалог добавления/редактирования севооборота -->
        <v-dialog v-model="showCropRotationDialog" max-width="500px">
            <v-card>
                <v-card-title>
                    {{ editingCropRotation ? 'Редактировать севооборот' : 'Добавить культуру' }}
                </v-card-title>
                
                <v-card-text>
                    <v-form ref="cropRotationForm" v-model="cropRotationFormValid">
                        <v-select
                            v-model="cropRotationForm.crop_id"
                            :items="crops"
                            item-title="name"
                            item-value="id"
                            label="Культура"
                            :rules="[v => !!v || 'Выберите культуру']"
                            required
                        ></v-select>
                        
                        <v-text-field
                            v-model="cropRotationForm.planting_date"
                            label="Дата посадки"
                            type="date"
                            :rules="[v => !!v || 'Укажите дату посадки']"
                            required
                        ></v-text-field>
                        
                        <v-text-field
                            v-model="cropRotationForm.harvest_date"
                            label="Дата сбора урожая"
                            type="date"
                            :min="cropRotationForm.planting_date"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey darken-1"
                        text
                        @click="closeCropRotationDialog"
                    >
                        Отмена
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="saveCropRotation"
                        :loading="savingCropRotation"
                        :disabled="!cropRotationFormValid"
                    >
                        {{ editingCropRotation ? 'Обновить' : 'Добавить' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Диалог переноса станции -->
        <v-dialog v-model="showMoveStationDialog" max-width="500px">
            <v-card>
                <v-card-title>
                    Перенести станцию {{ selectedStation?.serial_number }}
                </v-card-title>
                
                <v-card-text>
                    <div v-if="availableFields.length === 0" class="text-center pa-4">
                        <v-icon size="48" color="grey">mdi-map-marker-off</v-icon>
                        <div class="text-subtitle-1 mt-2">Нет доступных полей для переноса</div>
                        <div class="text-caption text-grey">Создайте новое поле, чтобы перенести станцию</div>
                        <v-btn
                            color="primary"
                            class="mt-4"
                            @click="createNewField"
                        >
                            Создать новое поле
                        </v-btn>
                    </div>
                    <div v-else>
                        <v-form ref="moveStationForm" v-model="moveStationFormValid">
                            <label class="text-subtitle-2 mb-2 d-block">Выберите новое поле для станции:</label>
                            <select 
                                v-model="moveStationForm.field_id" 
                                class="form-control"
                                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;"
                                required
                            >
                                <option value="">Выберите поле</option>
                                <option 
                                    v-for="field in availableFields" 
                                    :key="field.id" 
                                    :value="field.id"
                                >
                                    {{ field.name }} (ID: {{ field.id }})
                                </option>
                            </select>
                        </v-form>
                    </div>
                </v-card-text>
                
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey darken-1"
                        text
                        @click="closeMoveStationDialog"
                    >
                        Отмена
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="confirmMoveStation"
                        :loading="movingStation"
                        :disabled="!moveStationFormValid || availableFields.length === 0"
                    >
                        Перенести
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'FieldsShow',
    data() {
        return {
            field: {},
            loading: false,
            crops: [],
            allFields: [], // Все поля для переноса
            showCropRotationDialog: false,
            editingCropRotation: null,
            cropRotationFormValid: false,
            savingCropRotation: false,
            cropRotationForm: {
                crop_id: '',
                planting_date: '',
                harvest_date: ''
            },
            // Для переноса станции
            showMoveStationDialog: false,
            selectedStation: null,
            availableFields: [],
            moveStationFormValid: false,
            movingStation: false,
            moveStationForm: {
                field_id: ''
            },
            cropRotationHeaders: [
                { text: 'Культура', value: 'crop.name' },
                { text: 'Дата посадки', value: 'planting_date' },
                { text: 'Дата сбора', value: 'harvest_date' },
                { text: 'Статус', value: 'status', sortable: false },
                { text: 'Действия', value: 'actions', sortable: false }
            ],
            stationHeaders: [
                { text: 'Серийный номер', value: 'serial_number' },
                { text: 'Статус', value: 'is_active' },
                { text: 'Батарея', value: 'battery_level' },
                { text: 'Сигнал', value: 'signal_strength' },
                { text: 'Последний сигнал', value: 'last_seen' },
                { text: 'Действия', value: 'actions', sortable: false }
            ]
        }
    },
    async mounted() {
        console.log('=== КОМПОНЕНТ ЗАГРУЖЕН ===')
        console.log('Route params:', this.$route.params)
        
        await this.fetchField()
        await this.fetchCrops()
        // Загружаем все поля заранее
        await this.fetchAllFields()
        
        console.log('=== ЗАГРУЗКА ЗАВЕРШЕНА ===')
    },
    methods: {
        async fetchField() {
            console.log('=== ВЫЗВАН fetchField ===')
            this.loading = true
            try {
                console.log('Загружаем основное поле...')
                const response = await axios.get(`/api/v1/fields/${this.$route.params.id}`)
                console.log('Ответ API основного поля:', response)
                this.field = response.data
                console.log('Основное поле загружено:', this.field)
            } catch (error) {
                console.error('Ошибка загрузки поля:', error)
                console.error('Детали ошибки:', error.response?.data)
                console.error('Статус ошибки:', error.response?.status)
                alert('Ошибка при загрузке поля')
                this.$router.push({ name: 'fields.index' })
            } finally {
                this.loading = false
            }
        },
        async fetchCrops() {
            console.log('=== ВЫЗВАН fetchCrops ===')
            try {
                const response = await axios.get('/api/v1/crops')
                this.crops = response.data
                console.log('Культуры загружены:', this.crops)
            } catch (error) {
                console.error('Ошибка загрузки культур:', error)
            }
        },
        async fetchAllFields() {
            console.log('=== ВЫЗВАН fetchAllFields ===')
            try {
                console.log('Начинаем загрузку всех полей через фермы...')
                
                // Загружаем поля через фермы
                const farmsResponse = await axios.get('/api/v1/farms')
                console.log('Ответ API ферм:', farmsResponse)
                
                if (farmsResponse.data && farmsResponse.data.length > 0) {
                    // Собираем все поля из всех ферм
                    const allFields = []
                    for (const farm of farmsResponse.data) {
                        if (farm.fields && farm.fields.length > 0) {
                            // Добавляем информацию о ферме к каждому полю
                            const fieldsWithFarm = farm.fields.map(field => ({
                                ...field,
                                farm: { name: farm.name }
                            }))
                            allFields.push(...fieldsWithFarm)
                        }
                    }
                    console.log('Все поля из ферм:', allFields)
                    this.allFields = allFields
                } else {
                    console.warn('Нет ферм или полей в системе')
                    this.allFields = []
                }
                
            } catch (error) {
                console.error('Ошибка загрузки всех полей:', error)
                console.error('Детали ошибки:', error.response?.data)
                console.error('Статус ошибки:', error.response?.status)
                this.allFields = []
            }
        },
        editField() {
            this.$router.push({ name: 'fields.edit', params: { id: this.field.id } })
        },
        async deleteField() {
            if (confirm(`Вы уверены, что хотите удалить поле "${this.field.name}"?`)) {
                try {
                    await axios.delete(`/api/v1/fields/${this.field.id}`)
                    this.$router.push({ name: 'fields.index' })
                } catch (error) {
                    console.error('Ошибка удаления поля:', error)
                    alert('Ошибка при удалении поля')
                }
            }
        },
        viewFarm(farm) {
            this.$router.push({ name: 'farms.show', params: { id: farm.id } })
        },
        editCropRotation(cropRotation) {
            this.editingCropRotation = cropRotation
            this.cropRotationForm = {
                crop_id: cropRotation.crop_id,
                planting_date: cropRotation.planting_date,
                harvest_date: cropRotation.harvest_date || ''
            }
            this.showCropRotationDialog = true
        },
        async deleteCropRotation(cropRotation) {
            if (confirm('Вы уверены, что хотите удалить этот севооборот?')) {
                try {
                    await axios.delete(`/api/v1/crop-rotations/${cropRotation.id}`)
                    await this.fetchField()
                } catch (error) {
                    console.error('Ошибка удаления севооборота:', error)
                    alert('Ошибка при удалении севооборота')
                }
            }
        },
        async saveCropRotation() {
            if (!this.cropRotationFormValid) return

            this.savingCropRotation = true
            try {
                const data = {
                    field_id: this.field.id,
                    ...this.cropRotationForm
                }

                if (this.editingCropRotation) {
                    await axios.put(`/api/v1/crop-rotations/${this.editingCropRotation.id}`, data)
                } else {
                    await axios.post('/api/v1/crop-rotations', data)
                }

                await this.fetchField()
                this.closeCropRotationDialog()
            } catch (error) {
                console.error('Ошибка сохранения севооборота:', error)
                alert('Ошибка при сохранении севооборота')
            } finally {
                this.savingCropRotation = false
            }
        },
        closeCropRotationDialog() {
            this.showCropRotationDialog = false
            this.editingCropRotation = null
            this.cropRotationForm = {
                crop_id: '',
                planting_date: '',
                harvest_date: ''
            }
        },
        getCropStatusColor(cropRotation) {
            const today = new Date()
            const plantingDate = new Date(cropRotation.planting_date)
            const harvestDate = cropRotation.harvest_date ? new Date(cropRotation.harvest_date) : null

            if (today < plantingDate) return 'warning' // Планируется
            if (harvestDate && today > harvestDate) return 'success' // Завершен
            return 'primary' // Активен
        },
        getCropStatusText(cropRotation) {
            const today = new Date()
            const plantingDate = new Date(cropRotation.planting_date)
            const harvestDate = cropRotation.harvest_date ? new Date(cropRotation.harvest_date) : null

            if (today < plantingDate) return 'Планируется'
            if (harvestDate && today > harvestDate) return 'Завершен'
            return 'Активен'
        },
        addStation() {
            this.$router.push({ name: 'stations.create', query: { field_id: this.field.id } })
        },
        viewStation(station) {
            this.$router.push({ name: 'stations.show', params: { id: station.id } })
        },
        viewStationData(station) {
            this.$router.push({ name: 'stations.data', params: { id: station.id } })
        },
        async moveStation(station) {
            this.selectedStation = station
            this.moveStationForm.field_id = ''
            this.showMoveStationDialog = true
            
            // Фильтруем доступные поля из уже загруженных
            const currentFieldId = parseInt(this.$route.params.id)
            this.availableFields = this.allFields
                .filter(field => field.id !== currentFieldId)
                .map(field => ({
                    id: field.id,
                    name: field.name,
                    farm_id: field.farm_id,
                    area_hectares: field.area_hectares
                }))
            console.log('Доступные поля для переноса:', this.availableFields)
            console.log('Количество доступных полей:', this.availableFields.length)
            
            // Проверяем структуру данных
            if (this.availableFields.length > 0) {
                console.log('Первое поле:', this.availableFields[0])
                console.log('Название первого поля:', this.availableFields[0].name)
            }
        },
        async confirmMoveStation() {
            if (!this.moveStationFormValid) return

            this.movingStation = true
            try {
                await axios.put(`/api/v1/stations/${this.selectedStation.id}/move`, {
                    field_id: this.moveStationForm.field_id
                })
                
                // Обновляем данные поля
                await this.fetchField()
                this.closeMoveStationDialog()
                alert('Станция успешно перенесена')
            } catch (error) {
                console.error('Ошибка переноса станции:', error)
                alert('Ошибка при переносе станции')
            } finally {
                this.movingStation = false
            }
        },
        closeMoveStationDialog() {
            this.showMoveStationDialog = false
            this.selectedStation = null
            this.moveStationForm.field_id = ''
        },
        formatDate(dateString) {
            if (!dateString) return 'Не указана'
            return new Date(dateString).toLocaleDateString('ru-RU')
        },
        formatDateTime(dateString) {
            if (!dateString) return 'Не указана'
            return new Date(dateString).toLocaleString('ru-RU')
        },
        createNewField() {
            this.$router.push({ name: 'fields.create' })
        }
    }
}
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style> 