<template>
  <div>
    <div class="global-noise"></div>

    <Header
      :current-page="currentPage"
      :user="user"
      @open-auth="showAuthPage"
      @open-profile="showProfilePage"
      @open-orders="showOrdersPage"
      @open-create-order="showCreateOrderPage"
      @open-admin="showAdminPage"
      @logout="handleLogout"
      @back="showMainPage"
    />

    <div class="min-h-screen flex flex-col pt-16">
      <div v-if="currentPage === 'main'" class="flex-1">
        <main>
          <!-- Hero Section -->
          <section class="py-20 px-8 bg-gradient-to-br from-DAE9E8 to-85AFB5 text-center relative overflow-hidden">
            <div class="noise-overlay-section"></div>
            <div class="max-w-6xl mx-auto">
              <h1 class="text-6xl font-bold text-black mb-6">DeliveryPro</h1>
              <p class="text-393B3C text-xl mb-8 font-medium">Быстрая и надежная доставка по всему городу</p>
              <p class="text-393B3C text-lg mb-12 max-w-2xl mx-auto">
                Доставляем ваши посылки быстро, безопасно и в срок. Работаем 24/7 для вашего удобства.
              </p>
              <div class="flex justify-center gap-4 flex-wrap">
                <button
                  v-if="!user"
                  @click="showAuthPage"
                  class="bg-1AF9D5 text-black font-semibold py-4 px-8 rounded-lg hover:bg-opacity-90 transition-all duration-300 shadow-1AF9D5/25 text-lg"
                >
                  Войти / Зарегистрироваться
                </button>
                <button
                  v-if="user"
                  @click="showCreateOrderPage"
                  class="bg-1AF9D5 text-black font-semibold py-4 px-8 rounded-lg hover:bg-opacity-90 transition-all duration-300 shadow-1AF9D5/25 text-lg"
                >
                  Создать заказ
                </button>
                <button
                  v-if="user"
                  @click="showOrdersPage"
                  class="border border-1AF9D5 text-black py-4 px-8 rounded-lg hover:bg-1AF9D5 hover:text-black transition-all duration-300 text-lg"
                >
                  Мои заказы
                </button>
              </div>
            </div>
          </section>

          <!-- Features Section -->
          <section class="py-16 px-8 bg-DAE6E6">
            <div class="max-w-6xl mx-auto">
              <h2 class="text-4xl font-bold text-black text-center mb-12">Почему выбирают нас?</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300">
                  <div class="w-16 h-16 bg-1AF9D5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🚚</span>
                  </div>
                  <h3 class="text-xl font-bold text-black mb-4">Быстрая доставка</h3>
                  <p class="text-393B3C">Доставляем заказы в течение 2-4 часов по городу. Курьеры на автомобилях и скутерах.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300">
                  <div class="w-16 h-16 bg-1AF9D5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🛡️</span>
                  </div>
                  <h3 class="text-xl font-bold text-black mb-4">Гарантия сохранности</h3>
                  <p class="text-393B3C">Все заказы застрахованы. Компенсация в случае повреждения или утери.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300">
                  <div class="w-16 h-16 bg-1AF9D5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">📱</span>
                  </div>
                  <h3 class="text-xl font-bold text-black mb-4">Отслеживание онлайн</h3>
                  <p class="text-393B3C">Реальное время отслеживания заказа на карте. Уведомления на каждом этапе.</p>
                </div>
              </div>
            </div>
          </section>

          <!-- Pricing Section -->
          <section class="py-16 px-8 bg-white">
            <div class="max-w-6xl mx-auto">
              <h2 class="text-4xl font-bold text-black text-center mb-12">Наши тарифы</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="border-2 border-DAE6E6 rounded-2xl p-8 hover:border-1AF9D5 transition-all duration-300">
                  <h3 class="text-2xl font-bold text-black mb-4">Экспресс</h3>
                  <p class="text-4xl font-bold text-393B3C mb-6">299 ₽</p>
                  <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      До 5 кг
                    </li>
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      До 2-4 часов
                    </li>
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      В пределах города
                    </li>
                  </ul>
                  <button 
                    @click="user ? showCreateOrderPage() : showAuthPage()"
                    class="w-full bg-1AF9D5 text-black font-semibold py-3 rounded-lg hover:bg-opacity-90 transition-all duration-300"
                  >
                    Выбрать
                  </button>
                </div>
                <div class="border-2 border-1AF9D5 rounded-2xl p-8 bg-gradient-to-b from-1AF9D5/10 to-transparent relative">
                  <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="bg-1AF9D5 text-black px-4 py-1 rounded-full text-sm font-semibold">Популярный</span>
                  </div>
                  <h3 class="text-2xl font-bold text-black mb-4">Стандарт</h3>
                  <p class="text-4xl font-bold text-393B3C mb-6">199 ₽</p>
                  <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      До 10 кг
                    </li>
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      До 6 часов
                    </li>
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      По городу и пригороду
                    </li>
                  </ul>
                  <button 
                    @click="user ? showCreateOrderPage() : showAuthPage()"
                    class="w-full bg-1AF9D5 text-black font-semibold py-3 rounded-lg hover:bg-opacity-90 transition-all duration-300"
                  >
                    Выбрать
                  </button>
                </div>
                <div class="border-2 border-DAE6E6 rounded-2xl p-8 hover:border-1AF9D5 transition-all duration-300">
                  <h3 class="text-2xl font-bold text-black mb-4">Эконом</h3>
                  <p class="text-4xl font-bold text-393B3C mb-6">99 ₽</p>
                  <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      До 15 кг
                    </li>
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      До 24 часов
                    </li>
                    <li class="flex items-center text-393B3C">
                      <span class="w-2 h-2 bg-1AF9D5 rounded-full mr-3"></span>
                      Групповая доставка
                    </li>
                  </ul>
                  <button 
                    @click="user ? showCreateOrderPage() : showAuthPage()"
                    class="w-full bg-1AF9D5 text-black font-semibold py-3 rounded-lg hover:bg-opacity-90 transition-all duration-300"
                  >
                    Выбрать
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- CTA Section -->
          <section class="py-16 px-8 bg-gradient-to-r from-1AF9D5 to-85AFB5">
            <div class="max-w-4xl mx-auto text-center">
              <h2 class="text-4xl font-bold text-black mb-6">Готовы отправить посылку?</h2>
              <p class="text-393B3C text-lg mb-8 max-w-2xl mx-auto">
                Присоединяйтесь к тысячам довольных клиентов, которые уже пользуются нашим сервисом доставки
              </p>
              <div class="flex justify-center gap-4 flex-wrap">
                <button
                  v-if="!user"
                  @click="showAuthPage"
                  class="bg-white text-black font-semibold py-4 px-8 rounded-lg hover:bg-opacity-90 transition-all duration-300 shadow-lg text-lg"
                >
                  Начать сейчас
                </button>
                <button
                  v-if="user"
                  @click="showCreateOrderPage"
                  class="bg-white text-black font-semibold py-4 px-8 rounded-lg hover:bg-opacity-90 transition-all duration-300 shadow-lg text-lg"
                >
                  Создать заказ
                </button>
              </div>
            </div>
          </section>
        </main>
      </div>

      <div v-else-if="currentPage === 'auth'">
        <AuthPage
          @login-success="handleLoginSuccess"
          @register-success="handleRegisterSuccess"
          @back="showMainPage"
        />
      </div>

      <div v-else-if="currentPage === 'create-order'">
        <CreateOrder 
          v-if="user" 
          @order-created="handleOrderCreated"
          @back="showMainPage" 
        />
      </div>

      <div v-else-if="currentPage === 'orders'">
        <OrderList 
          v-if="user" 
          :user="user" 
          @back="showMainPage"
          @create-new-order="showCreateOrderPage"
        />
      </div>

      <div v-else-if="currentPage === 'profile'">
        <ProfilePage 
          v-if="user" 
          :user="user" 
          @back="showMainPage" 
          @open-orders="showOrdersPage"
          @open-create-order="showCreateOrderPage"
          @open-admin="showAdminPage"
        />
      </div>

      <div v-else-if="currentPage === 'admin'">
        <AdminPanel 
          v-if="user && user.role === 'admin'" 
          @back="showMainPage" 
        />
      </div>
    </div>

    <!-- Глобальные уведомления -->
    <div class="fixed top-20 right-4 z-50 space-y-2">
      <div 
        v-for="notification in notifications"
        :key="notification.id"
        :class="['p-4 rounded-lg shadow-lg transform transition-all duration-300', 
                 notification.type === 'success' ? 'bg-green-100 border border-green-300 text-green-800' : 
                 notification.type === 'error' ? 'bg-red-100 border border-red-300 text-red-800' :
                 'bg-blue-100 border border-blue-300 text-blue-800']"
      >
        <div class="flex items-center justify-between">
          <span>{{ notification.message }}</span>
          <button 
            @click="removeNotification(notification.id)"
            class="ml-4 text-gray-500 hover:text-gray-700"
          >
            ×
          </button>
        </div>
      </div>
    </div>

    <!-- Футер -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Header from "./components/Header.vue";
