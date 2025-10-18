<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-500/50">
    <nav class="container mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <h1 class="text-xl font-bold text-white cursor-pointer" @click="$emit('back')">
            DeliveryPro
          </h1>
        </div>

        <div class="flex items-center space-x-4">
          <template v-if="!user">
            <button 
              @click="$emit('open-auth')"
              class="text-gray-300 hover:text-white transition-colors"
            >
              Войти
            </button>
          </template>

          <template v-else>
            <button 
              @click="$emit('open-create-order')"
              class="text-gray-300 hover:text-white transition-colors"
            >
              Создать заказ
            </button>
            <button 
              @click="$emit('open-orders')"
              class="text-gray-300 hover:text-white transition-colors"
            >
              Мои заказы
            </button>
            <button 
              @click="$emit('open-profile')"
              class="text-gray-300 hover:text-white transition-colors"
            >
              Профиль
            </button>
            
            <button 
              v-if="user.role === 'admin'"
              @click="$emit('open-admin')"
              class="text-orange-400 hover:text-orange-300 transition-colors"
            >
              Админ
            </button>

            <button 
              @click="$emit('logout')"
              class="text-gray-300 hover:text-white transition-colors"
            >
              Выйти
            </button>
          </template>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
defineProps({
  currentPage: String,
  user: Object
})

defineEmits([
  'back',
  'open-auth', 
  'open-profile',
  'open-orders',
  'open-create-order',
  'open-admin',
  'logout'
])
</script>