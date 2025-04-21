<template>
  <div class="flex flex-col min-h-screen m-0">

    <div class="fixed bottom-5 right-5 space-y-3 z-50">

      <div v-for="(notification, index) in successNotifications" :key="notification.id"
        class="max-w-xs w-full bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-300"
        :class="{ 'opacity-0 translate-y-2': notification.hiding }">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="flex-grow">{{ notification.message }}</span>
      </div>

      <div v-for="(notification, index) in errorNotifications" :key="notification.id"
        class="max-w-xs w-full bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-300"
        :class="{ 'opacity-0 translate-y-2': notification.hiding }">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span class="flex-grow">{{ notification.message }}</span>
      </div>
    </div>

    <Header />
    <main class="h-full grow flex">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script>
import Header from './Header.vue';
import Footer from './Footer.vue';

export default {
  name: "BaseLayout",
  components: { Header, Footer },
  data() {
    return {
      successNotifications: [],
      errorNotifications: [],
      nextId: 1
    };
  },
  watch: {
    '$page.props.success': {
      handler(newMessage) {
        if (newMessage) {
          this.addSuccessNotification(newMessage);
          this.$page.props.success = null;
        }
      },
      immediate: true
    },
    '$page.props.session.flash.error': {
      handler(errorMessage) {
        if (errorMessage) {
          this.addErrorNotification(errorMessage);
          this.$page.props.session.flash.error = null;
        }
      },
      immediate: true
    }
  },
  methods: {
    addSuccessNotification(message) {
      this.addNotification(message, 'success');
    },
    addErrorNotification(message) {
      this.addNotification(message, 'error');
    },
    addNotification(message, type = 'success') {
      const id = this.nextId++;
      const notification = {
        id,
        message,
        type,
        hiding: false
      };

      const targetArray = type === 'success' ? this.successNotifications : this.errorNotifications;
      targetArray.push(notification);

      const timer = setTimeout(() => {
        this.hideNotification(id, type);
      }, 5000);

      notification.timer = timer;
    },
    hideNotification(id, type = 'success') {
      const targetArray = type === 'success' ? this.successNotifications : this.errorNotifications;
      const index = targetArray.findIndex(n => n.id === id);

      if (index !== -1) {
        targetArray[index].hiding = true;

        setTimeout(() => {
          if (type === 'success') {
            this.successNotifications = this.successNotifications.filter(n => n.id !== id);
          } else {
            this.errorNotifications = this.errorNotifications.filter(n => n.id !== id);
          }
        }, 300);
      }
    },
  }
};
</script>