import AuthPage from "./components/AuthPage.vue";
import CreateOrder from "./components/CreateOrder.vue";
import OrderList from "./components/OrderList.vue";
import ProfilePage from "./components/ProfilePage.vue";
import AdminPanel from "./components/AdminPanel.vue";
import Footer from "./components/Footer.vue";

const currentPage = ref("main");
const user = ref(null);
const notifications = ref([]);
let notificationId = 0;

const showMainPage = () => (currentPage.value = "main");
const showAuthPage = () => (currentPage.value = "auth");
const showCreateOrderPage = () => {
  if (!user.value) return showAuthPage();
  currentPage.value = "create-order";
};
const showOrdersPage = () => {
  if (!user.value) return showAuthPage();
  currentPage.value = "orders";
};
const showProfilePage = () => {
  if (!user.value) return showAuthPage();
  currentPage.value = "profile";
};
const showAdminPage = () => {
  if (user.value?.role !== "admin") return;
  currentPage.value = "admin";
};

const handleLoginSuccess = (userData) => {
  user.value = userData;
  localStorage.setItem("user_data", JSON.stringify(userData));
  localStorage.setItem("auth_token", "user_" + userData.id);
  
  showNotification(`Добро пожаловать, ${userData.full_name}!`, 'success');
  showProfilePage();
};

const handleRegisterSuccess = handleLoginSuccess;

const handleLogout = () => {
  localStorage.clear();
  user.value = null;
  showNotification('Вы успешно вышли из системы', 'success');
  showMainPage();
};

const handleOrderCreated = () => {
  showNotification('Заказ успешно создан!', 'success');
  showOrdersPage();
};

const showNotification = (message, type = 'info') => {
  const id = notificationId++;
  notifications.value.push({ id, message, type });
  
  setTimeout(() => {
    removeNotification(id);
  }, 5000);
};

const removeNotification = (id) => {
  notifications.value = notifications.value.filter(n => n.id !== id);
};

onMounted(() => {
  const savedUser = localStorage.getItem("user_data");
  if (savedUser) user.value = JSON.parse(savedUser);
});

defineExpose({ showNotification });
</script>