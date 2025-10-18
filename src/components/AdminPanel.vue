<template>
  <div class="min-h-screen bg-gradient-to-br from-393B3C to-85AFB5 py-8 px-4 relative">
    <div class="noise-overlay-section"></div>
    <div class="container mx-auto max-w-7xl">
      <div class="bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <div class="noise-overlay-card"></div>
        
        <div class="flex items-center justify-between mb-8">
          <button 
            @click="$emit('back')" 
            class="flex items-center text-393B3C hover:text-85AFB5 transition-colors duration-300"
          >
            ← Назад
          </button>
          <h2 class="text-3xl font-bold text-393B3C">Панель администратора</h2>
          <div class="w-6"></div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-1AF9D5 rounded-xl p-6 text-393B3C">
            <h3 class="text-lg font-semibold mb-2">Всего заказов</h3>
            <p class="text-2xl font-bold">{{ stats.orders?.total_orders || 0 }}</p>
          </div>
          <div class="bg-85AFB5 rounded-xl p-6 text-white">
            <h3 class="text-lg font-semibold mb-2">На модерации</h3>
            <p class="text-2xl font-bold">{{ stats.orders?.new_orders || 0 }}</p>
          </div>
          <div class="bg-393B3C rounded-xl p-6 text-DAE9E8">
            <h3 class="text-lg font-semibold mb-2">Пользователи</h3>
            <p class="text-2xl font-bold">{{ stats.users?.total_users || 0 }}</p>
          </div>
          <div class="bg-DAE6E6 rounded-xl p-6 text-393B3C">
            <h3 class="text-lg font-semibold mb-2">Активные курьеры</h3>
            <p class="text-2xl font-bold">{{ stats.couriers?.active_couriers || 0 }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
          <!-- Заказы на модерации -->
          <div class="bg-DAE6E6 rounded-xl p-6">
            <h3 class="text-xl font-semibold text-393B3C mb-4">Заказы на модерации</h3>
            <div class="space-y-4">
              <div 
                v-for="order in stats.moderation_orders" 
                :key="order.id"
                class="bg-white rounded-lg p-4"
              >
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h4 class="font-semibold text-393B3C">Заказ #{{ order.id }}</h4>
                    <p class="text-85AFB5 text-sm">{{ order.user_name }}</p>
                  </div>
                  <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">На модерации</span>
                </div>
                <p class="text-393B3C text-sm mb-2">{{ order.from_address }} → {{ order.to_address }}</p>
                <div class="flex gap-2 mt-3">
                  <button 
                    @click="updateOrderStatus(order.id, 'processing')"
                    class="bg-1AF9D5 text-393B3C text-sm px-3 py-1 rounded hover:bg-opacity-90 transition-all"
                  >
                    Одобрить
                  </button>
                  <button 
                    @click="updateOrderStatus(order.id, 'cancelled')"
                    class="bg-red-500 text-white text-sm px-3 py-1 rounded hover:bg-red-600 transition-all"
                  >
                    Отклонить
                  </button>
                </div>
              </div>
              <div v-if="!stats.moderation_orders || stats.moderation_orders.length === 0" class="text-center py-4">
                <p class="text-393B3C">Нет заказов на модерации</p>
              </div>
            </div>
          </div>

          <!-- Управление курьерами -->
          <div class="bg-DAE6E6 rounded-xl p-6">
            <h3 class="text-xl font-semibold text-393B3C mb-4">Активные курьеры</h3>
            <div class="space-y-3">
              <div 
                v-for="courier in stats.active_couriers" 
                :key="courier.id"
                class="bg-white rounded-lg p-4"
              >
                <p class="font-semibold text-393B3C">{{ courier.name }}</p>
                <p class="text-85AFB5 text-sm">{{ courier.phone }} • {{ courier.vehicle_type }}</p>
              </div>
              <div v-if="!stats.active_couriers || stats.active_couriers.length === 0" class="text-center py-4">
                <p class="text-393B3C">Нет активных курьеров</p>
              </div>
            </div>
            
            <!-- Форма добавления курьера -->
            <div class="mt-6 bg-white rounded-lg p-4">
              <h4 class="font-semibold text-393B3C mb-3">Добавить курьера</h4>
              <div class="space-y-3">
                <input 
                  v-model="newCourier.name"
                  type="text" 
                  placeholder="Имя курьера"
                  class="w-full px-3 py-2 border border-DAE6E6 rounded focus:border-1AF9D5 focus:ring-1 focus:ring-1AF9D5"
                >
                <input 
                  v-model="newCourier.phone"
                  type="text" 
                  placeholder="Телефон"
                  class="w-full px-3 py-2 border border-DAE6E6 rounded focus:border-1AF9D5 focus:ring-1 focus:ring-1AF9D5"
                >
                <select 
                  v-model="newCourier.vehicle_type"
                  class="w-full px-3 py-2 border border-DAE6E6 rounded focus:border-1AF9D5 focus:ring-1 focus:ring-1AF9D5"
                >
                  <option value="">Тип транспорта</option>
                  <option value="Автомобиль">Автомобиль</option>
                  <option value="Мотоцикл">Мотоцикл</option>
                  <option value="Велосипед">Велосипед</option>
                  <option value="Пеший">Пеший</option>
                </select>
                <button 
                  @click="addCourier"
                  class="bg-1AF9D5 text-393B3C px-4 py-2 rounded hover:bg-opacity-90 transition-all"
                >
                  Добавить курьера
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const emit = defineEmits(['back'])

const stats = ref({})
const newCourier = ref({
  name: '',
  phone: '',
  vehicle_type: ''
})

const loadStats = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/admin.php')
    const data = await response.json()
    stats.value = data
  } catch (error) {
    console.error('Ошибка загрузки статистики:', error)
  }
}

const updateOrderStatus = async (orderId, status) => {
  try {
    const response = await fetch('http://localhost:8000/api/admin.php', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        order_id: orderId,
        status: status
      })
    })
    
    const data = await response.json()
    if (data.success) {
      await loadStats()
    }
  } catch (error) {
    console.error('Ошибка обновления статуса:', error)
  }
}

const addCourier = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/admin.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'add_courier',
        ...newCourier.value
      })
    })
    
    const data = await response.json()
    if (data.success) {
      newCourier.value = { name: '', phone: '', vehicle_type: '' }
      await loadStats()
    }
  } catch (error) {
    console.error('Ошибка добавления курьера:', error)
  }
}

onMounted(() => {
  loadStats()
})
</script>