<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12" class="d-flex justify-space-between align-center">
                    <h1 class="text-h4">Культуры</h1>
                    <v-btn
                        color="primary"
                        @click="$router.push({ name: 'crops.create' })"
                        prepend-icon="mdi-plus"
                    >
                        Добавить культуру
                    </v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-data-table
                                :headers="headers"
                                :items="crops"
                                :loading="loading"
                                :search="search"
                                class="elevation-1"
                            >
                                <template v-slot:top>
                                    <v-text-field
                                        v-model="search"
                                        label="Поиск"
                                        prepend-inner-icon="mdi-magnify"
                                        single-line
                                        hide-details
                                        class="mx-4"
                                    ></v-text-field>
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <v-btn
                                        icon
                                        small
                                        @click="editCrop(item)"
                                        color="primary"
                                    >
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        small
                                        @click="deleteCrop(item)"
                                        color="error"
                                    >
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
import axios from 'axios'

export default {
    name: 'CropsIndex',
    setup() {
        const crops = ref([])
        const loading = ref(true)
        const search = ref('')

        const headers = [
            { title: 'ID', key: 'id', sortable: true },
            { title: 'Название', key: 'name', sortable: true },
            { title: 'Описание', key: 'description', sortable: false },
            { title: 'Действия', key: 'actions', sortable: false }
        ]

        const fetchCrops = async () => {
            try {
                const response = await axios.get('/api/v1/crops')
                crops.value = response.data
            } catch (error) {
                console.error('Ошибка загрузки культур:', error)
            } finally {
                loading.value = false
            }
        }

        const editCrop = (crop) => {
            // TODO: Реализовать переход к редактированию
            console.log('Edit crop:', crop)
        }

        const deleteCrop = async (crop) => {
            if (confirm(`Вы уверены, что хотите удалить культуру "${crop.name}"?`)) {
                try {
                    await axios.delete(`/api/v1/crops/${crop.id}`)
                    await fetchCrops()
                } catch (error) {
                    console.error('Ошибка удаления культуры:', error)
                }
            }
        }

        onMounted(() => {
            fetchCrops()
        })

        return {
            crops,
            loading,
            search,
            headers,
            editCrop,
            deleteCrop
        }
    }
}
</script> 