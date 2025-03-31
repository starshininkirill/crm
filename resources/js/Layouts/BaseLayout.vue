<template>
    <div class="flex flex-col min-h-screen m-0">
      <div class="fixed bottom-5 right-5 space-y-3 z-50">
        <div 
          v-for="(notification, index) in notifications" 
          :key="notification.id"
          class="max-w-xs w-full bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-300"
          :class="{'opacity-0 translate-y-2': notification.hiding}"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
        notifications: [],
        nextId: 1
      };
    },
    watch: {
      '$page.props.success': {
        handler(newMessage) {
          if (newMessage) {
            this.addNotification(newMessage);
            this.$page.props.success = null;
          }
        },
        immediate: true
      }
    },
    methods: {
      addNotification(message) {
        const id = this.nextId++;
        const notification = {
          id,
          message,
          hiding: false
        };
  
        this.notifications.push(notification);
  
        const timer = setTimeout(() => {
          this.hideNotification(id);
        }, 5000);
  
        notification.timer = timer;
      },
      hideNotification(id) {
        const index = this.notifications.findIndex(n => n.id === id);
        if (index !== -1) {
          this.notifications[index].hiding = true;
          
          setTimeout(() => {
            this.notifications = this.notifications.filter(n => n.id !== id);
          }, 300);
        }
      },
    }
  };
  </script>