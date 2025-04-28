<template>
  <BaseLayout>
    <div class="flex gap-5 h-auto grow">
      <div class=" w-48 bg-gray-800 text-white flex flex-col">
        <nav class="">
          <ul class="space-y-1 p-4">

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.contract.index') }"
                :href="route('admin.contract.index')">
              Договоры
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.payment.index') }"
                :href="route('admin.payment.index')">
              Платежи
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.organization.index') }"
                :href="route('admin.organization.index')">
              Организации
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.service.index') }"
                :href="route('admin.service.index')">
              Услуги
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.user.index') }" :href="route('admin.user.index')">
              Сотрудники
              </Link>
            </li>
            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.time-check.index') }"
                :href="route('admin.time-check.index')">
              Time Check
              </Link>
            </li>
            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.department.index') }"
                :href="route('admin.department.index')">
              Отделы
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.sale-department.index') }"
                :href="route('admin.sale-department.index')">
              Отдел продаж
              </Link>
            </li>

            <li>
              <Link class="flex items-center p-2 hover:bg-gray-900 rounded"
                :class="{ 'bg-gray-900 text-white': isActive('admin.settings.index') }"
                :href="route('admin.settings.index')">
              Настройки
              </Link>
            </li>

          </ul>
        </nav>

      </div>

      <section style="flex: 1;">
        <slot />
      </section>
    </div>
  </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
  name: "AdminLayout",
  components: { BaseLayout },

  methods: {
    isActive(routeName) {
      const page = usePage();
      const currentRoute = page.props.ziggy.location;
      const targetRoute = route(routeName);

      return currentRoute === targetRoute || currentRoute.startsWith(targetRoute + '/');
    }
  },
};
</script>