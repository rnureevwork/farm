<template>
    <div>
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Севооборот поля: {{ field?.name }}</span>
                <v-btn
                    color="primary"
                    @click="showAddDialog = true"
                    prepend-icon="mdi-plus"
                >
                    Добавить культуру
                </v-btn>
            </v-card-title>
            
            <v-card-text>
                <v-data-table
                    :headers="headers"
                    :items="cropRotations"
                    :loading="loading"
                    class="elevation-1"
                >
                    <template v-slot:item.crop.name="{ item }">
                        <strong>{{ item.crop.name }}</strong>
                    </template>
                    
                    <template v-slot:item.planting_date="{ item }">
                        {{ formatDate(item.planting_date) }}
                    </template>
                    
                    <template v-slot:item.harvest_date="{ item }">
                        {{ item.harvest_date ? formatDate(item.harvest_date) : '—' }}
                    </template>
                    
                    <template v-slot:item.status="{ item }">
                        <v-chip
                            :color="getStatusColor(item)"
                            small
                        >
                            {{ getStatusText(item) }}
                        </v-chip>
                    </template>
                    
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            icon
                            small
                            @click="editCropRotation(item)"
                            color="primary"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn
                            icon
                            small
                            @click="deleteCropRotation(item)"
                            color="error"
                        >
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>

        <!-- Диалог добавления/редактирования севооборота -->
        <v-dialog v-model="showAddDialog" max-width="500px">
            <v-card>
                <v-card-title>
                    {{ editingCropRotation ? 'Редактировать севооборот' : 'Добавить культуру' }}
                </v-card-title>
                
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-select
                            v-model="form.crop_id"
                            :items="crops"
                            item-title="name"
                            item-value="id"
                            label="Культура"
                            :rules="[v => !!v || 'Выберите культуру']"
                            required
                        ></v-select>
                        
                        <v-text-field
                            v-model="form.planting_date"
                            label="Дата посадки"
                            type="date"
                            :rules="[v => !!v || 'Укажите дату посадки']"
                            required
                        ></v-text-field>
                        
                        <v-text-field
                            v-model="form.harvest_date"
                            label="Дата сбора урожая"
                            type="date"
                            :min="form.planting_date"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey darken-1"
                        text
                        @click="closeDialog"
                    >
                        Отмена
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="saveCropRotation"
                        :loading="saving"
                        :disabled="!valid"
                    >
                        {{ editingCropRotation ? 'Обновить' : 'Добавить' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
    name: 'CropRotation',
    props: {
        fieldId: {
            type: [String, Number],
            required: true
        }
    },
    setup(props) {
        const field = ref(null)
        const cropRotations = ref([])
        const crops = ref([])
        const loading = ref(true)
        const saving = ref(false)
        const showAddDialog = ref(false)
        const editingCropRotation = ref(null)
        const valid = ref(false)
        const form = ref({
            crop_id: '',
            planting_date: '',
            harvest_date: ''
        })

        const headers = [
            { title: 'Культура', key: 'crop.name', sortable: true },
            { title: 'Дата посадки', key: 'planting_date', sortable: true },
            { title: 'Дата сбора', key: 'harvest_date', sortable: true },
            { title: 'Статус', key: 'status', sortable: false },
            { title: 'Действия', key: 'actions', sortable: false }
        ]

        const fetchField = async () => {
            try {
                const response = await axios.get(`/api/v1/fields/${props.fieldId}`)
                field.value = response.data
            } catch (error) {
                console.error('Ошибка загрузки поля:', error)
            }
        }

        const fetchCropRotations = async () => {
            try {
                const response = await axios.get(`/api/v1/fields/${props.fieldId}/crop-rotations`)
                cropRotations.value = response.data
            } catch (error) {
                console.error('Ошибка загрузки севооборота:', error)
            } finally {
                loading.value = false
            }
        }

        const fetchCrops = async () => {
            try {
                const response = await axios.get('/api/v1/crops')
                crops.value = response.data
            } catch (error) {
                console.error('Ошибка загрузки культур:', error)
            }
        }

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('ru-RU')
        }

        const getStatusColor = (cropRotation) => {
            const today = new Date()
            const plantingDate = new Date(cropRotation.planting_date)
            const harvestDate = cropRotation.harvest_date ? new Date(cropRotation.harvest_date) : null

            if (today < plantingDate) return 'warning' // Планируется
            if (harvestDate && today > harvestDate) return 'success' // Завершен
            return 'primary' // Активен
        }

        const getStatusText = (cropRotation) => {
            const today = new Date()
            const plantingDate = new Date(cropRotation.planting_date)
            const harvestDate = cropRotation.harvest_date ? new Date(cropRotation.harvest_date) : null

            if (today < plantingDate) return 'Планируется'
            if (harvestDate && today > harvestDate) return 'Завершен'
            return 'Активен'
        }

        const editCropRotation = (cropRotation) => {
            editingCropRotation.value = cropRotation
            form.value = {
                crop_id: cropRotation.crop_id,
                planting_date: cropRotation.planting_date,
                harvest_date: cropRotation.harvest_date || ''
            }
            showAddDialog.value = true
        }

        const deleteCropRotation = async (cropRotation) => {
            if (confirm('Вы уверены, что хотите удалить этот севооборот?')) {
                try {
                    await axios.delete(`/api/v1/crop-rotations/${cropRotation.id}`)
                    await fetchCropRotations()
                } catch (error) {
                    console.error('Ошибка удаления севооборота:', error)
                }
            }
        }

        const saveCropRotation = async () => {
            if (!valid.value) return

            saving.value = true
            try {
                const data = {
                    field_id: props.fieldId,
                    ...form.value
                }

                if (editingCropRotation.value) {
                    await axios.put(`/api/v1/crop-rotations/${editingCropRotation.value.id}`, data)
                } else {
                    await axios.post('/api/v1/crop-rotations', data)
                }

                await fetchCropRotations()
                closeDialog()
            } catch (error) {
                console.error('Ошибка сохранения севооборота:', error)
            } finally {
                saving.value = false
            }
        }

        const closeDialog = () => {
            showAddDialog.value = false
            editingCropRotation.value = null
            form.value = {
                crop_id: '',
                planting_date: '',
                harvest_date: ''
            }
        }

        onMounted(() => {
            fetchField()
            fetchCropRotations()
            fetchCrops()
        })

        return {
            field,
            cropRotations,
            crops,
            loading,
            saving,
            showAddDialog,
            editingCropRotation,
            valid,
            form,
            headers,
            formatDate,
            getStatusColor,
            getStatusText,
            editCropRotation,
            deleteCropRotation,
            saveCropRotation,
            closeDialog
        }
    }
}
</script> 