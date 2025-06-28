<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12" class="d-flex justify-space-between align-center">
                    <h1 class="text-h4">Поля</h1>
                    <v-btn color="primary" @click="$router.push({ name: 'fields.create' })">
                        <v-icon left>mdi-plus</v-icon>
                        Добавить поле
                    </v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-data-table
                                :headers="headers"
                                :items="fields"
                                :loading="loading"
                                :search="search"
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

                                <template v-slot:item.area_hectares="{ item }">
                                    {{ item.area_hectares }} га
                                </template>

                                <template v-slot:item.current_crop="{ item }">
                                    <v-chip v-if="item.current_crop" color="primary" small>
                                        {{ item.current_crop.crop?.name || 'Культура' }}
                                    </v-chip>
                                    <span v-else class="text-grey">Нет культуры</span>
                                </template>

                                <template v-slot:item.stations_count="{ item }">
                                    <v-chip color="info" small>
                                        {{ item.stations_count || 0 }} станций
                                    </v-chip>
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <v-btn icon small @click="editField(item)">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn icon small @click="deleteField(item)" color="error">
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
    name: 'FieldsIndex',
    setup() {
        const router = useRouter()
        const fields = ref([])
        const loading = ref(true)
        const search = ref('')

        const headers = [
            { text: 'Название', value: 'name' },
            { text: 'Ферма', value: 'farm.name' },
            { text: 'Площадь', value: 'area_hectares' },
            { text: 'Текущая культура', value: 'current_crop' },
            { text: 'Станции', value: 'stations_count' },
            { text: 'Действия', value: 'actions', sortable: false }
        ]

        const fetchFields = async () => {
            try {
                const response = await axios.get('/api/v1/fields')
                fields.value = response.data
            } catch (error) {
                console.error('Ошибка загрузки полей:', error)
            } finally {
                loading.value = false
            }
        }

        const editField = (field) => {
            router.push({ name: 'fields.edit', params: { id: field.id } })
        }

        const deleteField = async (field) => {
            if (confirm('Вы уверены, что хотите удалить это поле?')) {
                try {
                    await axios.delete(`/api/v1/fields/${field.id}`)
                    await fetchFields()
                } catch (error) {
                    console.error('Ошибка удаления поля:', error)
                }
            }
        }

        onMounted(() => {
            fetchFields()
        })

        return {
            fields,
            loading,
            search,
            headers,
            editField,
            deleteField
        }
    }
}
</script> 