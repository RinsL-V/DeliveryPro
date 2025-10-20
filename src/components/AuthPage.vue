<template>
  <div class="min-h-screen bg-gradient-to-br from-85AFB5 to-393B3C flex items-center justify-center p-4 relative">
    <div class="noise-overlay-section"></div>
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-2xl p-8 relative overflow-hidden">
        <div class="noise-overlay-card"></div>
        
        <button 
          @click="$emit('back')" 
          class="absolute top-4 left-4 text-393B3C hover:text-85AFB5 transition-colors duration-300"
        >
          ← Назад
        </button>
        
        <h2 class="text-3xl font-bold text-center text-393B3C mb-8">
          {{ isLogin ? "Вход" : "Регистрация" }}
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="form-group">
            <label class="text-393B3C">Логин:</label>
            <input 
              v-model="form.login" 
              type="text" 
              required 
              class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
              placeholder="Введите ваш логин"
            />
          </div>

          <div class="form-group">
            <label class="text-393B3C">Пароль:</label>
            <input 
              v-model="form.password" 
              type="password" 
              required 
              class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
              placeholder="Введите ваш пароль"
            />
          </div>

          <div v-if="!isLogin" class="space-y-4">
            <div class="form-group">
              <label class="text-393B3C">Полное имя:</label>
              <input 
                v-model="form.full_name" 
                type="text" 
                required 
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
                placeholder="Иванов Иван Иванович"
              />
            </div>

            <div class="form-group">
              <label class="text-393B3C">Телефон:</label>
              <input 
                v-model="form.phone" 
                type="text" 
                required 
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
                placeholder="+7 (999) 123-45-67"
              />
            </div>

            <div class="form-group">
              <label class="text-393B3C">Email:</label>
              <input 
                v-model="form.email" 
                type="email" 
                required 
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
                placeholder="example@mail.ru"
              />
            </div>

            <div class="form-group">
              <label class="text-393B3C">Адрес:</label>
              <input 
                v-model="form.address" 
                type="text" 
                class="w-full px-4 py-3 border border-DAE6E6 rounded-lg focus:border-1AF9D5 focus:ring-2 focus:ring-1AF9D5/20 transition-all duration-300"
                placeholder="г. Москва, ул. Примерная, д. 1"
              />
            </div>
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full bg-1AF9D5 text-393B3C font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all duration-300 shadow-1AF9D5/25 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Загрузка...' : isLogin ? "Войти" : "Зарегистрироваться" }}
          </button>

          <p 
            class="text-center text-85AFB5 cursor-pointer hover:text-1AF9D5 transition-colors duration-300" 
            @click="isLogin = !isLogin"
          >
            {{ isLogin ? "Нет аккаунта? Зарегистрироваться" : "Уже есть аккаунт? Войти" }}
          </p>

          <div 
            v-if="message" 
            :class="['p-4 rounded-lg transition-all duration-300 border', 
                     success ? 'bg-green-100 text-green-700 border-green-300' : 'bg-red-100 text-red-700 border-red-300']"
          >
            <p class="text-center font-medium">{{ message }}</p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits(['login-success', 'register-success', 'back']);

const isLogin = ref(true);
const message = ref("");
const success = ref(false);
const loading = ref(false);

const form = ref({
  login: "",
  password: "",
  full_name: "",
  phone: "",
  email: "",
  address: "",
});

const handleSubmit = async () => {
  loading.value = true;
  message.value = "";
  success.value = false;

  // Валидация на клиенте
  if (!form.value.login || !form.value.password) {
    message.value = "Логин и пароль обязательны для заполнения";
    success.value = false;
    loading.value = false;
    return;
  }

  if (!isLogin.value && (!form.value.email || !form.value.full_name || !form.value.phone)) {
    message.value = "Все обязательные поля должны быть заполнены";
    success.value = false;
    loading.value = false;
    return;
  }

  const action = isLogin.value ? "login" : "register";
  const payload = { action, ...form.value };

  try {
    const response = await fetch("http://localhost:8000/api/auth.php", {
      method: "POST",
      headers: { 
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });

    const data = await response.json();
    console.log("Ответ от сервера:", data);

    if (data.success) {
      success.value = true;
      message.value = data.message || "Успех!";
      
      // Проверяем, что данные пользователя есть в ответе
      if (data.user && data.user.id) {
        if (isLogin.value) {
          emit('login-success', data.user);
        } else {
          emit('register-success', data.user);
        }
        
        // Очищаем форму после успешной операции
        form.value = {
          login: "",
          password: "",
          full_name: "",
          phone: "",
          email: "",
          address: "",
        };
      } else {
        message.value = "Ошибка: данные пользователя не получены";
        success.value = false;
      }
    } else {
      success.value = false;
      message.value = data.message || "Произошла ошибка";
    }
  } catch (err) {
    success.value = false;
    message.value = "Ошибка соединения с сервером";
    console.error("Ошибка:", err);
  } finally {
    loading.value = false;
  }
};
</script>