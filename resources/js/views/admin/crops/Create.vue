<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Добавить культуру</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
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
                                @click="saveCrop"
                                :loading="saving"
                                :disabled="!valid"
                            >
                                Сохранить
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
    name: 'CropsCreate',
    setup() {
        const valid = ref(false)
        const saving = ref(false)
        const form = ref({
            name: '',
            description: ''
        })

        const saveCrop = async () => {
            if (!valid.value) return

            saving.value = true
            try {
                await axios.post('/api/v1/crops', form.value)
                // Перенаправляем на список культур
                window.location.href = '/admin/crops'
            } catch (error) {
                console.error('Ошибка сохранения культуры:', error)
                alert('Ошибка при сохранении культуры')
            } finally {
                saving.value = false
            }
        }

        return {
            valid,
            saving,
            form,
            saveCrop
        }
    }
}
</script> 