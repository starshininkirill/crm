<template>
  <BaseLayout>
    <div style="display: flex; gap: 20px;">
      <aside style="width: 200px; background-color: #f5f5f5; padding: 10px;">
        <h3>Админ меню</h3>
        <ul>
          <li><a href="#">Панель управления</a></li>
          <li><a href="#">Пользователи</a></li>
          <li><a href="#">Настройки</a></li>
          <li><a href="#">Отчёты</a></li>
        </ul>
      </aside>
      <section style="flex: 1;">
        <slot />
      </section>
    </div>
  </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';

export default {
  name: "AdminLayout",
  components: { BaseLayout },
};
</script>