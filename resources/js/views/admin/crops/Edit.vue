<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Редактировать культуру</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card v-if="crop">
                        <v-card-title>Информация о культуре</v-card-title>
                        <v-card-text>
                            <v-form ref="form" v-model="valid">
                                <v-text-field
                                    v-model="form.name"
                                    label="Название культуры"
                                    :rules="[v => !!v || 'Название обязательно']"
                                    required
                                ></v-text-field>

                                <v-textarea
                                    v-model="form.description"
                                    label="Описание"
                                    rows="4"
                                    hint="Описание культуры, особенности выращивания и т.д."
                                ></v-textarea>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="grey darken-1"
                                text
                                @click="$router.push({ name: 'crops.index' })"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                color="primary"
                                @click="updateCrop"
                                :loading="saving"
                                :disabled="!valid"
                            >
                                Обновить
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                    <v-card v-else>
                        <v-card-text>
                            <v-progress-circular indeterminate></v-progress-circular>
                            Загрузка...
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
    name: 'CropsEdit',
    setup() {
        const crop = ref(null)
        const valid = ref(false)
        const saving = ref(false)
        const form = ref({
            name: '',
            description: ''
        })

        const fetchCrop = async () => {
            try {
                const response = await axios.get(`/api/v1/crops/${window.location.pathname.split('/').pop()}`)
                crop.value = response.data
                form.value = {
                    name: response.data.name,
                    description: response.data.description || ''
                }
            } catch (error) {
                console.error('Ошибка загрузки культуры:', error)
                alert('Ошибка при загрузке культуры')
            }
        }

        const updateCrop = async () => {
            if (!valid.value) return

            saving.value = true
            try {
                await axios.put(`/api/v1/crops/${crop.value.id}`, form.value)
                // Перенаправляем на список культур
                window.location.href = '/admin/crops'
            } catch (error) {
                console.error('Ошибка обновления культуры:', error)
                alert('Ошибка при обновлении культуры')
            } finally {
                saving.value = false
            }
        }

        onMounted(() => {
            fetchCrop()
        })

        return {
            crop,
            valid,
            saving,
            form,
            updateCrop
        }
    }
}
</script> 