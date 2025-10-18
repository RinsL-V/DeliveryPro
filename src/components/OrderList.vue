<template>
  <div class="min-h-screen bg-gradient-to-br from-DAE9E8 to-85AFB5 py-8 px-4 relative">
    <div class="noise-overlay-section"></div>
    <div class="container mx-auto max-w-6xl">
      <div class="bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <div class="noise-overlay-card"></div>
        
        <div class="flex items-center justify-between mb-8">
          <button 
            @click="$emit('back')" 
            class="flex items-center text-393B3C hover:text-85AFB5 transition-colors duration-300"
          >
            ← Назад
          </button>
          <h2 class="text-3xl font-bold text-393B3C">Мои заказы</h2>
          <button 
            @click="$emit('create-new-order')"
            class="bg-1AF9D5 text-393B3C font-semibold py-2 px-4 rounded-lg hover:bg-opacity-90 transition-all duration-300"
          >
            + Новый заказ
          </button>
        </div>

        <!-- Статистика заказов -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-1AF9D5 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-393B3C">{{ ordersStats.total }}</p>
            <p class="text-393B3C text-sm">Всего заказов</p>
          </div>
          <div class="bg-85AFB5 rounded-xl p-4 text-center text-white">
            <p class="text-2xl font-bold">{{ ordersStats.active }}</p>
            <p class="text-sm">Активные</p>
          </div>
          <div class="bg-393B3C rounded-xl p-4 text-center text-DAE9E8">
            <p class="text-2xl font-bold">{{ ordersStats.delivered }}</p>
            <p class="text-sm">Доставлено</p>
          </div>
          <div class="bg-DAE6E6 rounded-xl p-4 text-center text-393B3C">
            <p class="text-2xl font-bold">{{ ordersStats.cancelled }}</p>
            <p class="text-sm">Отменено</p>
          </div>
        </div>

        <!-- Список заказов -->
        <div class="space-y-4">
          <div 
            v-for="order in orders" 
            :key="order.id"
            class="border border-DAE6E6 rounded-xl p-6 hover:shadow-lg transition-all duration-300"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-393B3C">Заказ #{{ order.id }}</h3>
                <p class="text-85AFB5 text-sm">{{ formatDate(order.created_at) }}</p>
              </div>
              <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                {{ getStatusText(order.status) }}
              </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-393B3C font-medium">Откуда:</p>
                <p class="text-393B3C">{{ order.from_address }}</p>
              </div>
              <div>
                <p class="text-393B3C font-medium">Куда:</p>
                <p class="text-393B3C">{{ order.to_address }}</p>
              </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
              <div>
                <p class="text-85AFB5">Тип посылки</p>
                <p class="text-393B3C">{{ order.package_type }}</p>
              </div>
              <div>
                <p class="text-85AFB5">Вес</p>
                <p class="text-393B3C">{{ order.weight }} кг</p>
              </div>
              <div>
                <p class="text-85AFB5">Габариты</p>
                <p class="text-393B3C">{{ order.dimensions || 'Не указаны' }}</p>
              </div>
              <div>
                <p class="text-85AFB5">Описание</p>
                <p class="text-393B3C">{{ order.description || 'Нет описания' }}</p>
              </div>
            </div>

            <div v-if="order.comments" class="mt-4 p-3 bg-DAE6E6 rounded-lg">
              <p class="text-393B3C text-sm"><strong>Комментарий администратора:</strong> {{ order.comments }}</p>
            </div>
          </div>

          <div v-if="orders.length === 0" class="text-center py-12">
            <p class="text-393B3C text-lg mb-4">У вас пока нет заказов</p>
            <button 
              @click="$emit('create-new-order')"
              class="bg-1AF9D5 text-393B3C font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all duration-300"
            >
              Создать первый заказ
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  user: Object
})

const emit = defineEmits(['back', 'create-new-order'])

const orders = ref([])
const loading = ref(true)

const ordersStats = computed(() => {
  return {
    total: orders.value.length,
    active: orders.value.filter(o => ['new', 'processing', 'delivering'].includes(o.status)).length,
    delivered: orders.value.filter(o => o.status === 'delivered').length,
    cancelled: orders.value.filter(o => o.status === 'cancelled').length
  }
})

const getStatusClass = (status) => {
  const classes = {
    'new': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'delivering': 'bg-1AF9D5 text-393B3C',
    'delivered': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    'new': 'Новый',
    'processing': 'В обработке',
    'delivering': 'В доставке',
    'delivered': 'Доставлен',
    'cancelled': 'Отменен'
  }
  return texts[status] || status
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const loadOrders = async () => {
  try {
    loading.value = true
    const response = await fetch(`http://localhost:8000/api/orders.php?user_id=${props.user.id}`)
    const data = await response.json()
    orders.value = data
  } catch (error) {
    console.error('Ошибка загрузки заказов:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadOrders()
})
</script>