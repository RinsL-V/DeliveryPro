<template>
  <div class="min-h-screen bg-gradient-to-br from-DAE9E8 to-85AFB5 py-8 px-4 relative">
    <div class="noise-overlay-section"></div>
    <div class="container mx-auto max-w-2xl">
      <div class="bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <div class="noise-overlay-card"></div>
        
        <button 
          @click="$emit('back')" 
          class="flex items-center text-393B3C hover:text-85AFB5 transition-colors duration-300 mb-6"
        >
          ← Назад
        </button>
        
        <h2 class="text-3xl font-bold text-393B3C mb-8">Создать заказ доставки</h2>

        <form @submit.prevent="createOrder" class="space-y-6">
          <!-- Адреса -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-group">
              <label class="text-393B3C font-medium">Адрес отправления *</label>
              <textarea 
                v-model="form.from_address" 
                required 
                rows="3"
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
                placeholder="Улица, дом, квартира"
              ></textarea>
            </div>
            
            <div class="form-group">
              <label class="text-393B3C font-medium">Адрес доставки *</label>
              <textarea 
                v-model="form.to_address" 
                required 
                rows="3"
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
                placeholder="Улица, дом, квартира"
              ></textarea>
            </div>
          </div>

          <!-- Информация о посылке -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="form-group">
              <label class="text-393B3C font-medium">Тип посылки *</label>
              <select 
                v-model="form.package_type" 
                required
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
              >
                <option value="">Выберите тип</option>
                <option value="Документы">Документы</option>
                <option value="Малая посылка">Малая посылка</option>
                <option value="Средняя посылка">Средняя посылка</option>
                <option value="Крупная посылка">Крупная посылка</option>
                <option value="Хрупкий груз">Хрупкий груз</option>
                <option value="Продукты">Продукты</option>
              </select>
            </div>
            
            <div class="form-group">
              <label class="text-393B3C font-medium">Вес (кг) *</label>
              <input 
                v-model="form.weight" 
                type="number" 
                step="0.1" 
                min="0.1" 
                max="50"
                required
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
              >
            </div>
            
            <div class="form-group">
              <label class="text-393B3C font-medium">Габариты</label>
              <input 
                v-model="form.dimensions" 
                type="text" 
                placeholder="ДхШхВ см"
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
              >
            </div>
          </div>

          <!-- Описание -->
          <div class="form-group">
            <label class="text-393B3C font-medium">Описание посылки</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              placeholder="Опишите содержимое посылки, особые условия доставки и т.д."
              class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
            ></textarea>
          </div>

          <!-- Кнопки -->
          <div class="flex gap-4 pt-4">
            <button 
              type="button"
              @click="$emit('back')"
              class="flex-1 border border-393B3C text-393B3C font-semibold py-3 px-6 rounded-lg hover:bg-393B3C hover:text-white transition-all duration-300"
            >
              Отмена
            </button>
            <button 
              type="submit"
              :disabled="loading"
              class="flex-1 bg-1AF9D5 text-393B3C font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all duration-300 disabled:opacity-50"
            >
              {{ loading ? 'Создание...' : 'Создать заказ' }}
            </button>
          </div>

          <!-- Сообщение об ошибке/успехе -->
          <div v-if="message" :class="['p-4 rounded-lg', success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
            {{ message }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['order-created', 'back'])

const form = ref({
  from_address: '',
  to_address: '',
  package_type: '',
  weight: '',
  dimensions: '',
  description: ''
})

const loading = ref(false)
const message = ref('')
const success = ref(false)

const createOrder = async () => {
  try {
    loading.value = true
    message.value = ''
    
    const user = JSON.parse(localStorage.getItem('user_data'))
    
    const response = await fetch('http://localhost:8000/api/orders.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        ...form.value,
        user_id: user.id
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      success.value = true
      message.value = 'Заказ успешно создан! Номер заказа: #' + data.order_id
      
      form.value = {
        from_address: '',
        to_address: '',
        package_type: '',
        weight: '',
        dimensions: '',
        description: ''
      }
      
      setTimeout(() => {
        emit('order-created')
      }, 2000)
    } else {
      success.value = false
      message.value = data.message || 'Ошибка при создании заказа'
    }
  } catch (error) {
    success.value = false
    message.value = 'Ошибка соединения с сервером'
    console.error('Ошибка создания заказа:', error)
  } finally {
    loading.value = false
  }
}
</script>