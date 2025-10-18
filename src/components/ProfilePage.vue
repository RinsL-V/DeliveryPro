<template>
  <div class="min-h-screen bg-gradient-to-br from-DAE9E8 to-85AFB5 py-8 px-4 relative">
    <div class="noise-overlay-section"></div>
    <div class="container mx-auto max-w-4xl">
      <div class="bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <div class="noise-overlay-card"></div>
        
        <button 
          @click="$emit('back')" 
          class="flex items-center text-393B3C hover:text-85AFB5 transition-colors duration-300 mb-6"
        >
          ← Назад
        </button>
        
        <h2 class="text-3xl font-bold text-393B3C mb-8">Профиль пользователя</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Информация о пользователе -->
          <div class="space-y-6">
            <div class="bg-DAE6E6 rounded-xl p-6">
              <h3 class="text-xl font-semibold text-393B3C mb-4">Основная информация</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-85AFB5 text-sm">Имя</p>
                  <p class="text-393B3C font-medium">{{ user.full_name || 'Не указано' }}</p>
                </div>
                <div>
                  <p class="text-85AFB5 text-sm">Email</p>
                  <p class="text-393B3C font-medium">{{ user.email || 'Не указан' }}</p>
                </div>
                <div>
                  <p class="text-85AFB5 text-sm">Роль</p>
                  <p class="text-393B3C font-medium capitalize">{{ user.role || 'user' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Действия -->
          <div class="space-y-6">
            <div class="bg-DAE6E6 rounded-xl p-6">
              <h3 class="text-xl font-semibold text-393B3C mb-4">Действия</h3>
              <div class="space-y-3">
                <button 
                  @click="$emit('open-orders')"
                  class="w-full bg-1AF9D5 text-393B3C font-semibold py-3 px-4 rounded-lg hover:bg-opacity-90 transition-all duration-300 text-left"
                >
                  Мои заказы
                </button>
                
                <button 
                  @click="$emit('open-create-order')"
                  class="w-full bg-1AF9D5 text-393B3C font-semibold py-3 px-4 rounded-lg hover:bg-opacity-90 transition-all duration-300 text-left"
                >
                  Создать заказ
                </button>

                <button 
                  v-if="user.role === 'admin'"
                  @click="$emit('open-admin')"
                  class="w-full bg-393B3C text-DAE9E8 font-semibold py-3 px-4 rounded-lg hover:bg-opacity-90 transition-all duration-300 text-left"
                >
                  Панель администратора
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Статистика -->
        <div class="mt-8 bg-gradient-to-r from-1AF9D5 to-85AFB5 rounded-xl p-6 text-white">
          <h3 class="text-xl font-semibold mb-4">Статистика</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
              <p class="text-2xl font-bold">{{ userStats.total || 0 }}</p>
              <p class="text-sm opacity-90">Всего заказов</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold">{{ userStats.active || 0 }}</p>
              <p class="text-sm opacity-90">Активные</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold">{{ userStats.delivered || 0 }}</p>
              <p class="text-sm opacity-90">Завершенные</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold">{{ userStats.cancelled || 0 }}</p>
              <p class="text-sm opacity-90">Отменено</p>
            </div>
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

const emit = defineEmits(['profile-updated', 'back', 'open-orders', 'open-create-order', 'open-admin'])

const userOrders = ref([])

const userStats = computed(() => {
  return {
    total: userOrders.value.length,
    active: userOrders.value.filter(o => ['new', 'processing', 'delivering'].includes(o.status)).length,
    delivered: userOrders.value.filter(o => o.status === 'delivered').length,
    cancelled: userOrders.value.filter(o => o.status === 'cancelled').length
  }
})

const loadUserOrders = async () => {
  try {
    const response = await fetch(`http://localhost:8000/api/orders.php?user_id=${props.user.id}`)
    const data = await response.json()
    userOrders.value = data
  } catch (error) {
    console.error('Ошибка загрузки заказов:', error)
  }
}

onMounted(() => {
  loadUserOrders()
})
</script>